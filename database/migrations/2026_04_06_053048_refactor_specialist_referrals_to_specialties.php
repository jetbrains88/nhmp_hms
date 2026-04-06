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
        // Drop the old pivot table first to satisfy foreign key constraints
        Schema::dropIfExists('diagnosis_external_specialist');
        
        // Drop the specialists table
        Schema::dropIfExists('external_specialists');

        // Create the new specialties pivot table for referrals
        Schema::create('diagnosis_medical_specialty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnosis_id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_specialty_id')->constrained()->onDelete('cascade');
            $table->text('referral_notes')->nullable();
            $table->timestamps();

            $table->unique(['diagnosis_id', 'medical_specialty_id'], 'diag_med_specialty_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis_medical_specialty');
    }
};
