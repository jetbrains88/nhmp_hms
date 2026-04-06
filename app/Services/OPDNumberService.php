<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class OPDNumberService
{
    /**
     * Generate the next OPD number for a given year
     * Format: {sequence}/{year_short} e.g., 1/25, 2/25
     */
    public function generateNext(int $year = null): array
    {
        $year = $year ?? now()->year;
        
        // Use database transaction with lock to prevent race conditions
        return DB::transaction(function () use ($year) {
            // Lock the table to prevent duplicate sequences
            $lastSequence = Patient::where('opd_year', $year)
                ->lockForUpdate()
                ->max('opd_sequence') ?? 0;
            
            $nextSequence = $lastSequence + 1;
            
            return [
                'year' => $year,
                'sequence' => $nextSequence,
                'formatted' => $nextSequence . '/' . substr($year, -2)
            ];
        });
    }
    
    /**
     * Format OPD number from year and sequence
     */
    public function format(int $sequence, int $year): string
    {
        return $sequence . '/' . substr($year, -2);
    }
    
    /**
     * Parse OPD number into year and sequence
     */
    public function parse(string $opdNumber): ?array
    {
        $parts = explode('/', $opdNumber);
        
        if (count($parts) !== 2) {
            return null;
        }
        
        $sequence = (int) $parts[0];
        $yearShort = $parts[1];
        
        // Convert short year to full year (e.g., 25 -> 2025)
        $year = 2000 + (int) $yearShort;
        
        // Validate that this OPD number exists
        $patient = Patient::where('opd_year', $year)
            ->where('opd_sequence', $sequence)
            ->first();
            
        if (!$patient) {
            return null;
        }
        
        return [
            'year' => $year,
            'sequence' => $sequence,
            'patient' => $patient
        ];
    }
}