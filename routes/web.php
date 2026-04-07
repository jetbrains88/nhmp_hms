<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchSwitchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\ConsultationController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\DiagnosisController;
use App\Http\Controllers\Doctor\ExternalSpecialistController;
use App\Http\Controllers\Doctor\IllnessTagController;
use App\Http\Controllers\Doctor\LabOrderController as DoctorLabOrderController;
use App\Http\Controllers\Doctor\PrescriptionAbbreviationController as DoctorPrescriptionAbbreviationController;
use App\Http\Controllers\Doctor\PrescriptionController as DoctorPrescriptionController;
use App\Http\Controllers\Doctor\ReportController as DoctorReportController;
use App\Http\Controllers\Lab\DashboardController as LabDashboardController;
use App\Http\Controllers\Lab\OrderController as LabOrderController;
use App\Http\Controllers\Lab\ReportController as LabReportController;
use App\Http\Controllers\Lab\ResultController;
use App\Http\Controllers\Lab\TestParameterController;
use App\Http\Controllers\Lab\TestTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Nurse\DashboardController as NurseDashboardController;
use App\Http\Controllers\Nurse\VitalController;
use App\Http\Controllers\Pharmacy\DashboardController as PharmacyDashboardController;
use App\Http\Controllers\Pharmacy\InventoryController;
use App\Http\Controllers\Pharmacy\MedicineCategoryController;
use App\Http\Controllers\Pharmacy\MedicineController;
use App\Http\Controllers\Pharmacy\MedicineFormController;
use App\Http\Controllers\Pharmacy\PrescriptionController as PharmacyPrescriptionController;
use App\Http\Controllers\Pharmacy\ReportController as PharmacyReportController;
use App\Http\Controllers\Pharmacy\StockAlertController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reception\AppointmentController as ReceptionAppointmentController;
use App\Http\Controllers\Reception\DashboardController as ReceptionDashboardController;
use App\Http\Controllers\Reception\DesignationController as ReceptionDesignationController;
use App\Http\Controllers\Reception\OfficeController as ReceptionOfficeController;
use App\Http\Controllers\Reception\PatientController as ReceptionPatientController;
use App\Http\Controllers\Reception\QueueController;
use App\Http\Controllers\Reception\VisitController as ReceptionVisitController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES
// ============================================

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dev-login', function () {
    auth()->loginUsingId(3);
    session(['current_branch_id' => 1]);
    return redirect()->route('reception.dashboard');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Live Queue Display (No Auth Required)
Route::get('/live-queue/{branch?}', [QueueController::class, 'index'])->name('live.queue');

// ============================================
// PROTECTED ROUTES (All Authenticated Users)
// ============================================

