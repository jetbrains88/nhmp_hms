<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display reports dashboard
     */
    public function index()
    {
        $branches = Branch::where('is_active', true)->get();
        $doctors = User::whereHas('roles', fn($q) => $q->where('name', 'doctor'))->get();

        return view('admin.reports.index', compact('branches', 'doctors'));
    }

    /**
     * Generate patient report
     */
    public function patients(Request $request)
    {
        $filters = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $stats = $this->reportService->patientStats($filters);

        if ($request->has('export')) {
            $csv = $this->reportService->toCsv(
                $stats['registrations_by_month']->toArray(),
                ['Year', 'Month', 'Registrations']
            );

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="patient-report-' . now()->format('Y-m-d') . '.csv"',
            ]);
        }

        return view('admin.reports.patients', compact('stats'));
    }

    /**
     * Generate visit report
     */
    public function visits(Request $request)
    {
        $filters = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $stats = $this->reportService->visitStats($filters);

        return view('admin.reports.visits', compact('stats'));
    }

    /**
     * Generate pharmacy report
     */
    public function pharmacy(Request $request)
    {
        $filters = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $stats = $this->reportService->pharmacyStats($filters);

        return view('admin.reports.pharmacy', compact('stats'));
    }

    /**
     * Generate laboratory report
     */
    public function laboratory(Request $request)
    {
        $filters = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $stats = $this->reportService->labStats($filters);

        return view('admin.reports.laboratory', compact('stats'));
    }

    /**
     * Generate appointment report
     */
    public function appointments(Request $request)
    {
        $filters = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'doctor_id' => 'nullable|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $stats = $this->reportService->appointmentStats($filters);

        return view('admin.reports.appointments', compact('stats'));
    }

    /**
     * Generate audit report
     */
    public function audit(Request $request)
    {
        $filters = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'user_id' => 'nullable|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $stats = $this->reportService->auditStats($filters);

        return view('admin.reports.audit', compact('stats'));
    }
}
