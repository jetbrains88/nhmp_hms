<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\EmployeeDetail;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientService
{
    protected $opdNumberService;
    
    public function __construct(OPDNumberService $opdNumberService)
    {
        $this->opdNumberService = $opdNumberService;
    }
    
    /**
     * Register a new patient with OPD number generation
     */
    public function registerPatient(array $data, int $branchId): Patient
    {
        return DB::transaction(function () use ($data, $branchId) {
            // Generate OPD number
            $opdData = $this->opdNumberService->generateNext();
            
            // Generate EMRN if not provided
            if (empty($data['emrn'])) {
                $data['emrn'] = $this->generateEMRN();
            }
            
            // Prepare patient data
            $patientData = [
                'uuid' => (string) Str::uuid(),
                'branch_id' => $branchId,
                'cnic' => $data['cnic'] ?? null,
                'emrn' => $data['emrn'],
                'name' => $data['name'],
                'dob' => $data['dob'],
                'gender' => $data['gender'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'emergency_contact' => $data['emergency_contact'] ?? null,
                'allergies' => $data['allergies'] ?? null,
                'chronic_conditions' => $data['chronic_conditions'] ?? null,
                'medical_history' => $data['medical_history'] ?? null,
                'blood_group' => $data['blood_group'] ?? null,
                'is_active' => true,
                'opd_year' => $opdData['year'],
                'opd_sequence' => $opdData['sequence'],
                'user_id' => $data['user_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'relationship' => $data['relationship'] ?? ($data['category'] === 'nhmp' ? 'self' : null),
            ];
            
            $patient = Patient::create($patientData);
            
            // Create employee details if this is an NHMP employee
            if (!empty($data['is_nhmp']) && ($data['is_nhmp'] === true || $data['is_nhmp'] === '1')) {
                EmployeeDetail::create([
                    'patient_id' => $patient->id,
                    'is_nhmp' => true,
                    'designation_id' => $data['designation_id'] ?? null,
                    'office_id' => $data['office_id'] ?? null,
                    'rank' => $data['rank'] ?? null,
                ]);
            }
            
            return $patient;
        });
    }
    
    /**
     * Register a dependent for an existing patient
     */
    public function registerDependent(int $parentId, array $data, int $branchId): Patient
    {
        $parent = Patient::findOrFail($parentId);
        
        return DB::transaction(function () use ($parent, $data, $branchId) {
            // Generate OPD number
            $opdData = $this->opdNumberService->generateNext();
            
            $patient = Patient::create([
                'uuid' => (string) Str::uuid(),
                'branch_id' => $branchId,
                'cnic' => $data['cnic'] ?? null, // Dependents may not have CNIC
                'emrn' => $this->generateEMRN(),
                'name' => $data['name'],
                'dob' => $data['dob'],
                'gender' => $data['gender'],
                'phone' => $data['phone'] ?? $parent->phone,
                'address' => $data['address'] ?? $parent->address,
                'emergency_contact' => $data['emergency_contact'] ?? $parent->phone,
                'blood_group' => $data['blood_group'] ?? null,
                'is_active' => true,
                'opd_year' => $opdData['year'],
                'opd_sequence' => $opdData['sequence'],
                'parent_id' => $parent->id,
                'relationship' => $data['relationship'],
            ]);
            
            return $patient;
        });
    }
    
    /**
     * Update patient information
     */
    public function updatePatient(Patient $patient, array $data): Patient
    {
        return DB::transaction(function () use ($patient, $data) {
            // Update patient basic info
            $patient->update([
                'name' => $data['name'] ?? $patient->name,
                'phone' => $data['phone'] ?? $patient->phone,
                'address' => $data['address'] ?? $patient->address,
                'emergency_contact' => $data['emergency_contact'] ?? $patient->emergency_contact,
                'allergies' => $data['allergies'] ?? $patient->allergies,
                'chronic_conditions' => $data['chronic_conditions'] ?? $patient->chronic_conditions,
                'medical_history' => $data['medical_history'] ?? $patient->medical_history,
                'blood_group' => $data['blood_group'] ?? $patient->blood_group,
            ]);
            
            // Update employee details if exists
            if ($patient->employeeDetail) {
                $patient->employeeDetail->update([
                    'designation_id' => $data['designation_id'] ?? $patient->employeeDetail->designation_id,
                    'office_id' => $data['office_id'] ?? $patient->employeeDetail->office_id,
                    'rank' => $data['rank'] ?? $patient->employeeDetail->rank,
                ]);
            } elseif (!empty($data['is_nhmp'])) {
                // Create employee details if newly marked as NHMP
                EmployeeDetail::create([
                    'patient_id' => $patient->id,
                    'is_nhmp' => true,
                    'designation_id' => $data['designation_id'] ?? null,
                    'office_id' => $data['office_id'] ?? null,
                    'rank' => $data['rank'] ?? null,
                ]);
            }
            
            return $patient->fresh();
        });
    }
    
    /**
     * Search patients by various criteria
     */
    public function searchPatients(string $query, int $branchId = null)
    {
        $search = Patient::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('cnic', 'LIKE', "%{$query}%")
                  ->orWhere('emrn', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%");
            })
            ->with(['employeeDetail.designation', 'employeeDetail.office']);
            
        if ($branchId) {
            $search->where('branch_id', $branchId);
        }
        
        return $search->paginate(15);
    }
    
    /**
     * Generate unique EMRN (Electronic Medical Record Number)
     */
    protected function generateEMRN(): string
    {
        $prefix = 'EMRN';
        $year = now()->year;
        $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        $emrn = $prefix . '-' . $year . '-' . $random;
        
        // Ensure uniqueness
        while (Patient::where('emrn', $emrn)->exists()) {
            $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $emrn = $prefix . '-' . $year . '-' . $random;
        }
        
        return $emrn;
    }
}