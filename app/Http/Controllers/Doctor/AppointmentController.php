<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
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
        $doctorId = auth()->id();
        $date = $request->get('date', now()->format('Y-m-d'));

        $appointments = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereDate('scheduled_at', $date)
            ->orderBy('scheduled_at')
            ->get();

        return view('doctor.appointments.index', compact('appointments', 'date'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(Request $request)
    {
        $patientId = $request->get('patient_id');
        $patients = Patient::where('branch_id', session('current_branch_id'))->orderBy('name')->get();

        return view('doctor.appointments.create', compact('patients', 'patientId'));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'scheduled_at' => 'required|date|after:now',
            'type' => 'required|in:physical,online',
            'reason' => 'nullable|string|max:500',
        ]);

        $validated['doctor_id'] = auth()->id();

        try {
            $appointment = $this->appointmentService->createAppointment(
                $validated,
                session('current_branch_id')
            );

            return redirect()->route('doctor.appointments.show', $appointment)
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
        $appointment->load(['patient', 'visit']);

        return view('doctor.appointments.show', compact('appointment'));
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

        return redirect()->route('doctor.appointments.show', $appointment)
            ->with('success', 'Appointment status updated.');
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

        return redirect()->route('doctor.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }
}
