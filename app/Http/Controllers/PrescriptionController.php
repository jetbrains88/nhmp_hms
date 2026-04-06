<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\Visit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Prescription::with(['diagnosis.visit.patient', 'medicine']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->whereHas('diagnosis.visit', function ($q) use ($request) {
                $q->where('patient_id', $request->patient_id);
            });
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $prescriptions = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('prescriptions.index', compact('prescriptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'diagnosis_id' => 'required|exists:diagnoses,id',
            'medicine_id' => 'required|exists:medicines,id',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration' => 'required|string|max:100',
            'instructions' => 'nullable|string|max:500',
            'quantity' => 'required|integer|min:1',
            'refills_allowed' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $prescription = Prescription::create(array_merge($validated, [
                'prescribed_by' => Auth::id(),
                'status' => 'active',
            ]));

            // Update diagnosis to have prescription
            $diagnosis = Diagnosis::find($validated['diagnosis_id']);
            $diagnosis->update(['has_prescription' => true]);

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($prescription)
                ->withProperties(['medicine_id' => $validated['medicine_id']])
                ->log('created new prescription');
        });

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription created successfully!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $diagnosisId = $request->diagnosis_id;
        $diagnosis = Diagnosis::with(['visit.patient'])->find($diagnosisId);

        if (!$diagnosis) {
            return redirect()->back()->with('error', 'Diagnosis not found.');
        }

        $medicines = Medicine::where('is_active', true)->orderBy('name')->get();

        return view('prescriptions.create', compact('diagnosis', 'medicines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);

        if (Auth::user()->cannot('update', $prescription)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration' => 'required|string|max:100',
            'instructions' => 'nullable|string|max:500',
            'quantity' => 'required|integer|min:1',
            'refills_allowed' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:active,completed,cancelled,expired',
        ]);

        $prescription->update($validated);

        // Log the update
        activity()
            ->causedBy(Auth::user())
            ->performedOn($prescription)
            ->log('updated prescription');

        return redirect()->route('prescriptions.show', $prescription->id)
            ->with('success', 'Prescription updated successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $prescription = Prescription::with([
            'diagnosis.visit.patient',
            'medicine',
            'prescriber'
        ])->findOrFail($id);

        return view('prescriptions.show', compact('prescription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $prescription = Prescription::with(['diagnosis.visit.patient', 'medicine'])->findOrFail($id);

        // Check if user has permission to edit
        if (Auth::user()->cannot('update', $prescription)) {
            abort(403, 'Unauthorized action.');
        }

        $medicines = Medicine::where('is_active', true)->orderBy('name')->get();

        return view('prescriptions.edit', compact('prescription', 'medicines'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $prescription = Prescription::findOrFail($id);

        if (Auth::user()->cannot('delete', $prescription)) {
            abort(403, 'Unauthorized action.');
        }

        DB::transaction(function () use ($prescription) {
            // Log before deletion
            activity()
                ->causedBy(Auth::user())
                ->performedOn($prescription)
                ->log('deleted prescription');

            $prescription->delete();
        });

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription deleted successfully!');
    }

    /**
     * Print prescription.
     */
    public function print($visitId)
    {
        $visit = Visit::with([
            'patient',
            'vitals',
            'diagnoses.prescriptions.medicine',
            'doctor'
        ])->findOrFail($visitId);

        // Check if visit has prescriptions
        $hasPrescriptions = $visit->diagnoses->flatMap(function ($diagnosis) {
                return $diagnosis->prescriptions;
            })->count() > 0;

        if (!$hasPrescriptions) {
            return redirect()->back()->with('error', 'No prescriptions found for this visit.');
        }

        $data = [
            'visit' => $visit,
            'hospital' => [
                'name' => 'NHMP Healthcare',
                'address' => 'National Health Management Program Headquarters',
                'phone' => '051-1234567',
                'email' => 'info@nhmp-healthcare.gov.pk',
            ],
            'print_date' => now()->format('d M Y, h:i A'),
            'prescription_id' => 'RX-' . str_pad($visit->id, 6, '0', STR_PAD_LEFT),
        ];

        $pdf = Pdf::loadView('prescriptions.print', $data);

        return $pdf->stream('prescription-' . $visit->queue_token . '.pdf');
    }

    /**
     * Generate prescription PDF for download.
     */
    public function download($visitId)
    {
        $visit = Visit::with([
            'patient',
            'vitals',
            'diagnoses.prescriptions.medicine',
            'doctor'
        ])->findOrFail($visitId);

        $hasPrescriptions = $visit->diagnoses->flatMap(function ($diagnosis) {
                return $diagnosis->prescriptions;
            })->count() > 0;

        if (!$hasPrescriptions) {
            return redirect()->back()->with('error', 'No prescriptions found for this visit.');
        }

        $data = [
            'visit' => $visit,
            'hospital' => [
                'name' => 'NHMP Healthcare',
                'address' => 'National Health Management Program Headquarters',
                'phone' => '051-1234567',
                'email' => 'info@nhmp-healthcare.gov.pk',
            ],
            'print_date' => now()->format('d M Y, h:i A'),
            'prescription_id' => 'RX-' . str_pad($visit->id, 6, '0', STR_PAD_LEFT),
        ];

        $pdf = Pdf::loadView('prescriptions.print', $data);

        return $pdf->download('prescription-' . $visit->queue_token . '-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Get prescription details for AJAX.
     */
    public function getDetails($id)
    {
        $prescription = Prescription::with([
            'diagnosis.visit.patient',
            'medicine',
            'prescriber'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'prescription' => [
                'id' => $prescription->id,
                'medicine' => $prescription->medicine->name ?? 'N/A',
                'dosage' => $prescription->dosage,
                'frequency' => $prescription->frequency,
                'duration' => $prescription->duration,
                'instructions' => $prescription->instructions,
                'quantity' => $prescription->quantity,
                'refills_allowed' => $prescription->refills_allowed,
                'status' => $prescription->status,
                'notes' => $prescription->notes,
                'prescribed_by' => $prescription->prescriber->name ?? 'N/A',
                'prescribed_date' => $prescription->created_at->format('d M Y'),
                'patient' => [
                    'name' => $prescription->diagnosis->visit->patient->name ?? 'N/A',
                    'emrn' => $prescription->diagnosis->visit->patient->emrn ?? 'N/A',
                    'age' => $prescription->diagnosis->visit->patient->dob ?
                        \Carbon\Carbon::parse($prescription->diagnosis->visit->patient->dob)->age : 'N/A',
                ],
                'diagnosis' => $prescription->diagnosis->diagnosis ?? 'N/A',
            ]
        ]);
    }

    /**
     * Mark prescription as dispensed.
     */
    public function markDispensed(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);

        if (Auth::user()->cannot('update', $prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'dispensed_quantity' => 'required|integer|min:1|max:' . $prescription->quantity,
            'dispensed_by' => 'nullable|string|max:100',
            'dispense_notes' => 'nullable|string|max:500',
        ]);

        $prescription->update([
            'dispensed_quantity' => $validated['dispensed_quantity'],
            'dispensed_by' => $validated['dispensed_by'] ?? Auth::user()->name,
            'dispensed_at' => now(),
            'dispense_notes' => $validated['dispense_notes'] ?? null,
            'status' => $prescription->quantity == $validated['dispensed_quantity'] ? 'completed' : 'partially_dispensed',
        ]);

        // Log the dispense
        activity()
            ->causedBy(Auth::user())
            ->performedOn($prescription)
            ->withProperties(['dispensed_quantity' => $validated['dispensed_quantity']])
            ->log('dispensed prescription');

        return response()->json([
            'success' => true,
            'message' => 'Prescription marked as dispensed!',
            'prescription' => $prescription->fresh()
        ]);
    }

    /**
     * Refill prescription.
     */
    public function refill(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);

        if (Auth::user()->cannot('update', $prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Check if refills are allowed
        if ($prescription->refills_allowed <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No refills allowed for this prescription.'
            ], 400);
        }

        $validated = $request->validate([
            'refill_quantity' => 'required|integer|min:1|max:100',
            'refill_notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($prescription, $validated) {
            // Create a refill record
            $refill = $prescription->refills()->create([
                'original_prescription_id' => $prescription->id,
                'quantity' => $validated['refill_quantity'],
                'refilled_by' => Auth::id(),
                'refilled_at' => now(),
                'notes' => $validated['refill_notes'],
            ]);

            // Update prescription refill count
            $prescription->decrement('refills_allowed');
            $prescription->increment('quantity', $validated['refill_quantity']);

            if ($prescription->refills_allowed <= 0) {
                $prescription->update(['status' => 'no_refills_left']);
            }

            // Log the refill
            activity()
                ->causedBy(Auth::user())
                ->performedOn($prescription)
                ->withProperties(['refill_quantity' => $validated['refill_quantity']])
                ->log('refilled prescription');
        });

        return response()->json([
            'success' => true,
            'message' => 'Prescription refilled successfully!',
            'prescription' => $prescription->fresh()
        ]);
    }

    /**
     * Get prescription statistics.
     */
    public function statistics(Request $request)
    {
        $query = Prescription::query();

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $total = $query->count();
        $active = $query->where('status', 'active')->count();
        $completed = $query->where('status', 'completed')->count();
        $cancelled = $query->where('status', 'cancelled')->count();
        $expired = $query->where('status', 'expired')->count();

        $mostPrescribed = Medicine::withCount('prescriptions')
            ->orderBy('prescriptions_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($medicine) {
                return [
                    'name' => $medicine->name,
                    'count' => $medicine->prescriptions_count,
                ];
            });

        return response()->json([
            'success' => true,
            'statistics' => [
                'total' => $total,
                'active' => $active,
                'completed' => $completed,
                'cancelled' => $cancelled,
                'expired' => $expired,
                'most_prescribed' => $mostPrescribed,
                'average_prescriptions_per_day' => $this->calculateAveragePrescriptionsPerDay($request),
            ]
        ]);
    }

    /**
     * Calculate average prescriptions per day.
     */
    private function calculateAveragePrescriptionsPerDay($request)
    {
        $fromDate = $request->from_date ?? now()->subDays(30)->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $days = \Carbon\Carbon::parse($fromDate)->diffInDays($toDate) + 1;
        $totalPrescriptions = Prescription::whereBetween('created_at', [$fromDate, $toDate])->count();

        return $days > 0 ? round($totalPrescriptions / $days, 2) : 0;
    }

    /**
     * Search prescriptions.
     */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $prescriptions = Prescription::with([
            'diagnosis.visit.patient',
            'medicine'
        ])
            ->where(function ($query) use ($search) {
                $query->whereHas('diagnosis.visit.patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('emrn', 'like', "%{$search}%");
                })
                    ->orWhereHas('medicine', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('generic_name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'prescriptions' => $prescriptions
        ]);
    }

    /**
     * Bulk update prescription status.
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'prescription_ids' => 'required|array',
            'prescription_ids.*' => 'exists:prescriptions,id',
            'status' => 'required|in:active,completed,cancelled,expired',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            Prescription::whereIn('id', $validated['prescription_ids'])
                ->update([
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                    'updated_at' => now(),
                ]);

            // Log bulk update
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'count' => count($validated['prescription_ids']),
                    'status' => $validated['status']
                ])
                ->log('bulk updated prescriptions');
        });

        return response()->json([
            'success' => true,
            'message' => count($validated['prescription_ids']) . ' prescriptions updated successfully!'
        ]);
    }

    /**
     * Export prescriptions to CSV.
     */
    public function export(Request $request)
    {
        $query = Prescription::with(['diagnosis.visit.patient', 'medicine', 'prescriber']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $prescriptions = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $csvData[] = [
            'Prescription ID',
            'Patient Name',
            'EMRN',
            'Medicine',
            'Dosage',
            'Frequency',
            'Duration',
            'Quantity',
            'Status',
            'Prescribed By',
            'Prescribed Date',
            'Diagnosis',
            'Instructions',
            'Notes'
        ];

        foreach ($prescriptions as $prescription) {
            $csvData[] = [
                'RX-' . str_pad($prescription->id, 6, '0', STR_PAD_LEFT),
                $prescription->diagnosis->visit->patient->name ?? 'N/A',
                $prescription->diagnosis->visit->patient->emrn ?? 'N/A',
                $prescription->medicine->name ?? 'N/A',
                $prescription->dosage,
                $prescription->frequency,
                $prescription->duration,
                $prescription->quantity,
                ucfirst($prescription->status),
                $prescription->prescriber->name ?? 'N/A',
                $prescription->created_at->format('d M Y'),
                $prescription->diagnosis->diagnosis ?? 'N/A',
                $prescription->instructions ?? 'N/A',
                $prescription->notes ?? 'N/A'
            ];
        }

        $filename = 'prescriptions-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
