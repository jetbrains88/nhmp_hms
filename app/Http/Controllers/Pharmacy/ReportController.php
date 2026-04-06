<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicineCategory;
use App\Models\InventoryLog;
use App\Models\StockAlert;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display reports dashboard.
     */
    public function index()
    {
        $branchId = session('current_branch_id');

        // Quick stats for reports dashboard
        $metrics = [
            'total_medicines' => Medicine::where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhere('is_global', true);
            })->count(),
            'total_categories' => MedicineCategory::count(), // Added this
            'total_batches' => MedicineBatch::where('branch_id', $branchId)->count(),
            'total_prescriptions' => Prescription::where('branch_id', $branchId)->count(),
            'prescriptions_this_month' => Prescription::where('branch_id', $branchId)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'low_stock_count' => StockAlert::where('branch_id', $branchId)
                ->where('alert_type', 'low_stock')
                ->where('is_resolved', false)
                ->count(),
            'out_of_stock_count' => Medicine::whereHas('batches', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }, '=', 0)->count(),
            'total_value' => MedicineBatch::where('branch_id', $branchId)
                ->sum(DB::raw('remaining_quantity * unit_price')),
            'avg_stock_turnover' => 0, // Placeholder
            'total_dispenses' => Prescription::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'monthly_revenue' => 0, // Placeholder
        ];

        // Monthly prescription data for chart
        $monthlyData = Prescription::where('branch_id', $branchId)
            ->whereYear('created_at', now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top medicines
        $topMedicines = Prescription::where('branch_id', $branchId)
            ->select('medicine_id', DB::raw('COUNT(*) as total'))
            ->with('medicine:id,name')
            ->groupBy('medicine_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Low stock medicines for display
        $lowStock = Medicine::withSum(['batches as total_stock' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }], 'remaining_quantity')
            ->havingRaw('total_stock <= reorder_level')
            ->take(5)
            ->get();

        // Expiring soon for display
        $expiringSoon = MedicineBatch::with('medicine')
            ->where('branch_id', $branchId)
            ->where('remaining_quantity', '>', 0)
            ->where('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->take(5)
            ->get();

        return view('pharmacy.reports.index', compact('metrics', 'monthlyData', 'topMedicines', 'lowStock', 'expiringSoon'));
    }

    /**
     * Generate prescription report.
     */
    public function prescriptions(Request $request)
    {
        $branchId = session('current_branch_id');

        $query = Prescription::with(['diagnosis.visit.patient', 'medicine', 'prescribedBy', 'dispensations'])
            ->where('branch_id', $branchId);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        $prescriptions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $medicines = Medicine::where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)->orWhere('is_global', true);
        })->orderBy('name')->get();

        return view('pharmacy.reports.prescriptions', compact('prescriptions', 'medicines'));
    }

    /**
     * Generate inventory report.
     */
    public function inventory(Request $request)
    {
        $branchId = session('current_branch_id');

        $query = MedicineBatch::with(['medicine.category'])
            ->where('branch_id', $branchId);

        // Apply filters
        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        if ($request->filled('expiring_soon')) {
            $query->where('expiry_date', '<=', now()->addDays(30));
        }

        if ($request->filled('low_stock')) {
            $query->whereRaw('remaining_quantity <= (SELECT reorder_level FROM medicines WHERE id = medicine_batches.medicine_id)');
        }

        $batches = $query->orderBy('expiry_date')->paginate(20);

        // Calculate inventory value
        $totalValue = $batches->sum(function ($batch) {
            return $batch->remaining_quantity * $batch->unit_price;
        });

        $totalSaleValue = $batches->sum(function ($batch) {
            return $batch->remaining_quantity * ($batch->sale_price ?? $batch->unit_price);
        });

        return view('pharmacy.reports.inventory', compact('batches', 'totalValue', 'totalSaleValue'));
    }

    /**
     * Generate stock movement report.
     */
    public function movements(Request $request)
    {
        $branchId = session('current_branch_id');

        $query = InventoryLog::with(['medicine', 'user', 'medicineBatch'])
            ->where('branch_id', $branchId);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Summary by type
        $summary = InventoryLog::where('branch_id', $branchId)
            ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('type')
            ->get();

        return view('pharmacy.reports.movements', compact('logs', 'summary'));
    }

    /**
     * Generate expiry report.
     */
    public function expiry(Request $request)
    {
        $branchId = session('current_branch_id');
        $days = $request->get('days', 90);

        $batches = MedicineBatch::with(['medicine.category'])
            ->where('branch_id', $branchId)
            ->where('remaining_quantity', '>', 0)
            ->where('expiry_date', '<=', now()->addDays($days))
            ->orderBy('expiry_date')
            ->get();

        $grouped = $batches->groupBy(function ($batch) {
            if ($batch->expiry_date <= now()) {
                return 'expired';
            } elseif ($batch->expiry_date <= now()->addDays(30)) {
                return 'critical';
            } elseif ($batch->expiry_date <= now()->addDays(90)) {
                return 'warning';
            }
            return 'normal';
        });

        return view('pharmacy.reports.expiry', compact('grouped', 'days'));
    }

    /**
     * Export report to CSV.
     */
    public function export(Request $request, $type)
    {
        $branchId = session('current_branch_id');
        $data = [];
        $headers = [];

        switch ($type) {
            case 'prescriptions':
                $data = $this->getPrescriptionsExportData($branchId, $request);
                $headers = ['Date', 'Patient', 'Medicine', 'Dosage', 'Quantity', 'Status', 'Prescribed By'];
                break;

            case 'inventory':
                $data = $this->getInventoryExportData($branchId);
                $headers = ['Medicine', 'Batch', 'Expiry Date', 'Stock', 'Unit Price', 'Total Value'];
                break;

            case 'movements':
                $data = $this->getMovementsExportData($branchId, $request);
                $headers = ['Date', 'Type', 'Medicine', 'Batch', 'Quantity', 'Previous', 'New', 'User'];
                break;

            case 'expiry':
                $data = $this->getExpiryExportData($branchId, $request);
                $headers = ['Medicine', 'Batch', 'Expiry Date', 'Stock', 'Status', 'Days Left'];
                break;

            default:
                return redirect()->back()->with('error', 'Invalid report type.');
        }

        $csv = $this->reportService->toCsv($data, $headers);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $type . '-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Get prescriptions export data.
     */
    protected function getPrescriptionsExportData($branchId, $request)
    {
        $query = Prescription::with(['diagnosis.visit.patient', 'medicine', 'prescribedBy'])
            ->where('branch_id', $branchId);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $prescriptions = $query->orderBy('created_at', 'desc')->get();

        return $prescriptions->map(function ($p) {
            return [
                $p->created_at->format('Y-m-d H:i'),
                $p->diagnosis->visit->patient->name ?? 'N/A',
                $p->medicine->name ?? 'N/A',
                $p->dosage,
                $p->quantity,
                $p->status,
                $p->prescribedBy->name ?? 'N/A',
            ];
        })->toArray();
    }

    /**
     * Get inventory export data.
     */
    protected function getInventoryExportData($branchId)
    {
        $batches = MedicineBatch::with(['medicine'])
            ->where('branch_id', $branchId)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        return $batches->map(function ($batch) {
            return [
                $batch->medicine->name ?? 'N/A',
                $batch->batch_number,
                $batch->expiry_date->format('Y-m-d'),
                $batch->remaining_quantity,
                $batch->unit_price,
                $batch->remaining_quantity * $batch->unit_price,
            ];
        })->toArray();
    }

    /**
     * Get movements export data.
     */
    protected function getMovementsExportData($branchId, $request)
    {
        $query = InventoryLog::with(['medicine', 'user', 'medicineBatch'])
            ->where('branch_id', $branchId);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        return $logs->map(function ($log) {
            return [
                $log->created_at->format('Y-m-d H:i'),
                $log->type,
                $log->medicine->name ?? 'N/A',
                $log->medicineBatch->batch_number ?? 'N/A',
                $log->quantity,
                $log->previous_stock,
                $log->new_stock,
                $log->user->name ?? 'N/A',
            ];
        })->toArray();
    }

    /**
     * Get expiry export data.
     */
    protected function getExpiryExportData($branchId, $request)
    {
        $days = $request->get('days', 90);

        $batches = MedicineBatch::with(['medicine'])
            ->where('branch_id', $branchId)
            ->where('remaining_quantity', '>', 0)
            ->where('expiry_date', '<=', now()->addDays($days))
            ->orderBy('expiry_date')
            ->get();

        return $batches->map(function ($batch) {
            $daysLeft = now()->diffInDays($batch->expiry_date, false);

            $status = 'normal';
            if ($daysLeft <= 0) {
                $status = 'Expired';
            } elseif ($daysLeft <= 30) {
                $status = 'Critical';
            } elseif ($daysLeft <= 90) {
                $status = 'Warning';
            }

            return [
                $batch->medicine->name ?? 'N/A',
                $batch->batch_number,
                $batch->expiry_date->format('Y-m-d'),
                $batch->remaining_quantity,
                $status,
                $daysLeft,
            ];
        })->toArray();
    }
}