Route::middleware(['auth'])->group(function () {
    // Branch Switching
    Route::post('/branch/switch/{branch}', [BranchSwitchController::class, 'switch'])
        ->name('branch.switch');

    // Main Dashboard (redirects to role-specific dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Add this after the existing API routes section in web.php
    Route::get('/api/user/branches', [App\Http\Controllers\Api\UserController::class, 'branches'])->name('api.user.branches');
    // Route::get('/user/branches', [App\Http\Controllers\UserBranchController::class, 'index'])->name('user.branches');
    // Route::get('/api/user/branches', [App\Http\Controllers\Api\UserController::class, 'branches']);

    // Add this route
    Route::get('/dashboard/realtime-data', [DashboardController::class, 'getRealtimeData'])->name('dashboard.realtime-data');
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])
            ->name('unread-count');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])
            ->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])
            ->name('mark-all-read');
        Route::get('/', [NotificationController::class, 'index'])->name('index');
    });

    // API Routes for AJAX (with permission checks)
    Route::prefix('api')->name('api.')->group(function () {
        // Office Hierarchy API (used in forms)
        Route::prefix('offices')->name('offices.')->group(function () {
            Route::get('/region/{regionId}/zones', function ($regionId) {
                $zones = \App\Models\Office::where('type', 'Zone')
                    ->where('parent_id', $regionId)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name']);
                return response()->json($zones);
            })->name('region.zones');

            Route::get('/zone/{zoneId}/sectors', function ($zoneId) {
                $sectors = \App\Models\Office::where('type', 'Sector')
                    ->where('parent_id', $zoneId)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name']);
                return response()->json($sectors);
            })->name('zone.sectors');

            Route::get('/sector/{sectorId}/plhqs', function ($sectorId) {
                $plhqs = \App\Models\Office::where('type', 'PLHQ')
                    ->where('parent_id', $sectorId)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name']);
                return response()->json($plhqs);
            })->name('sector.plhqs');

            Route::get('/plhq/{plhqId}/beats', function ($plhqId) {
                $beats = \App\Models\Office::where('type', 'Beat')
                    ->where('parent_id', $plhqId)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name']);
                return response()->json($beats);
            })->name('plhq.beats');
        });

        // Patient Search (used across modules)
        Route::get('/patients/search', [ReceptionPatientController::class, 'apiSearch'])
            ->name('patients.search');
        Route::get('/patients/{patient}/visits', [ReceptionPatientController::class, 'visitHistory'])
            ->name('patients.visits');

        // Medicine Search
        Route::get('/medicines/search', [MedicineController::class, 'apiSearch'])
            ->name('medicines.search');

        // Doctor Schedule
        Route::get('/doctors/{doctor}/available-slots', [AppointmentController::class, 'getAvailableSlots'])
            ->name('doctors.available-slots');

        // Global Search
        Route::get('/search', [App\Http\Controllers\SearchController::class, 'global'])->name('search');
    });
});

// ============================================
// ADMIN MODULE ROUTES
// ============================================

Route::middleware(['auth', 'branch.context', 'role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('users/stats', [UserController::class, 'stats'])->name('users.stats');
    Route::get('users/data', [UserController::class, 'data'])->name('users.data');
    Route::resource('users', UserController::class);
    // Inside the admin routes group, add:
    // Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');
    Route::get('/users/{user}/audit', [UserController::class, 'auditLog'])
        ->name('users.audit');
    Route::get('/users/{user}/permissions', [UserController::class, 'permissions'])
        ->name('users.permissions');
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])
        ->name('users.bulk-action');

    // Role Management
    Route::get('roles/stats', [RoleController::class, 'stats'])->name('roles.stats');
    Route::get('roles/data', [RoleController::class, 'data'])->name('roles.data');
    Route::post('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    Route::resource('roles', RoleController::class);
    Route::post('/roles/{role}/clone', [RoleController::class, 'cloneToBranch'])
        ->name('roles.clone');

    // Permission Management
    Route::get('permissions/stats', [PermissionController::class, 'stats'])->name('permissions.stats');
    Route::get('permissions/data', [PermissionController::class, 'data'])->name('permissions.data');
    Route::post('permissions/{permission}/toggle-status', [PermissionController::class, 'toggleStatus'])->name('permissions.toggle-status');
    Route::post('permissions/bulk-status', [PermissionController::class, 'bulkStatus'])->name('permissions.bulk-status');
    Route::post('permissions/bulk-destroy', [PermissionController::class, 'bulkDestroy'])->name('permissions.bulk-destroy');
    Route::resource('permissions', PermissionController::class)->except(['show']);

    // Branch Management
    Route::get('branches/stats', [BranchController::class, 'stats'])->name('branches.stats');
    Route::get('branches/data', [BranchController::class, 'data'])->name('branches.data');
    Route::resource('branches', BranchController::class);
    Route::post('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])
        ->name('branches.toggle-status');
    Route::get('/branches/{branch}/users', [BranchController::class, 'users'])
        ->name('branches.users');

    // Audit Logs
    Route::get('/audit-logs/stats', [AuditLogController::class, 'stats'])->name('audit.stats');
    Route::get('/audit-logs/data', [AuditLogController::class, 'data'])->name('audit.data');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit.export');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit.show');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/patients', [ReportController::class, 'patients'])->name('reports.patients');
    Route::get('/reports/visits', [ReportController::class, 'visits'])->name('reports.visits');
    Route::get('/reports/pharmacy', [ReportController::class, 'pharmacy'])->name('reports.pharmacy');
    Route::get('/reports/laboratory', [ReportController::class, 'laboratory'])->name('reports.laboratory');
    Route::get('/reports/appointments', [ReportController::class, 'appointments'])->name('reports.appointments');
    Route::get('/reports/audit', [ReportController::class, 'audit'])->name('reports.audit');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
});

