<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Send a notification to a user
     */
    public function send(
        User $user,
        string $title,
        string $body,
        string $type = 'info',
        $related = null,
        string $actionUrl = null,
        string $actionText = null,
        User $triggeredBy = null
    ): Notification {
        $data = [
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'triggered_by' => $triggeredBy?->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
        ];

        if ($related) {
            $data['related_type'] = get_class($related);
            $data['related_id'] = $related->id;
        }

        return Notification::create($data);
    }

    /**
     * Send notification to multiple users
     */
    public function sendBulk(array $userIds, string $title, string $body, string $type = 'info', $related = null): void
    {
        foreach ($userIds as $userId) {
            $this->send(User::find($userId), $title, $body, $type, $related);
        }
    }

    /**
     * Send patient waiting notification to doctor
     */
    public function patientWaiting(User $doctor, $patient, $visit): Notification
    {
        return $this->send(
            $doctor,
            'New Patient Waiting',
            "Patient {$patient->name} is waiting for consultation",
            'info',
            $visit,
            route('doctor.consultancy.show', $visit),
            'Start Consultation',
            auth()->user()
        );
    }

    /**
     * Send new prescription notification to pharmacy
     */
    public function newPrescription(User $pharmacy, $prescription): Notification
    {
        $patient = $prescription->diagnosis->visit->patient;
        
        return $this->send(
            $pharmacy,
            'New Prescription',
            "Dr. {$prescription->prescribedBy->name} prescribed {$prescription->quantity} units of {$prescription->medicine->name} for {$patient->name}",
            'info',
            $prescription,
            route('pharmacy.prescriptions.show', $prescription),
            'Dispense Medicine'
        );
    }

    /**
     * Send lab results ready notification to doctor
     */
    public function labResultsReady(User $doctor, $labOrder): Notification
    {
        $patient = $labOrder->patient;
        
        return $this->send(
            $doctor,
            'Lab Results Ready',
            "Lab report for {$patient->name} ({$labOrder->lab_number}) is ready",
            'success',
            $labOrder,
            route('doctor.lab-orders.show', $labOrder),
            'View Report'
        );
    }

    /**
     * Send new lab order notification to lab technician
     */
    public function newLabOrder(User $technician, $labOrder): Notification
    {
        $patient = $labOrder->patient;
        $tests = $labOrder->items->pluck('labTestType.name')->implode(', ');
        
        return $this->send(
            $technician,
            'New Lab Order',
            "New lab order for {$patient->name}. Tests: {$tests}",
            'info',
            $labOrder,
            route('lab.orders.show', $labOrder),
            'Process Order'
        );
    }

    /**
     * Send completed visit notification (typically to pharmacy)
     */
    public function visitCompleted(User $pharmacy, $visit): Notification
    {
        $patient = $visit->patient;
        
        return $this->send(
            $pharmacy,
            'Visit Completed',
            "Visit completed for {$patient->name}. Review if any prescriptions need dispensing.",
            'success',
            $visit,
            route('pharmacy.prescriptions.index'),
            'View Prescriptions'
        );
    }

    /**
     * Send stock alert notification to pharmacy
     */
    public function stockAlert(User $pharmacy, $alert): Notification
    {
        return $this->send(
            $pharmacy,
            $alert->alert_type == 'low_stock' ? 'Low Stock Alert' : 'Stock Alert',
            $alert->message,
            'warning',
            $alert,
            route('pharmacy.alerts'),
            'View Alerts'
        );
    }

    /**
     * Send appointment reminder
     */
    public function appointmentReminder($appointment): Notification
    {
        return $this->send(
            $appointment->patient->user,
            'Appointment Reminder',
            "You have an appointment with Dr. {$appointment->doctor->name} tomorrow at " . $appointment->scheduled_at->format('H:i'),
            'info',
            $appointment,
            $appointment->type == 'online' ? $appointment->online_meeting_link : route('appointments.show', $appointment),
            $appointment->type == 'online' ? 'Join Meeting' : 'View Details'
        );
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnread(User $user)
    {
        return Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): Notification
    {
        $notification->update(['read_at' => now()]);
        return $notification;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}