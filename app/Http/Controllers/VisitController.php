<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Visit;
use App\Models\Vital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $visit = Visit::with([
            'patient',
            'latestVital',
            'diagnoses',
            'diagnoses.prescriptions.medicine',
            'doctor'
        ])->findOrFail($id);

        return view('visits.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $visit = Visit::with(['patient', 'latestVital', 'diagnoses'])->findOrFail($id);

        // Check if user has permission to edit
        if (Auth::user()->cannot('update', $visit)) {
            abort(403, 'Unauthorized action.');
        }

        return view('visits.edit', compact('visit'));
    }

    /**
     * Mark a visit as completed.
     */
    public function complete(Request $request, $id)
    {
        $visit = Visit::findOrFail($id);

        // Check if user has permission to complete
        if (Auth::user()->cannot('update', $visit)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        DB::transaction(function () use ($visit) {
            $visit->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Log the completion
            activity()
                ->causedBy(Auth::user())
                ->performedOn($visit)
                ->log('marked visit as completed');
        });

        return response()->json([
            'success' => true,
            'message' => 'Visit marked as completed!',
            'visit' => $visit->fresh()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $visit = Visit::findOrFail($id);

        // Check if user has permission to update
        if (Auth::user()->cannot('update', $visit)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:waiting,in_progress,completed,cancelled',
            'visit_type' => 'required|string|max:100',
            // 'weight' => 'nullable|numeric|min:1|max:300',
            // 'bp' => 'nullable|string|max:20',
            // 'temp' => 'nullable|numeric|between:35,42',
            // 'pulse' => 'nullable|integer|between:40,180',
            'notes' => 'nullable|string|max:1000',
        ]);

        $visit->update($validated);

        return redirect()->route('visits.show', $visit->id)
            ->with('success', 'Visit updated successfully!');
    }

    /**
     * Cancel a visit.
     */
    public function cancel(Request $request, $id)
    {
        $visit = Visit::findOrFail($id);

        // Check if user has permission to cancel
        if (Auth::user()->cannot('delete', $visit)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }


        DB::transaction(function () use ($visit) {
            $visit->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Log the cancellation
            activity()
                ->causedBy(Auth::user())
                ->performedOn($visit)
                ->log('cancelled visit');
        });

        return response()->json([
            'success' => true,
            'message' => 'Visit cancelled successfully!',
            'visit' => $visit->fresh()
        ]);
    }

    /**
     * Get visit details for AJAX requests.
     */
    public function getDetails($id)
    {
        $visit = Visit::with([
            'patient',
            'latestVital',
            'diagnoses',
            'diagnoses.prescriptions.medicine',
            'doctor'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'visit' => [
                'id' => $visit->id,
                'queue_token' => $visit->queue_token,
                'status' => $visit->status,
                'visit_type' => $visit->visit_type,
                'created_at' => $visit->created_at->format('d M Y, h:i A'),
                'patient' => [
                    'name' => $visit->patient->name,
                    'emrn' => $visit->patient->emrn,
                    'age' => $visit->patient->dob ? \Carbon\Carbon::parse($visit->patient->dob)->age : 'N/A',
                    'gender' => $visit->patient->gender,
                    'blood_group' => $visit->patient->blood_group,
                ],
                'vitals' => $visit->latestVital ? [
                    'temperature' => $visit->latestVital->temperature,
                    'pulse' => $visit->latestVital->pulse,
                    'blood_pressure' => $visit->latestVital->blood_pressure_systolic . '/' . $visit->latestVital->blood_pressure_diastolic,
                    'oxygen_saturation' => $visit->latestVital->oxygen_saturation,
                    'respiratory_rate' => $visit->latestVital->respiratory_rate,
                    'weight' => $visit->latestVital->weight,
                    'height' => $visit->latestVital->height,
                    'bmi' => $visit->latestVital->bmi,
                    'notes' => $visit->latestVital->notes,
                ] : null,
                'diagnoses' => $visit->diagnoses->map(function ($diagnosis) {
                    return [
                        'id' => $diagnosis->id,
                        'diagnosis' => $diagnosis->diagnosis,
                        'description' => $diagnosis->description,
                        'prescriptions' => $diagnosis->prescriptions->map(function ($prescription) {
                            return [
                                'medicine_name' => $prescription->medicine->name ?? 'N/A',
                                'dosage' => $prescription->dosage,
                                'frequency' => $prescription->frequency,
                                'duration' => $prescription->duration,
                                'instructions' => $prescription->instructions,
                            ];
                        })
                    ];
                })
            ]
        ]);
    }

    /**
     * Get all visits for a patient.
     */
    public function getPatientVisits($patientId)
    {
        $visits = Visit::with(['latestVital', 'doctor'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'visits' => $visits->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'queue_token' => $visit->queue_token,
                    'status' => $visit->status,
                    'visit_type' => $visit->visit_type,
                    'created_at' => $visit->created_at->format('d M Y, h:i A'),
                    'doctor' => $visit->doctor ? $visit->doctor->name : 'N/A',
                    'vitals' => $visit->latestVital ? [
                        'bp' => $visit->latestVital->blood_pressure_systolic . '/' . $visit->latestVital->blood_pressure_diastolic,
                        'temp' => $visit->latestVital->temperature,
                        'pulse' => $visit->latestVital->pulse,
                    ] : null
                ];
            })
        ]);
    }

    /**
     * Add diagnosis to visit.
     */
    public function addDiagnosis(Request $request, $visitId)
    {
        $visit = Visit::findOrFail($visitId);

        if (Auth::user()->cannot('update', $visit)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'diagnosis' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icd_code' => 'nullable|string|max:20',
            'severity' => 'nullable|in:mild,moderate,severe,critical',
        ]);

        $diagnosis = Diagnosis::create(array_merge($validated, [
            'visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'doctor_id' => Auth::id(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Diagnosis added successfully!',
            'diagnosis' => $diagnosis
        ]);
    }

    /**
     * Update vitals for a visit.
     */
    public function updateVitals(Request $request, $visitId)
    {
        $visit = Visit::findOrFail($visitId);

        if (Auth::user()->cannot('update', $visit)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'temperature' => 'required|numeric|between:35,42',
            'pulse' => 'required|integer|between:40,180',
            'blood_pressure_systolic' => 'required|integer|between:70,250',
            'blood_pressure_diastolic' => 'required|integer|between:40,150',
            'oxygen_saturation' => 'nullable|integer|between:70,100',
            'respiratory_rate' => 'nullable|integer|between:8,40',
            'weight' => 'nullable|numeric|min:1|max:300',
            'height' => 'nullable|numeric|min:30|max:250',
            'pain_scale' => 'nullable|integer|between:0,10',
            'blood_glucose' => 'nullable|numeric|between:30,500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Calculate BMI
        $bmi = null;
        if (isset($validated['weight']) && isset($validated['height']) && $validated['height'] > 0) {
            $heightInMeters = $validated['height'] / 100;
            $bmi = round($validated['weight'] / ($heightInMeters * $heightInMeters), 1);
        }

        $vital = Vital::updateOrCreate(
            ['visit_id' => $visit->id],
            array_merge($validated, [
                'bmi' => $bmi,
                'patient_id' => $visit->patient_id,
                'staff_id' => Auth::id(),
                'date_time' => now(),
            ])
        );

        return response()->json([
            'success' => true,
            'message' => 'Vitals updated successfully!',
            'vitals' => $vital
        ]);
    }

    /**
     * Get visit statistics.
     */
    public function statistics()
    {
        $today = now()->format('Y-m-d');

        $stats = [
            'total_visits_today' => Visit::whereDate('created_at', $today)->count(),
            'waiting_visits' => Visit::where('status', 'waiting')->count(),
            'in_progress_visits' => Visit::where('status', 'in_progress')->count(),
            'completed_visits_today' => Visit::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->count(),
            'cancelled_visits_today' => Visit::whereDate('created_at', $today)
                ->where('status', 'cancelled')
                ->count(),
            'average_waiting_time' => $this->calculateAverageWaitingTime(),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Calculate average waiting time for visits.
     */
    private function calculateAverageWaitingTime()
    {
        $completedVisits = Visit::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedVisits->isEmpty()) {
            return '0 minutes';
        }

        $totalMinutes = $completedVisits->sum(function ($visit) {
            return $visit->created_at->diffInMinutes($visit->completed_at);
        });

        $averageMinutes = round($totalMinutes / $completedVisits->count());

        if ($averageMinutes < 60) {
            return $averageMinutes . ' minutes';
        } else {
            $hours = floor($averageMinutes / 60);
            $minutes = $averageMinutes % 60;
            return $hours . 'h ' . $minutes . 'm';
        }
    }

    /**
     * Search visits.
     */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $visits = Visit::with(['patient', 'doctor'])
            ->where(function ($query) use ($search) {
                $query->where('queue_token', 'like', "%{$search}%")
                    ->orWhere('visit_type', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('emrn', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'visits' => $visits
        ]);
    }

    /**
     * Export visit data.
     */
    public function export($id)
    {
        $visit = Visit::with([
            'patient',
            'latestVital',
            'diagnoses',
            'diagnoses.prescriptions.medicine',
            'doctor'
        ])->findOrFail($id);

        $data = [
            'visit_id' => $visit->id,
            'queue_token' => $visit->queue_token,
            'date' => $visit->created_at->format('d M Y'),
            'time' => $visit->created_at->format('h:i A'),
            'status' => $visit->status,
            'visit_type' => $visit->visit_type,
            'patient' => [
                'name' => $visit->patient->name,
                'emrn' => $visit->patient->emrn,
                'dob' => $visit->patient->dob ? $visit->patient->dob->format('d M Y') : 'N/A',
                'age' => $visit->patient->dob ? \Carbon\Carbon::parse($visit->patient->dob)->age : 'N/A',
                'gender' => $visit->patient->gender,
                'blood_group' => $visit->patient->blood_group,
                'phone' => $visit->patient->phone,
                'address' => $visit->patient->address,
            ],
            'vitals' => $visit->latestVital ? [
                'temperature' => $visit->latestVital->temperature,
                'pulse' => $visit->latestVital->pulse,
                'blood_pressure' => $visit->latestVital->blood_pressure_systolic . '/' . $visit->latestVital->blood_pressure_diastolic,
                'oxygen_saturation' => $visit->latestVital->oxygen_saturation,
                'respiratory_rate' => $visit->latestVital->respiratory_rate,
                'weight' => $visit->latestVital->weight,
                'height' => $visit->latestVital->height,
                'bmi' => $visit->latestVital->bmi,
                'blood_glucose' => $visit->latestVital->blood_glucose,
                'pain_scale' => $visit->latestVital->pain_scale,
                'notes' => $visit->latestVital->notes,
            ] : null,
            'diagnoses' => $visit->diagnoses->map(function ($diagnosis) {
                return [
                    'diagnosis' => $diagnosis->diagnosis,
                    'description' => $diagnosis->description,
                    'icd_code' => $diagnosis->icd_code,
                    'severity' => $diagnosis->severity,
                    'prescriptions' => $diagnosis->prescriptions->map(function ($prescription) {
                        return [
                            'medicine' => $prescription->medicine->name ?? 'N/A',
                            'dosage' => $prescription->dosage,
                            'frequency' => $prescription->frequency,
                            'duration' => $prescription->duration,
                            'instructions' => $prescription->instructions,
                            'quantity' => $prescription->quantity,
                        ];
                    })
                ];
            }),
            'doctor' => $visit->doctor ? [
                'name' => $visit->doctor->name,
                'qualification' => $visit->doctor->qualification,
                'specialization' => $visit->doctor->specialization,
            ] : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'exported_at' => now()->format('d M Y, h:i A')
        ]);
    }
}
