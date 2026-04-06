<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display reports dashboard.
     */
    public function index()
    {
        return view('doctor.reports.index');
    }

    /**
     * Export report.
     */
    public function export(Request $request, $type)
    {
        $filters = $request->all();
        $filters['doctor_id'] = auth()->id();

        $data = [];

        switch ($type) {
            case 'prescriptions':
                $data = $this->getPrescriptionsReport($filters);
                break;
            case 'diagnoses':
                $data = $this->getDiagnosesReport($filters);
                break;
            case 'patients':
                $data = $this->getPatientsReport($filters);
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type.');
        }

        $csv = $this->reportService->toCsv($data['rows'], $data['headers']);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $type . '-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    protected function getPrescriptionsReport($filters)
    {
        // Implementation for prescriptions report
        return [
            'headers' => ['Date', 'Patient', 'Medicine', 'Dosage', 'Quantity', 'Status'],
            'rows' => [],
        ];
    }

    protected function getDiagnosesReport($filters)
    {
        // Implementation for diagnoses report
        return [
            'headers' => ['Date', 'Patient', 'Diagnosis', 'Severity', 'Follow-up'],
            'rows' => [],
        ];
    }

    protected function getPatientsReport($filters)
    {
        // Implementation for patients report
        return [
            'headers' => ['Patient Name', 'EMRN', 'Age', 'Gender', 'Last Visit', 'Total Visits'],
            'rows' => [],
        ];
    }
}
