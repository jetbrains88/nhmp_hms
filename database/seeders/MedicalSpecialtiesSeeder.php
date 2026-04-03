<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalSpecialty;

class MedicalSpecialtiesSeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            'Cardiologist',
            'Dermatologist',
            'Endocrinologist',
            'Gastroenterologist',
            'Geriatrician',
            'Gynecologist (OB/GYN)',
            'Hematologist',
            'Infectious Disease Specialist',
            'Nephrologist',
            'Neurologist',
            'Oncologist',
            'Ophthalmologist',
            'Orthopedic Surgeon',
            'Pediatrician',
            'Pulmonologist',
            'Psychiatrist',
            'Rheumatologist',
            'Critical Care Medicine',
            'Sports Medicine',
            'Sleep Medicine',
            'Hospice and Palliative Medicine',
        ];

        foreach ($specialties as $name) {
            MedicalSpecialty::updateOrCreate(['name' => $name]);
        }
    }
}