// ============================================
// DOCTOR MODULE ROUTES
// ============================================

Route::middleware(['auth', 'branch.context', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

    // Consultations
    Route::get('/consultancy', [ConsultationController::class, 'index'])->name('consultancy');
    Route::get('/consultancy/data', [ConsultationController::class, 'data'])->name('consultancy.data');
    Route::get('/consultancy/stats', [ConsultationController::class, 'stats'])->name('consultancy.stats');
    Route::get('/consultancy/{visit}', [ConsultationController::class, 'show'])->name('consultancy.show');
    Route::post('/consultancy/{visit}/start', [ConsultationController::class, 'start'])->name('consultancy.start');
    Route::post('/consultancy/{visit}/complete', [ConsultationController::class, 'complete'])->name('consultancy.complete');
    Route::post('/consultancy/{visit}/cancel', [ConsultationController::class, 'cancel'])->name('consultancy.cancel');
    Route::get('/patient/{patient}/history-json', [ConsultationController::class, 'patientHistoryJson'])->name('patient.history.json');
    Route::post('/vitals/record', [ConsultationController::class, 'recordVitals'])->name('vitals.record');

    // E-Consultation
    Route::get('/e-consultancy', [ConsultationController::class, 'eConsultancy'])->name('e-consultancy');
    Route::post('/teleconsultation/start', [ConsultationController::class, 'startTeleconsultation'])
        ->name('teleconsultation.start');

    // Diagnoses
    Route::post('/diagnoses', [DiagnosisController::class, 'store'])->name('diagnoses.store');
    Route::get('/diagnoses/{diagnosis}', [DiagnosisController::class, 'show'])->name('diagnoses.show');
    Route::put('/diagnoses/{diagnosis}', [DiagnosisController::class, 'update'])->name('diagnoses.update');

    // Prescriptions
    Route::post('/prescriptions', [DoctorPrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions', [DoctorPrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/{prescription}', [DoctorPrescriptionController::class, 'show'])->name('prescriptions.show');

    // Lab Orders
    Route::resource('lab-orders', DoctorLabOrderController::class)->except(['edit', 'update']);
    Route::get('/lab-orders/{labOrder}/results', [DoctorLabOrderController::class, 'results'])
        ->name('lab-orders.results');

    // Appointments
    Route::resource('appointments', DoctorAppointmentController::class);

    // Patient History
    Route::get('/patients/{patient}/history', [DoctorDashboardController::class, 'patientHistory'])
        ->name('patients.history');

    // Reports
    Route::get('/reports', [DoctorReportController::class, 'index'])->name('reports');
    Route::get('/reports/export/{type}', [DoctorReportController::class, 'export'])->name('reports.export');

    // AJAX endpoints
    Route::get('/patients/search', [DoctorDashboardController::class, 'searchPatients'])->name('patients.search');
    Route::get('/office/{office}/patients', [DoctorDashboardController::class, 'getOfficePatients'])
        ->name('office.patients');

    // ── Doctor Setup / Configuration ─────────────────────────────────────────
    Route::prefix('setup')->name('setup.')->group(function () {

        // Illness Tags
        Route::get('/illness-tags', [IllnessTagController::class, 'index'])->name('illness-tags.index');
        Route::get('/illness-tags/data', [IllnessTagController::class, 'data'])->name('illness-tags.data');
        Route::get('/illness-tags/stats', [IllnessTagController::class, 'stats'])->name('illness-tags.stats');
        Route::post('/illness-tags', [IllnessTagController::class, 'store'])->name('illness-tags.store');
        Route::put('/illness-tags/{illnessTag}', [IllnessTagController::class, 'update'])->name('illness-tags.update');
        Route::patch('/illness-tags/{illnessTag}/toggle', [IllnessTagController::class, 'toggleStatus'])->name('illness-tags.toggle');
        Route::post('/illness-tags/bulk-status', [IllnessTagController::class, 'bulkStatus'])->name('illness-tags.bulk-status');
        Route::post('/illness-tags/bulk-destroy', [IllnessTagController::class, 'bulkDestroy'])->name('illness-tags.bulk-destroy');
        Route::delete('/illness-tags/{illnessTag}', [IllnessTagController::class, 'destroy'])->name('illness-tags.destroy');

        // External Specialists (branch-scoped)
        Route::get('/physicians', [ExternalSpecialistController::class, 'index'])->name('physicians.index');
        Route::get('/physicians/data', [ExternalSpecialistController::class, 'data'])->name('physicians.data');
        Route::get('/physicians/stats', [ExternalSpecialistController::class, 'stats'])->name('physicians.stats');
        Route::post('/physicians', [ExternalSpecialistController::class, 'store'])->name('physicians.store');
        Route::put('/physicians/{externalSpecialist}', [ExternalSpecialistController::class, 'update'])->name('physicians.update');
        Route::patch('/physicians/{externalSpecialist}/toggle', [ExternalSpecialistController::class, 'toggleStatus'])->name('physicians.toggle');
        Route::post('/physicians/bulk-status', [ExternalSpecialistController::class, 'bulkStatus'])->name('physicians.bulk-status');
        Route::post('/physicians/bulk-destroy', [ExternalSpecialistController::class, 'bulkDestroy'])->name('physicians.bulk-destroy');
        Route::delete('/physicians/{externalSpecialist}', [ExternalSpecialistController::class, 'destroy'])->name('physicians.destroy');

        // Prescription Abbreviations
        Route::get('/prescription-abbreviations', [DoctorPrescriptionAbbreviationController::class, 'index'])->name('prescription-abbreviations.index');
        Route::get('/prescription-abbreviations/data', [DoctorPrescriptionAbbreviationController::class, 'data'])->name('prescription-abbreviations.data');
        Route::get('/prescription-abbreviations/stats', [DoctorPrescriptionAbbreviationController::class, 'stats'])->name('prescription-abbreviations.stats');
        Route::get('/prescription-abbreviations/for-form', [DoctorPrescriptionAbbreviationController::class, 'forForm'])->name('prescription-abbreviations.for-form');
        Route::post('/prescription-abbreviations', [DoctorPrescriptionAbbreviationController::class, 'store'])->name('prescription-abbreviations.store');
        Route::put('/prescription-abbreviations/{prescriptionAbbreviation}', [DoctorPrescriptionAbbreviationController::class, 'update'])->name('prescription-abbreviations.update');
        Route::patch('/prescription-abbreviations/{prescriptionAbbreviation}/toggle', [DoctorPrescriptionAbbreviationController::class, 'toggleStatus'])->name('prescription-abbreviations.toggle');
        Route::post('/prescription-abbreviations/bulk-status', [DoctorPrescriptionAbbreviationController::class, 'bulkStatus'])->name('prescription-abbreviations.bulk-status');
        Route::post('/prescription-abbreviations/bulk-destroy', [DoctorPrescriptionAbbreviationController::class, 'bulkDestroy'])->name('prescription-abbreviations.bulk-destroy');
        Route::delete('/prescription-abbreviations/{prescriptionAbbreviation}', [DoctorPrescriptionAbbreviationController::class, 'destroy'])->name('prescription-abbreviations.destroy');
    });
});

// ============================================
// RECEPTION MODULE ROUTES
// ============================================

Route::middleware(['auth', 'branch.context', 'role:reception'])->prefix('reception')->name('reception.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ReceptionDashboardController::class, 'index'])->name('dashboard');
    Route::post('/check-patient-exists', [ReceptionDashboardController::class, 'checkPatientExists'])
        ->name('check-patient-exists');
    Route::get('/quick-search', [ReceptionDashboardController::class, 'quickSearch'])
        ->name('quick-search');

    // Offices & Designations

    Route::get('/offices', [ReceptionOfficeController::class, 'index'])->name('offices.index');
    Route::get('/offices/stats', [ReceptionOfficeController::class, 'stats'])->name('offices.stats');
    Route::get('/offices/data', [ReceptionOfficeController::class, 'data'])->name('offices.data');
    Route::post('/offices/bulk-destroy', [ReceptionOfficeController::class, 'bulkDestroy'])->name('offices.bulk-destroy');
    Route::post('/offices/{office}/toggle-status', [ReceptionOfficeController::class, 'toggleStatus'])->name('offices.toggle-status');
    Route::resource('offices', ReceptionOfficeController::class)->except(['show', 'create', 'edit', 'index']);

    Route::get('/designations', [ReceptionDesignationController::class, 'index'])->name('designations.index');
    Route::get('/designations/stats', [ReceptionDesignationController::class, 'stats'])->name('designations.stats');
    Route::get('/designations/data', [ReceptionDesignationController::class, 'data'])->name('designations.data');
    Route::post('/designations/bulk-destroy', [ReceptionDesignationController::class, 'bulkDestroy'])->name('designations.bulk-destroy');
    Route::resource('designations', ReceptionDesignationController::class)->except(['show', 'create', 'edit', 'index']);

    // Patient Management - static routes MUST come before resource() to avoid {patient} wildcard
    Route::get('/patients/template/download', [ReceptionPatientController::class, 'downloadTemplate'])
        ->name('patients.template');
    Route::post('/patients/bulk-upload', [ReceptionPatientController::class, 'bulkUpload'])
        ->name('patients.bulk-upload');
    Route::get('/patients/list', [ReceptionPatientController::class, 'list'])
        ->name('patients.list');
    Route::get('/patients/export', [ReceptionPatientController::class, 'export'])
        ->name('patients.export');
    Route::resource('patients', ReceptionPatientController::class);
    Route::get('/patients/{patient}/dependents/create', [ReceptionPatientController::class, 'createDependent'])
        ->name('patients.dependents.create');
    Route::post('/patients/{patient}/dependents', [ReceptionPatientController::class, 'storeDependent'])
        ->name('patients.dependents.store');
    Route::get('/patients/{patient}/medical-history', [ReceptionPatientController::class, 'medicalHistory'])
        ->name('patients.medical-history');
    Route::get('/patients/{patient}/visit-history', [ReceptionPatientController::class, 'visitHistory'])
        ->name('patients.visit-history');
    Route::get('/patients/{patient}/history', [ReceptionPatientController::class, 'history'])
        ->name('patients.history');

    // AJAX endpoints for live updates
    Route::get('/visits/waiting', [ReceptionVisitController::class, 'getWaitingVisits'])
        ->name('visits.waiting');
    Route::get('/visits/in-progress', [ReceptionVisitController::class, 'getInProgressVisits'])
        ->name('visits.in-progress');

    // Visit Management
    Route::resource('visits', ReceptionVisitController::class)->except(['index', 'destroy']);
    Route::post('/visits/{visit}/update-status', [ReceptionVisitController::class, 'updateStatus'])
        ->name('visits.update-status');
    Route::get('/visits/{visit}/vitals', [ReceptionVisitController::class, 'getVitals'])
        ->name('visits.vitals');
    Route::get('/visits/{visit}/print-token', [ReceptionVisitController::class, 'printToken'])
        ->name('visits.print-token');

    // Appointment Management
    Route::resource('appointments', ReceptionAppointmentController::class);
    Route::post('/appointments/{appointment}/status', [ReceptionAppointmentController::class, 'updateStatus'])
        ->name('appointments.update-status');
    Route::post('/appointments/{appointment}/cancel', [ReceptionAppointmentController::class, 'cancel'])
        ->name('appointments.cancel');

    // Queue Management
    Route::get('/queue', [QueueController::class, 'index'])->name('queue');
});

