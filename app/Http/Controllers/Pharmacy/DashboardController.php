<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\StockAlert;
use App\Models\MedicineBatch;
use App\Services\PrescriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    /**
     * Display pharmacy dashboard.
     */
    public function index()
    {
        $branchId = session('current_branch_id');

        $stats = [
            'total_prescriptions' => Prescription::where('branch_id', $branchId)->count(),
            'pending_prescriptions' => Prescription::where('branch_id', $branchId)
                ->whereIn('status', ['pending', 'partially_dispensed'])->count(),
            'dispensed_today' => Prescription::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->whereDate('updated_at', today())->count(),
            'total_medicines' => Medicine::where('branch_id', $branchId)->count(),
            'total_stock_value' => MedicineBatch::where('branch_id', $branchId)
                ->sum(DB::raw('remaining_quantity * unit_price')),
            'low_stock_items' => StockAlert::where('branch_id', $branchId)
                ->where('alert_type', 'low_stock')
                ->where('is_resolved', false)
                ->count(),
        ];

        $alerts = [
            'low_stock' => Medicine::withSum(['batches as total_stock' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }], 'remaining_quantity')
                ->havingRaw('total_stock <= reorder_level')
                ->take(10)
                ->get()
                ->map(function($medicine) {
                    $medicine->stock = $medicine->total_stock;
                    return $medicine;
                }),
            'expiring_soon' => MedicineBatch::with('medicine')
                ->where('branch_id', $branchId)
                ->where('remaining_quantity', '>', 0)
                ->where('expiry_date', '<=', now()->addDays(30))
                ->orderBy('expiry_date')
                ->take(10)
                ->get()
                ->map(function($batch) {
                    $batch->name = $batch->medicine->name;
                    $batch->stock = $batch->remaining_quantity;
                    return $batch;
                }),
        ];

        $recentDispenses = Prescription::with(['patient', 'medicine', 'diagnosis.visit.patient'])
            ->where('branch_id', $branchId)
            ->whereIn('status', ['completed'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('pharmacy.dashboard', compact('stats', 'recentDispenses', 'alerts'));
    }

    /**
     * Get pharmacy statistics for AJAX.
     */
    public function getStats()
    {
        $branchId = session('current_branch_id');

        return response()->json([
            'pending' => Prescription::where('branch_id', $branchId)
                ->where('status', 'pending')
                ->count(),
            'partially_dispensed' => Prescription::where('branch_id', $branchId)
                ->where('status', 'partially_dispensed')
                ->count(),
            'completed_today' => Prescription::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count(),
        ]);
    }
}
