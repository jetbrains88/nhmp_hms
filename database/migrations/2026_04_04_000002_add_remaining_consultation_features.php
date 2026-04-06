<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────
        // 1. Prescription Abbreviations
        // ─────────────────────────────────────────────────────────
        if (!Schema::hasTable('prescription_abbreviations')) {
            Schema::create('prescription_abbreviations', function (Blueprint $table) {
                $table->id();
                $table->string('abbreviation');         // e.g. "BID"
                $table->string('full_meaning');          // e.g. "Twice a day"
                $table->string('category');              // frequency | route | timing | dosage | general
                $table->unsignedTinyInteger('doses_per_day')->nullable(); // BID=2, TID=3, QID=4
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['category', 'is_active']);
                $table->unique('abbreviation');
            });

            // Seed the standard abbreviations provided by user
            $abbreviations = [
                ['abbreviation' => 'ac',    'full_meaning' => 'Before meals',              'category' => 'Frequency & Timing', 'doses_per_day' => null],
                ['abbreviation' => 'pc',    'full_meaning' => 'After meals',               'category' => 'Frequency & Timing', 'doses_per_day' => null],
                ['abbreviation' => 'bid',   'full_meaning' => 'Twice a day',               'category' => 'Frequency & Timing', 'doses_per_day' => 2],
                ['abbreviation' => 'tid',   'full_meaning' => 'Three times a day',         'category' => 'Frequency & Timing', 'doses_per_day' => 3],
                ['abbreviation' => 'qid',   'full_meaning' => 'Four times a day',          'category' => 'Frequency & Timing', 'doses_per_day' => 4],
                ['abbreviation' => 'q4h',   'full_meaning' => 'Every 4 hours',             'category' => 'Frequency & Timing', 'doses_per_day' => 6],
                ['abbreviation' => 'qd',    'full_meaning' => 'Every day / Daily',         'category' => 'Frequency & Timing', 'doses_per_day' => 1],
                ['abbreviation' => 'qod',   'full_meaning' => 'Every other day',           'category' => 'Frequency & Timing', 'doses_per_day' => null],
                ['abbreviation' => 'hs',    'full_meaning' => 'At bedtime',                'category' => 'Frequency & Timing', 'doses_per_day' => 1],
                ['abbreviation' => 'prn',   'full_meaning' => 'As needed',                 'category' => 'Frequency & Timing', 'doses_per_day' => null],
                ['abbreviation' => 'stat',  'full_meaning' => 'Immediately',               'category' => 'Frequency & Timing', 'doses_per_day' => null],
                ['abbreviation' => 'PO',    'full_meaning' => 'By mouth / Orally',         'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'SL',    'full_meaning' => 'Under the tongue',          'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'IV',    'full_meaning' => 'Intravenous',               'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'IM',    'full_meaning' => 'Intramuscular',             'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'SC',    'full_meaning' => 'Subcutaneous',              'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'OD',    'full_meaning' => 'Right eye',                 'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'OS',    'full_meaning' => 'Left eye',                  'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'OU',    'full_meaning' => 'Both eyes',                 'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'AD',    'full_meaning' => 'Right ear',                 'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'AS',    'full_meaning' => 'Left ear',                  'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'AU',    'full_meaning' => 'Both ears',                 'category' => 'Route of Administration', 'doses_per_day' => null],
                ['abbreviation' => 'aa',    'full_meaning' => 'Of each',                   'category' => 'Instructions', 'doses_per_day' => null],
                ['abbreviation' => 'ad lib','full_meaning' => 'Freely / As desired',       'category' => 'Instructions', 'doses_per_day' => null],
                ['abbreviation' => 'gtt',   'full_meaning' => 'Drop',                      'category' => 'Instructions', 'doses_per_day' => null],
                ['abbreviation' => 'ss',    'full_meaning' => 'One-half',                  'category' => 'Instructions', 'doses_per_day' => null],
                ['abbreviation' => 'AAA',   'full_meaning' => 'Apply to affected area',    'category' => 'Instructions', 'doses_per_day' => null],
                ['abbreviation' => 'DAW',   'full_meaning' => 'Dispense as written',       'category' => 'Instructions', 'doses_per_day' => null],
                ['abbreviation' => 'SR',    'full_meaning' => 'Slow Release',              'category' => 'Medication Terms', 'doses_per_day' => null],
                ['abbreviation' => 'XR',    'full_meaning' => 'Extended Release',          'category' => 'Medication Terms', 'doses_per_day' => null],
            ];

            $now = now();
            foreach ($abbreviations as &$row) {
                $row['is_active']   = true;
                $row['created_at']  = $now;
                $row['updated_at']  = $now;
            }
            DB::table('prescription_abbreviations')->insert($abbreviations);
        }

        // ─────────────────────────────────────────────────────────
        // 2. diagnoses — add medical_advice column
        // ─────────────────────────────────────────────────────────
        if (!Schema::hasColumn('diagnoses', 'medical_advice')) {
            Schema::table('diagnoses', function (Blueprint $table) {
                $table->text('medical_advice')->nullable()->after('recommendations')
                    ->comment('Doctor lifestyle/medical instructions for the patient');
            });
        }

        // ─────────────────────────────────────────────────────────
        // 3. prescriptions — add abbreviation_id
        // ─────────────────────────────────────────────────────────
        if (!Schema::hasColumn('prescriptions', 'abbreviation_id')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('abbreviation_id')->nullable()->after('instructions')
                    ->comment('FK to prescription_abbreviations');
                $table->foreign('abbreviation_id')
                    ->references('id')->on('prescription_abbreviations')
                    ->onDelete('set null');
            });
        }

        // ─────────────────────────────────────────────────────────
        // 4. prescription_dispensations — add alternative_medicine_id
        // ─────────────────────────────────────────────────────────
        if (!Schema::hasColumn('prescription_dispensations', 'alternative_medicine_id')) {
            Schema::table('prescription_dispensations', function (Blueprint $table) {
                $table->unsignedBigInteger('alternative_medicine_id')->nullable()->after('medicine_batch_id')
                    ->comment('Set when pharmacist substitutes with same generic-name alternative');
                $table->foreign('alternative_medicine_id')
                    ->references('id')->on('medicines')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('prescription_dispensations', 'alternative_medicine_id')) {
            Schema::table('prescription_dispensations', function (Blueprint $table) {
                $table->dropForeign(['alternative_medicine_id']);
                $table->dropColumn('alternative_medicine_id');
            });
        }

        if (Schema::hasColumn('prescriptions', 'abbreviation_id')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->dropForeign(['abbreviation_id']);
                $table->dropColumn('abbreviation_id');
            });
        }

        if (Schema::hasColumn('diagnoses', 'medical_advice')) {
            Schema::table('diagnoses', function (Blueprint $table) {
                $table->dropColumn('medical_advice');
            });
        }

        Schema::dropIfExists('prescription_abbreviations');
    }
};
