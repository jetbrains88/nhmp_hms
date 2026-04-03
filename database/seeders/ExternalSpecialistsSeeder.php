<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExternalSpecialist;
use App\Models\MedicalSpecialty;

class ExternalSpecialistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = MedicalSpecialty::all();

        if ($specialties->isEmpty()) {
            $this->command->warn('No medical specialties found. Please run MedicalSpecialtiesSeeder first.');
            return;
        }

        $physicians = [
            'Cardiologist' => ['Dr. Ahmad Khan', 'Dr. Sarah Smith'],
            'Dermatologist' => ['Dr. Fatima Zahra', 'Dr. John Doe'],
            'Neurologist' => ['Dr. Ali Raza', 'Dr. Jane Miller'],
            'Pediatrician' => ['Dr. Muhammad Bilal', 'Dr. Emily White'],
            'Gynecologist (OB/GYN)' => ['Dr. Zainab Bibi', 'Dr. Sophia Brown'],
            'Oncologist' => ['Dr. Usman Ghani'],
            'Gastroenterologist' => ['Dr. Hina Malik'],
        ];

        foreach ($physicians as $specialtyName => $names) {
            $specialty = $specialties->firstWhere('name', $specialtyName);
            
            if ($specialty) {
                foreach ($names as $name) {
                    ExternalSpecialist::updateOrCreate(
                        ['name' => $name, 'medical_specialty_id' => $specialty->id],
                        ['is_active' => true, 'branch_id' => session('current_branch_id') ?? 1]
                    );
                }
            }
        }

        $this->command->info('Physician Registry seeded successfully.');
    }
}
