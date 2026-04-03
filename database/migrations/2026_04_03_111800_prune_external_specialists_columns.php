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
        Schema::table('external_specialists', function (Blueprint $table) {
            // "keep Medical Specialty and status columns only name and other colmns are not required"
            $table->dropColumn(['specialty', 'clinic_hospital', 'address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_specialists', function (Blueprint $table) {
            $table->string('specialty')->nullable();
            $table->string('clinic_hospital')->nullable();
            $table->text('address')->nullable();
        });
    }
};
