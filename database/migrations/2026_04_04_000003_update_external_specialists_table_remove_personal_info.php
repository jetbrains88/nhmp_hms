<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('external_specialists', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email']);
            $table->foreignId('medical_specialty_id')->nullable()->constrained('medical_specialties')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('external_specialists', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->dropForeign(['medical_specialty_id']);
            $table->dropColumn('medical_specialty_id');
        });
    }
};
