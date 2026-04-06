<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppointmentService
{
    /**
     * Create a new appointment
     */
    public function createAppointment(array $data, int $branchId): Appointment
    {
        return DB::transaction(function () use ($data, $branchId) {
            // Check for conflicts
            $this->checkForConflicts(
                $data['doctor_id'],
                $data['scheduled_at'],
                $data['type'] ?? 'physical'
            );

            $appointment = Appointment::create([
                'uuid' => (string) Str::uuid(),
                'branch_id' => $branchId,
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'scheduled_at' => $data['scheduled_at'],
                'type' => $data['type'] ?? 'physical',
                'status' => 'scheduled',
                'reason' => $data['reason'] ?? null,
                'online_meeting_link' => $data['type'] === 'online' ? $this->generateMeetingLink() : null,
            ]);

            // Send confirmation notification
            event(new \App\Events\AppointmentConfirmed($appointment));

            return $appointment;
        });
    }

    /**
     * Check for scheduling conflicts
     */
    protected function checkForConflicts(int $doctorId, string $scheduledAt, string $type): void
    {
        $scheduledTime = Carbon::parse($scheduledAt);
        $endTime = $scheduledTime->copy()->addMinutes(30); // Assuming 30 min appointments

        $conflict = Appointment::where('doctor_id', $doctorId)
            ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
            ->where(function ($query) use ($scheduledTime, $endTime) {
                $query->whereBetween('scheduled_at', [$scheduledTime, $endTime])
                    ->orWhereRaw('? BETWEEN scheduled_at AND DATE_ADD(scheduled_at, INTERVAL 30 MINUTE)', [$scheduledTime]);
            })
            ->exists();

        if ($conflict) {
            throw new \InvalidArgumentException('Doctor already has an appointment at this time');
        }
    }

    /**
     * Generate meeting link for online appointments
     */
    protected function generateMeetingLink(): string
    {
        // This could integrate with Zoom/Google Meet API
        $meetingId = strtoupper(Str::random(8));
        return "https://meet.medcare.com/" . $meetingId;
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Appointment $appointment, string $status, array $data = []): Appointment
    {
        $allowedStatuses = ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];

        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        $appointment->update(array_merge(['status' => $status], $data));

        // Create visit when appointment starts
        if ($status === 'in_progress' && !$appointment->visit_id) {
            $visitService = app(VisitService::class);
            $visit = $visitService->startVisit(
                $appointment->patient_id,
                $appointment->branch_id,
                $appointment->doctor_id,
                ['complaint' => $appointment->reason]
            );
            $appointment->update(['visit_id' => $visit->id]);
        }

        // Send status update notification
        event(new \App\Events\AppointmentStatusUpdated($appointment));

        return $appointment;
    }

    /**
     * Get doctor's schedule for a given date
     */
    public function getDoctorSchedule(int $doctorId, string $date)
    {
        return Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereDate('scheduled_at', $date)
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Get available time slots for a doctor on a given date
     */
    public function getAvailableSlots(int $doctorId, string $date, int $duration = 30): array
    {
        $startTime = Carbon::parse($date . ' 09:00:00');
        $endTime = Carbon::parse($date . ' 17:00:00');
        
        $bookedSlots = $this->getDoctorSchedule($doctorId, $date)
            ->pluck('scheduled_at')
            ->map(function ($time) {
                return Carbon::parse($time);
            });

        $availableSlots = [];
        $currentSlot = $startTime->copy();

        while ($currentSlot < $endTime) {
            $slotEnd = $currentSlot->copy()->addMinutes($duration);
            
            $isBooked = $bookedSlots->contains(function ($bookedTime) use ($currentSlot, $slotEnd) {
                return $bookedTime->between($currentSlot, $slotEnd);
            });

            if (!$isBooked) {
                $availableSlots[] = $currentSlot->format('H:i');
            }

            $currentSlot->addMinutes($duration);
        }

        return $availableSlots;
    }

    /**
     * Get upcoming appointments for a patient
     */
    public function getPatientUpcoming(int $patientId)
    {
        return Appointment::with(['doctor', 'branch'])
            ->where('patient_id', $patientId)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Cancel appointment
     */
    public function cancelAppointment(Appointment $appointment, string $reason): Appointment
    {
        return $this->updateStatus($appointment, 'cancelled', [
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }
}