// ============================================
// PHARMACY MODULE ROUTES
// ============================================

Route::middleware(['auth', 'branch.context', 'role:pharmacy'])->prefix('pharmacy')->name('pharmacy.')->group(function () {
    // Dispense History
// Dispense History
    Route::get('/dispense-history', [PharmacyPrescriptionController::class, 'history'])->name('dispense.history'); // Changed from dispense_history
    Route::get('/dispense-history/data', [PharmacyPrescriptionController::class, 'getHistoryData'])->name('dispense.history.data'); // Changed from dispense_history_data

    // Dashboard
    Route::get('/dashboard', [PharmacyDashboardController::class, 'index'])->name('dashboard');

    // Prescriptions
    Route::get('/prescriptions', [PharmacyPrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/{prescription}', [PharmacyPrescriptionController::class, 'show'])
        ->name('prescriptions.show');
    Route::post('/prescriptions/{prescription}/dispense', [PharmacyPrescriptionController::class, 'dispense'])
        ->name('prescriptions.dispense');
    Route::get('/prescriptions/{prescription}/label', [PharmacyPrescriptionController::class, 'printLabel'])
        ->name('prescriptions.print');
    Route::get('/prescriptions/{prescription}/alternatives', [PharmacyPrescriptionController::class, 'alternativeMedicines'])
        ->name('prescriptions.alternatives');


    // Medicine Management
    Route::get('/medicines/api-list', [MedicineController::class, 'apiList'])->name('medicines.api-list');
    Route::resource('medicines', MedicineController::class);

    // Medicine Categories & Forms
    Route::get('/medicine-categories/stats', [MedicineCategoryController::class, 'stats'])->name('medicine-categories.stats');
    Route::get('/medicine-categories/data', [MedicineCategoryController::class, 'data'])->name('medicine-categories.data');
    Route::post('/medicine-categories/bulk-destroy', [MedicineCategoryController::class, 'bulkDestroy'])->name('medicine-categories.bulk-destroy');
    Route::post('/medicine-categories/bulk-status', [MedicineCategoryController::class, 'bulkStatus'])->name('medicine-categories.bulk-status');
    Route::post('/medicine-categories/{medicine_category}/toggle-status', [MedicineCategoryController::class, 'toggleStatus'])->name('medicine-categories.toggle-status');
    Route::resource('medicine-categories', MedicineCategoryController::class)->except(['show', 'create', 'edit']);

    Route::get('/medicine-forms/stats', [MedicineFormController::class, 'stats'])->name('medicine-forms.stats');
    Route::get('/medicine-forms/data', [MedicineFormController::class, 'data'])->name('medicine-forms.data');
    Route::post('/medicine-forms/bulk-destroy', [MedicineFormController::class, 'bulkDestroy'])->name('medicine-forms.bulk-destroy');
    Route::resource('medicine-forms', MedicineFormController::class)->except(['show', 'create', 'edit']);

    // Inventory Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/list', [InventoryController::class, 'inventoryList'])->name('inventory.list');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::get('/inventory/{medicine}/stock', [InventoryController::class, 'medicineStock'])->name('inventory.stock');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/batch/{batch}', [InventoryController::class, 'showBatch'])->name('inventory.batch');
    Route::post('/inventory/batch/{batch}/add-stock', [InventoryController::class, 'addStock'])
        ->name('inventory.add-stock');
    Route::get('/inventory/batch/{batch}/adjust', [InventoryController::class, 'adjustForm'])
        ->name('inventory.adjust-form');
    Route::post('/inventory/batch/{batch}/adjust', [InventoryController::class, 'adjust'])
        ->name('inventory.adjust');
    Route::get('/inventory/transfer/{batch}', [InventoryController::class, 'transferForm'])
        ->name('inventory.transfer-form');
    Route::post('/inventory/transfer', [InventoryController::class, 'transfer'])
        ->name('inventory.transfer');

    // Stock Alerts
    Route::get('/alerts', [StockAlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/data', [StockAlertController::class, 'getAlertsData'])->name('alerts.data');
    Route::get('/alerts/stats', [StockAlertController::class, 'getStats'])->name('alerts.stats');
    Route::post('/alerts/bulk/resolve', [StockAlertController::class, 'bulkResolve'])->name('alerts.bulk-resolve');
    Route::post('/alerts/resolve-all', [StockAlertController::class, 'resolveAll'])->name('alerts.resolve-all');
    Route::post('/alerts/{alert}/resolve', [StockAlertController::class, 'resolve'])->name('alerts.resolve');

    // Reports
    Route::get('/reports', [PharmacyReportController::class, 'index'])->name('reports');
    Route::get('/reports/export/{type}', [PharmacyReportController::class, 'export'])->name('reports.export');

    // AJAX endpoints
    Route::get('/medicines/search', [MedicineController::class, 'search'])->name('medicines.search');
    Route::get('/inventory/expiring', [InventoryController::class, 'expiringSoon'])->name('inventory.expiring');
});

// ============================================
// LABORATORY MODULE ROUTES
// ============================================

Route::middleware(['auth', 'branch.context', 'role:lab'])->prefix('lab')->name('lab.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [LabDashboardController::class, 'index'])->name('dashboard');

    // Lab Orders
    Route::resource('orders', LabOrderController::class);
    Route::get('/orders/{labOrder}/print', [LabOrderController::class, 'print'])->name('orders.print');
    Route::post('/orders/{labOrder}/verify', [LabOrderController::class, 'verify'])->name('orders.verify');

    // Order Items
    Route::post('/orders/items/{item}/start', [LabOrderController::class, 'startItem'])->name('orders.start-item');

    // Results Entry
    Route::get('/results/create/{orderItem}', [ResultController::class, 'create'])->name('results.create');
    Route::post('/results/{orderItem}', [ResultController::class, 'store'])->name('results.store');
    Route::get('/results/edit/{orderItem}', [ResultController::class, 'edit'])->name('results.edit');
    Route::put('/results/{orderItem}', [ResultController::class, 'update'])->name('results.update');

    // Reports — static routes MUST come before parameterized {labReport} routes
    Route::get('/reports', [LabReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/data', [LabReportController::class, 'getReportsData'])->name('reports.data');
    Route::get('/reports/statistics', [LabReportController::class, 'statistics'])->name('reports.statistics');
    Route::get('/reports/export/{type}', [LabReportController::class, 'export'])->name('reports.export');

    // Parameterized routes
    Route::get('/reports/{labReport}', [LabReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{labReport}/edit', [LabReportController::class, 'edit'])->name('reports.edit');
    Route::get('/reports/{labReport}/pdf', [LabReportController::class, 'pdf'])->name('reports.pdf');
    Route::get('/reports/{labReport}/print', [LabReportController::class, 'pdf'])->name('reports.print');
    Route::get('/reports/{labReport}/download-pdf', [LabReportController::class, 'pdf'])->name('reports.download-pdf');
    Route::put('/reports/{labReport}', [LabReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{labReport}', [LabReportController::class, 'destroy'])->name('reports.destroy');
    Route::post('/reports/{labReport}/results', [LabReportController::class, 'submitResults'])->name('reports.results');
    Route::put('/reports/{labReport}/status', [LabReportController::class, 'updateStatus'])->name('reports.status');
    Route::post('/reports/{labReport}/upload', [LabReportController::class, 'uploadFile'])->name('reports.upload');
    Route::post('/reports/{labReport}/verify', [LabReportController::class, 'verify'])->name('reports.verify');
    Route::post('/reports/{labReport}/notify-doctor', [LabReportController::class, 'notifyDoctor'])->name('reports.notify-doctor');

    // AJAX endpoints
    Route::get('/pending', [LabOrderController::class, 'pending'])->name('pending');
    Route::get('/statistics', [LabDashboardController::class, 'statistics'])->name('statistics');

    // Test Types & Parameters
    Route::get('/test-types/stats', [TestTypeController::class, 'stats'])->name('test-types.stats');
    Route::get('/test-types/data', [TestTypeController::class, 'data'])->name('test-types.data');
    Route::post('/test-types/bulk-destroy', [TestTypeController::class, 'bulkDestroy'])->name('test-types.bulk-destroy');
    Route::resource('test-types', TestTypeController::class)->except(['show', 'create', 'edit']);

    Route::get('/test-parameters/stats', [TestParameterController::class, 'stats'])->name('test-parameters.stats');
    Route::get('/test-parameters/data', [TestParameterController::class, 'data'])->name('test-parameters.data');
    Route::post('/test-parameters/bulk-destroy', [TestParameterController::class, 'bulkDestroy'])->name('test-parameters.bulk-destroy');
    Route::resource('test-parameters', TestParameterController::class)->except(['show', 'create', 'edit']);
});

// ============================================
// NURSE MODULE ROUTES
// ============================================

Route::middleware(['auth', 'branch.context', 'role:nurse'])->prefix('nurse')->name('nurse.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [NurseDashboardController::class, 'index'])->name('dashboard');

    // Vitals
    Route::get('/vitals/create', [VitalController::class, 'create'])->name('vitals.create');
    Route::post('/vitals', [VitalController::class, 'store'])->name('vitals.store');
    Route::get('/vitals/patient/{patient}', [VitalController::class, 'history'])->name('vitals.history');
    Route::get('/vitals/{vital}/edit', [VitalController::class, 'edit'])->name('vitals.edit');
    Route::put('/vitals/{vital}', [VitalController::class, 'update'])->name('vitals.update');

    // Patient List
    Route::get('/patients', [NurseDashboardController::class, 'patients'])->name('patients');
    Route::get('/patients/{patient}', [NurseDashboardController::class, 'patientDetail'])->name('patients.show');
});

// ============================================
// APPOINTMENTS (Accessible by multiple roles)
// ============================================

Route::middleware(['auth'])->prefix('appointments')->name('appointments.')->group(function () {
    Route::get('/', [AppointmentController::class, 'index'])->name('index');
    Route::get('/create', [AppointmentController::class, 'create'])->name('create');
    Route::post('/', [AppointmentController::class, 'store'])->name('store');
    Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
    Route::post('/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('update-status');
    Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
});

// ============================================
// PRINT ROUTES (Multiple roles)
// ============================================

Route::middleware(['auth'])->prefix('print')->name('print.')->group(function () {
    Route::get('/prescription/{prescription}', [PrintController::class, 'prescription'])
        ->name('prescription')
        ->middleware('permission:print_prescriptions');

    Route::get('/lab-report/{labOrder}', [PrintController::class, 'labReport'])
        ->name('lab-report')
        ->middleware('permission:print_lab_reports');

    Route::get('/visit-token/{visit}', [PrintController::class, 'visitToken'])
        ->name('visit-token')
        ->middleware('permission:view_visits');
});

// ============================================
// LEGACY REDIRECTS (Backward Compatibility)
// ============================================

// Redirect old URLs to new ones
Route::middleware(['auth'])->get('/doctor', function () {
    return redirect()->route('doctor.dashboard');
});

Route::middleware(['auth'])->get('/pharmacy', function () {
    return redirect()->route('pharmacy.dashboard');
});

Route::middleware(['auth'])->get('/reception', function () {
    return redirect()->route('reception.dashboard');
});

Route::middleware(['auth'])->get('/laboratory', function () {
    return redirect()->route('lab.dashboard');
});

Route::middleware(['auth'])->get('/nurse', function () {
    return redirect()->route('nurse.dashboard');
});
// ============================================
// PROFILE ROUTES
// ============================================
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::get('/change-password', [ProfileController::class, 'changePasswordForm'])->name('change-password');
    Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('change-password.update'); // Fixed
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::put('/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
    Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
});
