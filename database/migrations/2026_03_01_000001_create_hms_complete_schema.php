<?php
// database/migrations/2026_03_01_000001_create_hms_complete_schema.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =========================================================================
        // 1. OFFICES & DESIGNATIONS (independent)
        // =========================================================================
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('type'); // Region, Zone, Sector, PLHQ, Beat
            $table->foreignId('parent_id')->nullable()->constrained('offices')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('parent_id');
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title', 100);
            $table->string('short_form', 50)->nullable();
            $table->integer('bps')->nullable();
            $table->string('cadre_type', 50)->nullable();
            $table->string('rank_group', 100)->nullable();
            $table->timestamps();
        });

        // =========================================================================
        // 2. BRANCHES (depends on offices)
        // =========================================================================
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->enum('type', ['CMO', 'RMO'])->default('CMO');
            $table->string('location')->nullable();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // =========================================================================
        // 3. ROLES & PERMISSIONS
        // =========================================================================
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('display_name');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->timestamps();

            $table->unique(['name', 'branch_id']);
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('group');
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        // =========================================================================
        // 4. USERS & ACCESS
        // =========================================================================
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('preferences')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
        });

        Schema::create('branch_user', function (Blueprint $table) {
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->primary(['branch_id', 'user_id']);
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
            $table->timestamps();
        });

        // =========================================================================
        // 5. PATIENTS (with employee/dependent support)
        // =========================================================================
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cnic')->nullable();
            $table->string('emrn')->unique();
            $table->string('name');
            $table->date('dob');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('medical_history')->nullable();
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->boolean('is_active')->default(true);

            // OPD yearly sequence
            $table->year('opd_year')->nullable();
            $table->integer('opd_sequence')->nullable();
            $table->unique(['opd_year', 'opd_sequence']);

            // Employee / dependent relationship
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('patients')->cascadeOnDelete();
            $table->enum('relationship', ['self', 'father', 'mother', 'husband', 'wife', 'son', 'daughter', 'spouse', 'child', 'parent', 'other'])->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'cnic']);
            $table->unique(['cnic', 'deleted_at']);
        });

        // NHMP employee details
        Schema::create('employee_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_nhmp')->default(false);
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('office_id')->nullable()->constrained()->nullOnDelete();
            $table->string('rank')->nullable();
            $table->timestamps();
        });

        // =========================================================================
        // 6. PHARMACY & INVENTORY
        // =========================================================================
        Schema::create('medicine_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('medicine_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_global')->default(false)->index();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('brand')->nullable();
            $table->string('manufacturer')->nullable();
            $table->foreignId('form_id')->nullable()->constrained('medicine_forms')->nullOnDelete();
            $table->decimal('strength_value', 10, 2)->nullable();
            $table->string('strength_unit', 20)->nullable();
            $table->string('unit')->default('pcs');
            $table->foreignId('category_id')->nullable()->constrained('medicine_categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->integer('reorder_level')->default(10);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_prescription')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'name']);
        });

        Schema::create('medicine_batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number');
            $table->string('rc_number', 100)->nullable(); // Receipt/Challan number
            $table->date('expiry_date');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('remaining_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['branch_id', 'medicine_id', 'batch_number']);
            $table->index(['branch_id', 'medicine_id']);
            $table->index('expiry_date');
        });

        DB::statement('ALTER TABLE medicine_batches ADD CONSTRAINT chk_remaining_quantity_non_negative CHECK (remaining_quantity >= 0);');

        // =========================================================================
        // 7. CLINICAL
        // =========================================================================
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('queue_token');
            $table->enum('visit_type', ['routine', 'emergency', 'followup'])->default('routine');
            $table->enum('status', ['waiting', 'in_progress', 'completed', 'cancelled'])->default('waiting');
            $table->text('complaint')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at')->useCurrent();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('pulse')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('oxygen_saturation')->nullable();
            $table->string('oxygen_device')->nullable();
            $table->decimal('oxygen_flow_rate', 5, 2)->nullable();
            $table->integer('pain_scale')->default(0);
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('bmi', 4, 1)->nullable();
            $table->decimal('blood_glucose', 5, 2)->nullable();
            $table->integer('heart_rate')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'recorded_at']);
        });

        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('followup_date')->nullable();
            $table->boolean('is_chronic')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->enum('severity', ['mild', 'moderate', 'severe', 'critical'])->default('mild');
            $table->boolean('has_prescription')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // =========================================================================
        // 8. PRESCRIPTIONS (with enhanced frequency fields)
        // =========================================================================
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diagnosis_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->restrictOnDelete();
            $table->foreignId('prescribed_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('dosage')->nullable();
            $table->integer('frequency')->nullable();
            
            // Time-specific dosage fields
            $table->unsignedTinyInteger('morning')->default(0);
            $table->unsignedTinyInteger('evening')->default(0);
            $table->unsignedTinyInteger('night')->default(0);
            
            // Duration in days (renamed from duration)
            $table->string('days');
            
            $table->integer('quantity');
            $table->enum('status', ['pending', 'partially_dispensed', 'completed', 'cancelled'])->default('pending');
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('prescription_dispensations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity_dispensed');
            $table->foreignId('dispensed_by')->constrained('users');
            $table->timestamp('dispensed_at')->useCurrent();
            $table->foreignId('medicine_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // =========================================================================
        // 9. LABORATORY
        // =========================================================================
        Schema::create('lab_test_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('department')->nullable();
            $table->string('sample_type')->nullable();
            $table->timestamps();
        });

        Schema::create('lab_test_parameters', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('lab_test_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('group_name')->nullable();
            $table->string('unit')->nullable();
            $table->text('reference_range')->nullable();
            $table->decimal('min_range', 10, 3)->nullable();
            $table->decimal('max_range', 10, 3)->nullable();
            $table->string('input_type')->default('text');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('collection_date')->nullable();
            $table->dateTime('reporting_date')->nullable();
            $table->string('lab_number')->nullable();
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('device_name')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'status', 'priority']);
        });

        Schema::create('lab_order_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->unique(['lab_order_id', 'lab_test_type_id']);
        });

        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('lab_order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_parameter_id')->constrained()->cascadeOnDelete();
            $table->enum('value_type', ['numeric', 'text', 'boolean'])->default('numeric');
            $table->decimal('numeric_value', 15, 4)->nullable();
            $table->string('text_value')->nullable();
            $table->boolean('boolean_value')->nullable();
            $table->boolean('is_abnormal')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['lab_order_item_id', 'lab_test_parameter_id']);
        });

        Schema::create('lab_sample_infos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('lab_order_item_id')->constrained()->cascadeOnDelete();
            $table->dateTime('sample_collected_at')->nullable();
            $table->string('sample_id')->nullable();
            $table->string('sample_container')->nullable();
            $table->decimal('sample_quantity', 10, 2)->nullable();
            $table->string('sample_quantity_unit')->default('ml');
            $table->text('sample_condition')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });

        // =========================================================================
        // 10. APPOINTMENTS
        // =========================================================================
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->enum('type', ['physical', 'online'])->default('physical');
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->text('reason')->nullable();
            $table->string('online_meeting_link')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'scheduled_at']);
            $table->index(['doctor_id', 'scheduled_at']);
        });

        // =========================================================================
        // 11. INVENTORY LOGS
        // =========================================================================
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['initial', 'purchase', 'dispense', 'return', 'adjustment', 'transfer']);
            $table->integer('quantity');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->nullableMorphs('reference');
            $table->foreignId('prescription_dispensation_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('rc_number')->nullable();
            $table->foreignId('medicine_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('created_at');
        });

        // =========================================================================
        // 12. STOCK ALERTS
        // =========================================================================
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->enum('alert_type', ['low_stock', 'out_of_stock', 'expiring_soon']);
            $table->text('message');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });

        // =========================================================================
        // 13. AUDIT LOGS
        // =========================================================================
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });

        Schema::create('audit_log_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_log_id')->constrained()->cascadeOnDelete();
            $table->string('field_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();

            $table->index('audit_log_id');
        });

        // =========================================================================
        // 14. NOTIFICATIONS
        // =========================================================================
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('notifiable');
            $table->nullableMorphs('related');
            $table->string('type');
            $table->string('title');
            $table->text('body');
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // =========================================================================
        // 15. VIEWS
        // =========================================================================
        // Medicine stock value view for inventory reporting
        DB::statement("
            CREATE OR REPLACE VIEW medicine_stock_value AS
            SELECT
                m.id,
                m.name,
                m.reorder_level,
                COALESCE(SUM(mb.remaining_quantity), 0) as stock,
                CASE
                    WHEN COALESCE(SUM(mb.remaining_quantity), 0) = 0 THEN 'out_of_stock'
                    WHEN COALESCE(SUM(mb.remaining_quantity), 0) <= m.reorder_level THEN 'low_stock'
                    ELSE 'in_stock'
                END as stock_status
            FROM medicines m
            LEFT JOIN medicine_batches mb ON m.id = mb.medicine_id AND mb.is_active = 1
            WHERE m.deleted_at IS NULL
            GROUP BY m.id, m.name, m.reorder_level
        ");
    }

    public function down(): void
    {
        // Drop views first
        DB::statement("DROP VIEW IF EXISTS medicine_stock_value");
        
        // Drop tables in reverse order of creation (respecting foreign key constraints)
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_log_details');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('stock_alerts');
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('lab_sample_infos');
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('lab_order_items');
        Schema::dropIfExists('lab_orders');
        Schema::dropIfExists('lab_test_parameters');
        Schema::dropIfExists('lab_test_types');
        Schema::dropIfExists('prescription_dispensations');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('diagnoses');
        Schema::dropIfExists('vitals');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('medicine_batches');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('medicine_categories');
        Schema::dropIfExists('medicine_forms');
        Schema::dropIfExists('employee_details');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('branch_user');
        Schema::dropIfExists('users');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('offices');
    }
};