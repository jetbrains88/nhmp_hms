<?php

namespace App\Services;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\Vital;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VisitService
{
    /**
     * Start a new visit for a patient
     */
    public function startVisit(int $patientId, int $branchId, int $doctorId = null, array $data = []): Visit
    {
        return DB::transaction(function () use ($patientId, $branchId, $doctorId, $data) {
            // Generate unique queue token
            $queueToken = $this->generateQueueToken($branchId);
            
            $visit = Visit::create([
                'uuid' => (string) Str::uuid(),
                'branch_id' => $branchId,
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'queue_token' => $queueToken,
                'visit_type' => $data['visit_type'] ?? 'routine',
                'status' => 'waiting',
                'complaint' => $data['complaint'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
            
            return $visit;
        });
    }
    
    /**
     * Update visit status
     */
    public function updateStatus(Visit $visit, string $status): Visit
    {
        $allowedStatuses = ['waiting', 'in_progress', 'completed', 'cancelled'];
        
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }
        
        $visit->update(['status' => $status]);
        
        return $visit;
    }
    
    /**
     * Assign doctor to visit
     */
    public function assignDoctor(Visit $visit, int $doctorId): Visit
    {
        $visit->update(['doctor_id' => $doctorId]);
        
        return $visit;
    }
    
    /**
     * Get current queue for a branch
     */
    public function getQueue(int $branchId, string $status = 'waiting')
    {
        return Visit::with(['patient', 'doctor'])
            ->where('branch_id', $branchId)
            ->where('status', $status)
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    /**
     * Get waiting count for doctor
     */
    public function getWaitingCountForDoctor(int $doctorId): int
    {
        return Visit::where('doctor_id', $doctorId)
            ->where('status', 'waiting')
            ->count();
    }
    
    /**
     * Generate unique queue token
     */
    protected function generateQueueToken(int $branchId): string
    {
        $date = now()->format('Ymd');
        $prefix = 'TKN';
        
        // Get count of visits today for this branch
        $count = Visit::where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->count() + 1;
        
        $token = $prefix . '-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        return $token;
    }
}