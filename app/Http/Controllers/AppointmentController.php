<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
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
     * Display appointments calendar
     */
    public function index(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;
        $date = $request->get('date', now()->format('Y-m-d'));
        
        $appointments = Appointment::with(['patient', 'doctor'])
            ->where('branch_id', $branchId)
            ->whereDate('scheduled_at', $date)
            ->orderBy('scheduled_at')
            ->get();
        
        $doctors = User::whereHas('roles', function ($q) {
            $q->where('name', 'doctor');
        })->get();
        
        return view('appointments.index', compact('appointments', 'doctors', 'date'));
    }

    /**
     * Show create appointment form
     */
    public function create(Request $request)
    {
        $patientId = $request->get('patient_id');
        $doctorId = $request->get('doctor_id');
        
        $patients = Patient::where('branch_id', auth()->user()->current_branch_id)
            ->orderBy('name')
            ->get();
            
        $doctors = User::whereHas('roles', function ($q) {
            $q->where('name', 'doctor');
        })->get();
        
        return view('appointments.create', compact('patients', 'doctors', 'patientId', 'doctorId'));
    }

    /**
     * Store new appointment
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            $appointment = $this->appointmentService->createAppointment(
                $request->validated(),
                auth()->user()->current_branch_id
            );
            
            return redirect()
                ->route('appointments.show', $appointment)
                ->with('success', 'Appointment scheduled successfully');
                
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show appointment details
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'visit']);
        
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Update appointment status
     */
    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment)
    {
        $appointment = $this->appointmentService->updateStatus(
            $appointment,
            $request->status,
            $request->only('cancellation_reason')
        );
        
        $message = $request->status == 'cancelled' 
            ? 'Appointment cancelled successfully'
            : 'Appointment status updated successfully';
        
        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Get available time slots for a doctor
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);
        
        $slots = $this->appointmentService->getAvailableSlots(
            $request->doctor_id,
            $request->date
        );
        
        return response()->json($slots);
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);
        
        $appointment = $this->appointmentService->cancelAppointment(
            $appointment,
            $request->reason
        );
        
        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'Appointment cancelled successfully');
    }
}