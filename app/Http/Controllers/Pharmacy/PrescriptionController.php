<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pharmacy\DispenseRequest;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineForm;
use App\Models\User;
use App\Models\PrescriptionDispensation;
use App\Services\PrescriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    protected $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    /**
     * Display prescriptions index page (or return JSON when AJAX).
     */
    public function index(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;

        $query = Prescription::with([
            'diagnosis.visit.patient',
            'medicine.batches' => fn ($q) => $q->where('remaining_quantity', '>', 0)->where('is_active', true),
            'prescribedBy',
            'dispensations',
        ])->where('branch_id', $branchId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['pending', 'partially_dispensed']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('diagnosis.visit.patient', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('emrn', 'LIKE', "%{$search}%");
            });
        }

        $prescriptions = $query->orderBy('created_at', 'asc')->get();
        $stats = $this->prescriptionService->getStats($branchId);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            $grouped = $prescriptions->groupBy(function ($p) {
                return $p->diagnosis?->visit?->patient?->id ?? 0;
            })->map(function ($items) {
                $patient = $items->first()->diagnosis?->visit?->patient;
                return [
                    'patient' => [
                        'id'     => $patient?->id,
                        'name'   => $patient?->name ?? 'Unknown',
                        'emrn'   => $patient?->emrn ?? '',
                        'gender' => $patient?->gender ?? '',
                    ],
                    'prescriptions' => $items->map(fn ($p) => $this->formatPrescription($p))->values(),
                ];
            })->values();

            return response()->json(['patients' => $grouped, 'stats' => $stats]);
        }

        return view('pharmacy.prescriptions.index', compact('stats'));
    }

    /**
     * Format a prescription for JSON output.
     */
    private function formatPrescription(Prescription $p): array
    {
        $totalDispensed = $p->dispensations->sum('quantity_dispensed');
        $remaining      = max(0, $p->quantity - $totalDispensed);
        $stock          = $p->medicine?->stock ?? 0;

        return [
            'id'            => $p->id,
            'medicine_id'   => $p->medicine_id,
            'medicine_name' => $p->medicine?->name ?? 'N/A',
            'generic_name'  => $p->medicine?->generic_name ?? '',
            'dosage'        => $p->dosage,
            'morning'       => $p->morning,
            'evening'       => $p->evening,
            'night'         => $p->night,
            'days'          => $p->days,
            'quantity'      => $p->quantity,
            'dispensed_qty' => $totalDispensed,
            'remaining_qty' => $remaining,
            'status'        => $p->status,
            'instructions'  => $p->instructions,
            'stock'         => $stock,
            'stock_ok'      => $stock >= $remaining,
            'doctor_name'   => $p->prescribedBy?->name ?? '',
            'created_at'    => $p->created_at?->format('d M Y'),
            'batches'       => $p->medicine?->batches?->map(fn ($b) => [
                'id'           => $b->id,
                'batch_number' => $b->batch_number,
                'remaining'    => $b->remaining_quantity,
                'expiry'       => Carbon::parse($b->expiry_date)->format('M Y'),
            ])->values() ?? [],
        ];
    }

    /**
     * Return alternative medicines with the same generic name (for substitution).
     */
    public function alternativeMedicines(Prescription $prescription)
    {
        $genericName = $prescription->medicine?->generic_name;

        if (!$genericName) {
            return response()->json(['alternatives' => []]);
        }

        $branchId = auth()->user()->current_branch_id;

        $alternatives = Medicine::where('generic_name', $genericName)
            ->where('id', '!=', $prescription->medicine_id)
            ->where('is_active', true)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhere('is_global', true);
            })
            ->get()
            ->map(fn ($m) => [
                'id'      => $m->id,
                'name'    => $m->name,
                'brand'   => $m->brand,
                'stock'   => $m->stock,
                'generic' => $m->generic_name,
            ]);

        return response()->json(['alternatives' => $alternatives]);
    }

    /**
     * Show prescription details.
     */
    public function show(Prescription $prescription)
    {
        $prescription->load([
            'diagnosis.visit.patient',
            'medicine',
            'prescribedBy',
            'dispensations' => function ($q) {
                $q->with(['dispensedBy', 'medicineBatch', 'alternativeMedicine'])->latest();
            },
        ]);

        return view('pharmacy.prescriptions.show', compact('prescription'));
    }

    /**
     * Dispense prescription.
     */
    public function dispense(DispenseRequest $request, Prescription $prescription)
    {
        try {
            $dispensation = $this->prescriptionService->dispensePrescription(
                $prescription,
                auth()->id(),
                $request->validated()
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Dispensed {$dispensation->quantity_dispensed} units successfully.",
                    'prescription_status' => $prescription->fresh()->status,
                    'dispensed_qty' => $dispensation->quantity_dispensed,
                ]);
            }

            return redirect()
                ->route('pharmacy.prescriptions.show', $prescription)
                ->with('success', "Dispensed {$dispensation->quantity_dispensed} units successfully");

        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display dispense history.
     */
    public function history(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;

        $stats = [
            'total_dispensed'  => PrescriptionDispensation::whereHas('prescription', fn ($q) => $q->where('branch_id', $branchId))->count(),
            'today_dispensed'  => PrescriptionDispensation::whereHas('prescription', fn ($q) => $q->where('branch_id', $branchId))->whereDate('dispensed_at', today())->count(),
            'total_quantity'   => PrescriptionDispensation::whereHas('prescription', fn ($q) => $q->where('branch_id', $branchId))->sum('quantity_dispensed'),
            'unique_patients'  => PrescriptionDispensation::whereHas('prescription', fn ($q) => $q->where('branch_id', $branchId))->distinct('prescription_id')->count('prescription_id'),
        ];

        $medicineCategories = MedicineCategory::where('is_active', true)->orderBy('name')->get();
        $medicines = Medicine::where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)->orWhere('is_global', true);
        })->where('is_active', true)->orderBy('name')->get();
        $medicineForms = MedicineForm::orderBy('name')->get();
        $pharmacists = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['pharmacy', 'pharmacist', 'admin']))
            ->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('pharmacy.prescriptions.history', compact(
            'stats', 'medicineCategories', 'medicines', 'medicineForms', 'pharmacists'
        ));
    }

    /**
     * Handle filtered history data (AJAX).
     */
    public function getHistoryData(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;

        $query = PrescriptionDispensation::with([
            'prescription.diagnosis.visit.patient',
            'prescription.medicine.category',
            'prescription.medicine.form',
            'dispensedBy',
            'medicineBatch',
            'alternativeMedicine',
        ])->whereHas('prescription', fn ($q) => $q->where('branch_id', $branchId));

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('prescription.diagnosis.visit.patient', fn ($pq) => $pq->where('name', 'LIKE', "%{$search}%")->orWhere('emrn', 'LIKE', "%{$search}%"))
                  ->orWhereHas('prescription.medicine', fn ($mq) => $mq->where('name', 'LIKE', "%{$search}%")->orWhere('generic_name', 'LIKE', "%{$search}%"));
            });
        }

        if ($request->filled('date_from')) $query->whereDate('dispensed_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('dispensed_at', '<=', $request->date_to);
        if ($request->filled('medicine_category_id')) {
            $query->whereHas('prescription.medicine', fn ($q) => $q->where('category_id', $request->medicine_category_id));
        }
        if ($request->filled('medicine_id')) {
            $query->whereHas('prescription', fn ($q) => $q->where('medicine_id', $request->medicine_id));
        }
        if ($request->filled('dispensed_by')) $query->where('dispensed_by', $request->dispensed_by);
        if ($request->filled('min_quantity')) $query->where('quantity_dispensed', '>=', $request->min_quantity);
        if ($request->filled('max_quantity')) $query->where('quantity_dispensed', '<=', $request->max_quantity);

        $sortField     = $request->get('sort', 'dispensed_at');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortField === 'medicine_name') {
            $query->leftJoin('prescriptions', 'prescription_dispensations.prescription_id', '=', 'prescriptions.id')
                  ->leftJoin('medicines as m1', 'prescriptions.medicine_id', '=', 'm1.id')
                  ->leftJoin('medicines as m2', 'prescription_dispensations.alternative_medicine_id', '=', 'm2.id')
                  ->select('prescription_dispensations.*')
                  ->addSelect(\DB::raw('COALESCE(m2.name, m1.name) as actual_medicine_name'))
                  ->orderBy('actual_medicine_name', $sortDirection);
        } elseif (in_array($sortField, ['id', 'dispensed_at', 'quantity_dispensed'])) {
            $query->orderBy('prescription_dispensations.' . $sortField, $sortDirection);
        } else {
            $query->orderBy('prescription_dispensations.dispensed_at', 'desc');
        }

        $dispensations = $query->paginate($request->get('per_page', 10));

        return response()->json($dispensations);
    }

    /**
     * Print prescription label.
     */
    public function printLabel(Prescription $prescription)
    {
        return view('pharmacy.prescriptions.label', compact('prescription'));
    }
}