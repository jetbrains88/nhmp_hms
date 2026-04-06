<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Display a listing of appointments.
     */
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $date = $request->get('date', now()->format('Y-m-d'));

        $appointments = Appointment::with(['patient', 'doctor'])
            ->where('branch_id', $branchId)
            ->whereDate('scheduled_at', $date)
            ->orderBy('scheduled_at')
            ->get();

        $doctors = User::whereHas('roles', function ($q) {
            $q->where('name', 'doctor');
        })->get();

        return view('reception.appointments.index', compact('appointments', 'doctors', 'date'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(Request $request)
    {
        $patientId = $request->get('patient_id');
        $patients = Patient::where('branch_id', session('current_branch_id'))
            ->orderBy('name')
            ->get();

        $doctors = User::whereHas('roles', function ($q) {
            $q->where('name', 'doctor');
        })->get();

        return view('reception.appointments.create', compact('patients', 'doctors', 'patientId'));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after:now',
            'type' => 'required|in:physical,online',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $appointment = $this->appointmentService->createAppointment(
                $validated,
                session('current_branch_id')
            );

            return redirect()->route('reception.appointments.show', $appointment)
                ->with('success', 'Appointment scheduled successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'visit']);

        return view('reception.appointments.show', compact('appointment'));
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
        ]);

        $appointment = $this->appointmentService->updateStatus($appointment, $request->status);

        return redirect()->route('reception.appointments.show', $appointment)
            ->with('success', 'Appointment status updated.');
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::where('branch_id', session('current_branch_id'))
            ->orderBy('name')
            ->get();

        $doctors = User::whereHas('roles', function ($q) {
            $q->where('name', 'doctor');
        })->get();

        return view('reception.appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'type' => 'required|in:physical,online',
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $appointment->update($validated);

            return redirect()->route('reception.appointments.show', $appointment)
                ->with('success', 'Appointment updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update appointment: ' . $e->getMessage());
        }
    }

    /**
     * Cancel appointment.
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $appointment = $this->appointmentService->cancelAppointment($appointment, $request->reason);

        return redirect()->route('reception.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }
}
