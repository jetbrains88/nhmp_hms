<?php

namespace App\Services;

use App\Models\Visit;
use App\Models\Diagnosis;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class DoctorService
{
    public function completeConsultation(array $data, Visit $visit): array
    {
        return DB::transaction(function () use ($data, $visit) {
            // Create Diagnosis
            $diagnosis = Diagnosis::create([
                'visit_id' => $visit->id,
                'doctor_id' => auth()->id(),
                'symptoms' => $data['symptoms'] ?? null,
                'diagnosis' => $data['diagnosis'] ?? null,
                'doctor_notes' => $data['doctor_notes'],
                'recommendations' => $data['recommendations'] ?? null,
                'followup_date' => $data['followup_date'] ?? null,
                'severity' => $data['severity'],
            ]);

            // Create Prescriptions
            $prescriptions = [];
            if (!empty($data['prescriptions'])) {
                foreach ($data['prescriptions'] as $prescriptionData) {
                    $prescription = Prescription::create([
                        'diagnosis_id' => $diagnosis->id,
                        'medicine_id' => $prescriptionData['medicine_id'],
                        'dosage' => $prescriptionData['dosage'],
                        'frequency' => $prescriptionData['frequency'],
                        'duration' => $prescriptionData['duration'],
                        'quantity' => $prescriptionData['quantity'],
                        'instructions' => $prescriptionData['instructions'] ?? null,
                        'status' => 'pending',
                    ]);
                    $prescriptions[] = $prescription;
                }
            }

            // Update Visit Status
            $visit->update(['status' => 'completed']);

            return [
                'diagnosis' => $diagnosis,
                'prescriptions' => $prescriptions,
            ];
        });
    }

    public function getDoctorDashboardData()
    {
        $waitingPatients = Visit::with('patient')
            ->where('status', 'waiting')
            ->orderBy('created_at')
            ->get();

        $inProgressVisits = Visit::with('patient')
            ->where('status', 'in_progress')
            ->where('doctor_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();

        $todaysCompleted = Visit::with(['patient', 'diagnosis'])
            ->where('status', 'completed')
            ->whereDate('updated_at', today())
            ->where('doctor_id', auth()->id())
            ->count();

        return [
            'waitingPatients' => $waitingPatients,
            'inProgressVisits' => $inProgressVisits,
            'todaysCompleted' => $todaysCompleted,
            'totalWaiting' => $waitingPatients->count(),
        ];
    }
}