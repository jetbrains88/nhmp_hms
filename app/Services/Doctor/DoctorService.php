<?php

namespace App\Services\Doctor;

use App\Models\Diagnosis;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use App\Repositories\VisitRepository;
use App\Repositories\DoctorRepository;

class DoctorService
{
    protected $doctorRepository;
    protected $visitRepository;

    public function __construct(DoctorRepository $doctorRepository, VisitRepository $visitRepository)
    {
        $this->doctorRepository = $doctorRepository;
        $this->visitRepository = $visitRepository;
    }

    public function getDoctorRepository(): DoctorRepository
    {
        return $this->doctorRepository;
    }

    public function getVisitRepository(): VisitRepository
    {
        return $this->visitRepository;
    }

    public function getDashboardData(): array
    {
        $doctorId = Auth::id();
        
        return [
            'stats' => $this->doctorRepository->getDoctorStats($doctorId),
            'today_visits' => $this->doctorRepository->getDoctorVisits($doctorId, ['date' => now()->toDateString()]),
            'appointments' => $this->doctorRepository->getDoctorAppointments($doctorId),
            'recent_patients' => $this->getRecentPatients($doctorId),
        ];
    }

    public function startConsultation(int $visitId): array
    {
        $success = $this->visitRepository->startConsultation($visitId, Auth::id());
        
        return [
            'success' => $success,
            'message' => $success ? 'Consultation started successfully' : 'Unable to start consultation',
            'visit' => $success ? $this->doctorRepository->getVisitDetails($visitId) : null,
        ];
    }

    public function createDiagnosis(array $data): Diagnosis
    {
        $data['doctor_id'] = Auth::id();
        
        return Diagnosis::create($data);
    }

    public function createPrescription(array $data): Prescription
    {
        return Prescription::create(array_merge($data, [
            'prescribed_by' => Auth::id(),
            'status' => 'pending',
        ]));
    }

    public function searchPatients(string $searchTerm): array
    {
        $patients = $this->doctorRepository->searchPatients($searchTerm);
        
        return [
            'success' => true,
            'patients' => $patients,
            'count' => $patients->count(),
        ];
    }

    public function getOfficePatients(int $officeId): array
    {
        $patients = $this->doctorRepository->getOfficePatients($officeId);
        
        return [
            'success' => true,
            'patients' => $patients,
            'count' => $patients->count(),
        ];
    }

    private function getRecentPatients(int $doctorId, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Patient::whereHas('visits', function ($query) use ($doctorId) {
            $query->where('doctor_id', $doctorId)
                ->whereDate('created_at', '>=', now()->subDays(30));
        })
        ->with(['lastVisit'])
        ->orderByDesc(function ($query) use ($doctorId) {
            $query->select('created_at')
                ->from('visits')
                ->whereColumn('patient_id', 'patients.id')
                ->where('doctor_id', $doctorId)
                ->orderByDesc('created_at')
                ->limit(1);
        })
        ->limit($limit)
        ->get();
    }
}