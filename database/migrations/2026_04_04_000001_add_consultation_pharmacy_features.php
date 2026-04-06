<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────
        // 1. Illness Tags (master list of chronic/acute conditions)
        // ─────────────────────────────────────────────────────────
        Schema::create('illness_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('chronic'); // chronic | acute | infectious | other
            $table->text('description')->nullable();
            $table->string('icd_code')->nullable(); // ICD-10 code for reference
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });

        // ─────────────────────────────────────────────────────────
        // 2. Pivot: Diagnosis ↔ Illness Tags
        // ─────────────────────────────────────────────────────────
        Schema::create('diagnosis_illness_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnosis_id');
            $table->unsignedBigInteger('illness_tag_id');
            $table->timestamps();

            $table->foreign('diagnosis_id')->references('id')->on('diagnoses')->onDelete('cascade');
            $table->foreign('illness_tag_id')->references('id')->on('illness_tags')->onDelete('cascade');
            $table->unique(['diagnosis_id', 'illness_tag_id']);
        });

        // ─────────────────────────────────────────────────────────
        // 3. External Specialists (branch-scoped referral network)
        // ─────────────────────────────────────────────────────────
        Schema::create('external_specialists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable(); // null = global, specific branch = scoped
            $table->string('name');
            $table->string('specialty'); // e.g., Cardiologist, Neurologist
            $table->string('clinic_hospital')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'is_active']);
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // ─────────────────────────────────────────────────────────
        // 4. Pivot: Diagnosis ↔ External Specialists (referrals)
        // ─────────────────────────────────────────────────────────
        Schema::create('diagnosis_external_specialist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnosis_id');
            $table->unsignedBigInteger('external_specialist_id');
            $table->text('referral_notes')->nullable(); // Optional referral reason
            $table->timestamps();

            $table->foreign('diagnosis_id')->references('id')->on('diagnoses')->onDelete('cascade');
            $table->foreign('external_specialist_id')->references('id')->on('external_specialists')->onDelete('cascade');
            $table->unique(['diagnosis_id', 'external_specialist_id'], 'diag_ext_specialist_unique');
        });

        // ─────────────────────────────────────────────────────────
        // 5. Prescription Abbreviations
        // ─────────────────────────────────────────────────────────
        Schema::create('prescription_abbreviations', function (Blueprint $table) {
            $table->id();
            $table->string('abbreviation');         // e.g. "BID"
            $table->string('full_meaning');          // e.g. "Twice a day (bis in die)"
            $table->string('category');              // frequency | route | timing | dosage | general
            $table->unsignedTinyInteger('doses_per_day')->nullable(); // BID=2, TID=3, QID=4, QD=1 etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->unique('abbreviation');
        });

        // ─────────────────────────────────────────────────────────
        // 6. Modify: diagnoses — add medical_advice column
        // ─────────────────────────────────────────────────────────
        Schema::table('diagnoses', function (Blueprint $table) {
            $table->text('medical_advice')->nullable()->after('recommendations')
                ->comment('Doctor lifestyle/medical instructions for the patient');
        });

        // ─────────────────────────────────────────────────────────
        // 7. Modify: prescriptions — add abbreviation_id
        // ─────────────────────────────────────────────────────────
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('abbreviation_id')->nullable()->after('instructions')
                ->comment('FK to prescription_abbreviations — selected Rx shorthand');
            $table->foreign('abbreviation_id')->references('id')->on('prescription_abbreviations')->onDelete('set null');
        });

        // ─────────────────────────────────────────────────────────
        // 8. Modify: prescription_dispensations — add alternative_medicine_id
        // ─────────────────────────────────────────────────────────
        Schema::table('prescription_dispensations', function (Blueprint $table) {
            $table->unsignedBigInteger('alternative_medicine_id')->nullable()->after('medicine_batch_id')
                ->comment('FK to medicines — set when pharmacist substitutes with same generic-name alternative');
            $table->foreign('alternative_medicine_id')->references('id')->on('medicines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove added columns first (foreign keys)
        Schema::table('prescription_dispensations', function (Blueprint $table) {
            $table->dropForeign(['alternative_medicine_id']);
            $table->dropColumn('alternative_medicine_id');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['abbreviation_id']);
            $table->dropColumn('abbreviation_id');
        });

        Schema::table('diagnoses', function (Blueprint $table) {
            $table->dropColumn('medical_advice');
        });

        // Drop new tables (pivot tables first)
        Schema::dropIfExists('diagnosis_external_specialist');
        Schema::dropIfExists('diagnosis_illness_tag');
        Schema::dropIfExists('external_specialists');
        Schema::dropIfExists('prescription_abbreviations');
        Schema::dropIfExists('illness_tags');
    }
};
