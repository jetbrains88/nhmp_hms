<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting database cleanup...\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0');

$tablesToDrop = [
    'illness_tags',
    'diagnosis_illness_tag',
    'external_specialists',
    'diagnosis_external_specialist',
    'prescription_abbreviations',
    'medical_specialties',
    'diagnosis_medical_specialty'
];

foreach ($tablesToDrop as $table) {
    echo "Dropping table if exists: $table\n";
    Schema::dropIfExists($table);
}

$migrationsToRemove = [
    '2026_04_03_105149_create_medical_specialties_table',
    '2026_04_03_105150_update_external_specialists_table_remove_personal_info',
    '2026_04_03_111800_prune_external_specialists_columns',
    '2026_04_04_000001_add_consultation_pharmacy_features',
    '2026_04_04_000002_add_remaining_consultation_features',
    '2026_04_06_053048_refactor_specialist_referrals_to_specialties'
];

echo "Removing migration records...\n";
DB::table('migrations')->whereIn('migration', $migrationsToRemove)->delete();

// Also check if columns were added to diagnoses, prescriptions, prescription_dispensations
// Migration 2026_04_04_000001 adds medical_advice to diagnoses
if (Schema::hasColumn('diagnoses', 'medical_advice')) {
    echo "Removing medical_advice column from diagnoses\n";
    Schema::table('diagnoses', function ($table) {
        $table->dropColumn('medical_advice');
    });
}
// abbreviation_id to prescriptions
if (Schema::hasColumn('prescriptions', 'abbreviation_id')) {
    echo "Removing abbreviation_id column from prescriptions\n";
    Schema::table('prescriptions', function ($table) {
        $table->dropColumn('abbreviation_id');
    });
}
// alternative_medicine_id to prescription_dispensations
if (Schema::hasColumn('prescription_dispensations', 'alternative_medicine_id')) {
    echo "Removing alternative_medicine_id column from prescription_dispensations\n";
    Schema::table('prescription_dispensations', function ($table) {
        $table->dropColumn('alternative_medicine_id');
    });
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "Cleanup finished. Running migrations...\n";
