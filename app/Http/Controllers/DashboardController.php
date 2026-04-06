<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();

            if (!$user->roles || $user->roles->isEmpty()) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your account has no role assigned. Contact administrator.');
            }

            return $next($request);
        });
    }


    public function index()
    {
        $monthlyData = [
            ['month' => 'Jan', 'medicines' => 145, 'lab_reports' => 382],
            ['month' => 'Feb', 'medicines' => 738, 'lab_reports' => 565],
            ['month' => 'Mar', 'medicines' => 252, 'lab_reports' => 391],
            ['month' => 'Apr', 'medicines' => 630, 'lab_reports' => 484],
            ['month' => 'May', 'medicines' => 525, 'lab_reports' => 338],
            ['month' => 'Jun', 'medicines' => 515, 'lab_reports' => 622],
            ['month' => 'Jul', 'medicines' => 622, 'lab_reports' => 831],
            ['month' => 'Aug', 'medicines' => 728, 'lab_reports' => 440],
            ['month' => 'Sep', 'medicines' => 641, 'lab_reports' => 355],
            ['month' => 'Oct', 'medicines' => 555, 'lab_reports' => 678],
            ['month' => 'Nov', 'medicines' => 862, 'lab_reports' => 795],
            ['month' => 'Dec', 'medicines' => 770, 'lab_reports' => 610],
        ];

        $monthlyPerformance = [
            ['month' => 'Jan', 'visits' => 1050, 'medicines' => 1415, 'lab_reports' => 1665],
            ['month' => 'Feb', 'visits' => 980, 'medicines' => 1320, 'lab_reports' => 1520],
            ['month' => 'Mar', 'visits' => 1100, 'medicines' => 1540, 'lab_reports' => 1710],
            ['month' => 'Apr', 'visits' => 1150, 'medicines' => 1650, 'lab_reports' => 1035],
            ['month' => 'May', 'visits' => 1020, 'medicines' => 1428, 'lab_reports' => 1122],
            ['month' => 'Jun', 'visits' => 950, 'medicines' => 1310, 'lab_reports' => 855],
            ['month' => 'Jul', 'visits' => 1080, 'medicines' => 1512, 'lab_reports' => 1188],
            ['month' => 'Aug', 'visits' => 1200, 'medicines' => 1740, 'lab_reports' => 1440],
            ['month' => 'Sep', 'visits' => 1180, 'medicines' => 1652, 'lab_reports' => 1062],
            ['month' => 'Oct', 'visits' => 1250, 'medicines' => 1810, 'lab_reports' => 1375],
            ['month' => 'Nov', 'visits' => 1300, 'medicines' => 1885, 'lab_reports' => 1560],
            ['month' => 'Dec', 'visits' => 1350, 'medicines' => 2025, 'lab_reports' => 1215],
        ];

        $data = [
            'stats' => $this->getDashboardStats(),
            'monthlyPerformance' => $monthlyPerformance,
            'monthlyData' => $monthlyData,
            'medicineStockByCategory' => $this->getMedicineStockByCategory(),
            'labReportsByType' => $this->getLabReportsByType(),
            'topMedicines' => $this->getMostDispensedMedicines(),
            'patientsByOffice' => $this->getPatientsByOffice(),
            'lowStockMedicines' => $this->getLowStockMedicines(),
            'outOfStockMedicines' => $this->getOutOfStockMedicines(),
            'recentVisits' => $this->getRecentVisits(),
            'pendingPrescriptions' => Prescription::where('status', 'pending')->count(),
            'processingLabReports' => LabOrder::where('status', 'processing')->count(),
        ];

        // Add counts for low stock and out of stock medicines
        $data['lowStockCount'] = $data['lowStockMedicines']->count();
        $data['outOfStockCount'] = $data['outOfStockMedicines']->count();

        // Add stock percentage calculations
        $totalMedicines = Medicine::count();
        $data['inStockCount'] = $totalMedicines - $data['outOfStockCount'] - $data['lowStockCount'];
        $data['totalMedicines'] = $totalMedicines;
        $data['outOfStockPercentage'] = $totalMedicines > 0 ? round(($data['outOfStockCount'] / $totalMedicines) * 100) : 0;
        $data['lowStockPercentage'] = $totalMedicines > 0 ? round(($data['lowStockCount'] / $totalMedicines) * 100) : 0;
        $data['inStockPercentage'] = $totalMedicines > 0 ? (100 - $data['outOfStockPercentage'] - $data['lowStockPercentage']) : 0;

        // Calculate critical stock (below 5 units) - with error handling
        try {
            $data['criticalStockCount'] = DB::table('medicine_stock_value')
                ->where('stock', '>', 0)
                ->where('stock', '<=', 5)
                ->count();
            $data['criticalStockPercentage'] = $totalMedicines > 0 ? round(($data['criticalStockCount'] / $totalMedicines) * 100) : 0;
        } catch (\Exception $e) {
            // Fallback if view doesn't exist
            $data['criticalStockCount'] = DB::table('medicine_batches')
                ->where('remaining_quantity', '>', 0)
                ->where('remaining_quantity', '<=', 5)
                ->count(DB::raw('DISTINCT medicine_id'));
            $data['criticalStockPercentage'] = $totalMedicines > 0 ? round(($data['criticalStockCount'] / $totalMedicines) * 100) : 0;
        }

        Log::info("", $data);
        return view('dashboard', $data);
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'totalPatients' => Patient::count(),
            'totalVisits' => Visit::count(),
            'todayVisits' => Visit::whereDate('created_at', today())->count(),
            'pendingVisits' => Visit::where('status', 'waiting')->count(),
            'medicinesDispensed' => Prescription::where('status', 'dispensed')->sum('quantity'),
            'labReportsCompleted' => LabOrder::where('status', 'completed')->count(),
            'totalRevenue' => Prescription::where('prescriptions.status', 'dispensed')
                ->join('prescription_dispensations', 'prescriptions.id', '=', 'prescription_dispensations.prescription_id')
                ->join('medicine_batches', 'prescription_dispensations.medicine_batch_id', '=', 'medicine_batches.id')
                ->selectRaw('SUM(prescription_dispensations.quantity_dispensed * medicine_batches.sale_price) as total')
                ->value('total') ?? 0,
        ];
    }

    /**
     * Get most dispensed medicines
     */
    private function getMostDispensedMedicines()
    {
        return Prescription::select(
            'medicines.id',
            'medicines.name',
            DB::raw('SUM(prescription_dispensations.quantity_dispensed) as total_dispensed'),
            DB::raw('SUM(prescription_dispensations.quantity_dispensed * medicine_batches.sale_price) as revenue')
        )
            ->join('medicines', 'prescriptions.medicine_id', '=', 'medicines.id')
            ->join('prescription_dispensations', 'prescriptions.id', '=', 'prescription_dispensations.prescription_id')
            ->join('medicine_batches', 'prescription_dispensations.medicine_batch_id', '=', 'medicine_batches.id')
            ->where('prescriptions.status', 'dispensed')
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('total_dispensed')
            ->limit(10)
            ->get();
    }
    /**
     * Get patients distribution by office/region
     */
    private function getPatientsByOffice()
    {
        return DB::table('offices')
            ->join('employee_details', 'offices.id', '=', 'employee_details.office_id')
            ->join('patients', 'employee_details.patient_id', '=', 'patients.id')
            ->select('offices.name', DB::raw('COUNT(patients.id) as patient_count'))
            ->groupBy('offices.id', 'offices.name')
            ->orderByDesc('patient_count')
            ->limit(8)
            ->get();
    }


    /**
     * Get low stock medicines
     */
    private function getLowStockMedicines()
    {
        // Check if the view exists, if not, use a fallback query
        try {
            return DB::table('medicine_stock_value')
                ->where('stock', '>', 0)
                ->where('stock_status', 'low_stock')
                ->select('name', 'stock', 'reorder_level')
                ->orderBy('stock')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // Fallback query if view doesn't exist
            return collect([]);
        }
    }

    /**
     * Get out of stock medicines
     */
    private function getOutOfStockMedicines()
    {
        try {
            return DB::table('medicine_stock_value')
                ->where('stock', 0)
                ->select('name', 'stock')
                ->orderBy('name')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // Fallback query if view doesn't exist
            return DB::table('medicines')
                ->join('medicine_batches', 'medicines.id', '=', 'medicine_batches.medicine_id')
                ->where('medicine_batches.remaining_quantity', 0)
                ->select('medicines.name', DB::raw('0 as stock'))
                ->orderBy('medicines.name')
                ->limit(10)
                ->get();
        }
    }
    /**
     * Get recent visits - FIXED: Remove appends to avoid the error
     */
    private function getRecentVisits()
    {
        return Visit::with(['patient', 'doctor'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($visit) {
                // Manually format the data instead of using appends
                return [
                    'id' => $visit->id,
                    'patient' => [
                        'name' => $visit->patient->name ?? 'N/A',
                    ],
                    'doctor' => [
                        'name' => $visit->doctor->name ?? 'N/A',
                    ],
                    'status' => $visit->status,
                    'created_at' => $visit->created_at,
                    'queue_token' => $visit->queue_token,
                    'visit_type' => $visit->visit_type,
                ];
            });
    }

    public function analysis()
    {

        $monthlyData = [
            ['month' => 'Jan', 'medicines' => 845, 'lab_reports' => 382],
            ['month' => 'Feb', 'medicines' => 938, 'lab_reports' => 565],
            ['month' => 'Mar', 'medicines' => 552, 'lab_reports' => 391],
            ['month' => 'Apr', 'medicines' => 630, 'lab_reports' => 484],
            ['month' => 'May', 'medicines' => 525, 'lab_reports' => 338],
            ['month' => 'Jun', 'medicines' => 515, 'lab_reports' => 622],
            ['month' => 'Jul', 'medicines' => 822, 'lab_reports' => 631],
            ['month' => 'Aug', 'medicines' => 728, 'lab_reports' => 440],
            ['month' => 'Sep', 'medicines' => 641, 'lab_reports' => 355],
            ['month' => 'Oct', 'medicines' => 555, 'lab_reports' => 678],
            ['month' => 'Nov', 'medicines' => 862, 'lab_reports' => 795],
            ['month' => 'Dec', 'medicines' => 770, 'lab_reports' => 610],
        ];

        $monthlyPerformance = [
            ['month' => 'Jan', 'visits' => 1050, 'medicines' => 1415, 'lab_reports' => 1665],
            ['month' => 'Feb', 'visits' => 980, 'medicines' => 1320, 'lab_reports' => 1520],
            ['month' => 'Mar', 'visits' => 1100, 'medicines' => 1540, 'lab_reports' => 1710],
            ['month' => 'Apr', 'visits' => 1150, 'medicines' => 1650, 'lab_reports' => 1035],
            ['month' => 'May', 'visits' => 1020, 'medicines' => 1428, 'lab_reports' => 1122],
            ['month' => 'Jun', 'visits' => 950, 'medicines' => 1310, 'lab_reports' => 855],
            ['month' => 'Jul', 'visits' => 1080, 'medicines' => 1512, 'lab_reports' => 1188],
            ['month' => 'Aug', 'visits' => 1200, 'medicines' => 1740, 'lab_reports' => 1440],
            ['month' => 'Sep', 'visits' => 1180, 'medicines' => 1652, 'lab_reports' => 1062],
            ['month' => 'Oct', 'visits' => 1250, 'medicines' => 1810, 'lab_reports' => 1375],
            ['month' => 'Nov', 'visits' => 1300, 'medicines' => 1885, 'lab_reports' => 1560],
            ['month' => 'Dec', 'visits' => 1350, 'medicines' => 2025, 'lab_reports' => 1215],
        ];
        $data = [
            'stats' => $this->getDashboardStats(), //            'monthlyData' => $this->getMonthlyAnalytics(),
            //            'monthlyPerformance' => $this->getMonthlyPerformanceData(),
            'monthlyPerformance' => $monthlyPerformance,
            'monthlyData' => $monthlyData,
            'topMedicines' => $this->getMostDispensedMedicines(),
            'patientsByOffice' => $this->getPatientsByOffice(),
            'lowStockMedicines' => $this->getLowStockMedicines(),
            'outOfStockMedicines' => $this->getOutOfStockMedicines(),
            'recentVisits' => $this->getRecentVisits(),
            'pendingPrescriptions' => Prescription::where('status', 'pending')->count(),
            'processingLabReports' => LabOrder::where('status', 'processing')->count(),
        ];

        // Add counts for low stock and out of stock medicines
        $data['lowStockCount'] = $data['lowStockMedicines']->count();
        $data['outOfStockCount'] = $data['outOfStockMedicines']->count();

        // Add stock percentage calculations
        $totalMedicines = Medicine::count();
        $data['inStockCount'] = $totalMedicines - $data['outOfStockCount'] - $data['lowStockCount'];
        $data['totalMedicines'] = $totalMedicines;
        $data['outOfStockPercentage'] = $totalMedicines > 0 ? round(($data['outOfStockCount'] / $totalMedicines) * 100) : 0;
        $data['lowStockPercentage'] = $totalMedicines > 0 ? round(($data['lowStockCount'] / $totalMedicines) * 100) : 0;
        $data['inStockPercentage'] = $totalMedicines > 0 ? (100 - $data['outOfStockPercentage'] - $data['lowStockPercentage']) : 0;

        // Calculate critical stock (below 5 units)
        $data['criticalStockCount'] = DB::table('medicine_stock_value')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->count(); //        $data['criticalStockPercentage'] = $totalMedicines > 0 ? round(($data['criticalStockCount'] / $totalMedicines) * 100) : 0;
        $data['criticalStockPercentage'] = 100; //temp data


        Log::info("", $data);
        return view('g_analytics', $data);
    }

    public function analytics()
    {

        $monthlyData = [
            ['month' => 'Jan', 'medicines' => 845, 'lab_reports' => 382],
            ['month' => 'Feb', 'medicines' => 938, 'lab_reports' => 565],
            ['month' => 'Mar', 'medicines' => 552, 'lab_reports' => 391],
            ['month' => 'Apr', 'medicines' => 630, 'lab_reports' => 484],
            ['month' => 'May', 'medicines' => 525, 'lab_reports' => 338],
            ['month' => 'Jun', 'medicines' => 515, 'lab_reports' => 622],
            ['month' => 'Jul', 'medicines' => 822, 'lab_reports' => 631],
            ['month' => 'Aug', 'medicines' => 728, 'lab_reports' => 440],
            ['month' => 'Sep', 'medicines' => 641, 'lab_reports' => 355],
            ['month' => 'Oct', 'medicines' => 555, 'lab_reports' => 678],
            ['month' => 'Nov', 'medicines' => 862, 'lab_reports' => 795],
            ['month' => 'Dec', 'medicines' => 770, 'lab_reports' => 610],
        ];

        $monthlyPerformance = [
            ['month' => 'Jan', 'visits' => 1050, 'medicines' => 1415, 'lab_reports' => 1665],
            ['month' => 'Feb', 'visits' => 980, 'medicines' => 1320, 'lab_reports' => 1520],
            ['month' => 'Mar', 'visits' => 1100, 'medicines' => 1540, 'lab_reports' => 1710],
            ['month' => 'Apr', 'visits' => 1150, 'medicines' => 1650, 'lab_reports' => 1035],
            ['month' => 'May', 'visits' => 1020, 'medicines' => 1428, 'lab_reports' => 1122],
            ['month' => 'Jun', 'visits' => 950, 'medicines' => 1310, 'lab_reports' => 855],
            ['month' => 'Jul', 'visits' => 1080, 'medicines' => 1512, 'lab_reports' => 1188],
            ['month' => 'Aug', 'visits' => 1200, 'medicines' => 1740, 'lab_reports' => 1440],
            ['month' => 'Sep', 'visits' => 1180, 'medicines' => 1652, 'lab_reports' => 1062],
            ['month' => 'Oct', 'visits' => 1250, 'medicines' => 1810, 'lab_reports' => 1375],
            ['month' => 'Nov', 'visits' => 1300, 'medicines' => 1885, 'lab_reports' => 1560],
            ['month' => 'Dec', 'visits' => 1350, 'medicines' => 2025, 'lab_reports' => 1215],
        ];
        $data = [
            'stats' => $this->getDashboardStats(), //            'monthlyData' => $this->getMonthlyAnalytics(),
            //            'monthlyPerformance' => $this->getMonthlyPerformanceData(),
            'monthlyPerformance' => $monthlyPerformance,
            'monthlyData' => $monthlyData,
            'topMedicines' => $this->getMostDispensedMedicines(),
            'patientsByOffice' => $this->getPatientsByOffice(),
            'lowStockMedicines' => $this->getLowStockMedicines(),
            'outOfStockMedicines' => $this->getOutOfStockMedicines(),
            'recentVisits' => $this->getRecentVisits(),
            'pendingPrescriptions' => Prescription::where('status', 'pending')->count(),
            'processingLabReports' => LabOrder::where('status', 'processing')->count(),
        ];

        // Add counts for low stock and out of stock medicines
        $data['lowStockCount'] = $data['lowStockMedicines']->count();
        $data['outOfStockCount'] = $data['outOfStockMedicines']->count();

        // Add stock percentage calculations
        $totalMedicines = Medicine::count();
        $data['inStockCount'] = $totalMedicines - $data['outOfStockCount'] - $data['lowStockCount'];
        $data['totalMedicines'] = $totalMedicines;
        $data['outOfStockPercentage'] = $totalMedicines > 0 ? round(($data['outOfStockCount'] / $totalMedicines) * 100) : 0;
        $data['lowStockPercentage'] = $totalMedicines > 0 ? round(($data['lowStockCount'] / $totalMedicines) * 100) : 0;
        $data['inStockPercentage'] = $totalMedicines > 0 ? (100 - $data['outOfStockPercentage'] - $data['lowStockPercentage']) : 0;

        // Calculate critical stock (below 5 units)
        $data['criticalStockCount'] = DB::table('medicine_stock_value')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->count(); //        $data['criticalStockPercentage'] = $totalMedicines > 0 ? round(($data['criticalStockCount'] / $totalMedicines) * 100) : 0;
        $data['criticalStockPercentage'] = 100; //temp data


        Log::info("", $data);
        return view('analytics', $data);
    }

    /**
     * Get real-time dashboard data for AJAX updates
     */

    /**
     * Get real-time dashboard data for AJAX updates
     */
    public function getRealtimeData()
    {
        $stats = $this->getDashboardStats();

        $data = [
            'stats' => $stats,
            'pendingPrescriptions' => Prescription::where('status', 'pending')->count(),
            'processingLabReports' => LabOrder::where('status', 'processing')->count(),
            'lowStockCount' => DB::table('medicine_stock_value')
                ->where('stock_status', 'low_stock')
                ->count(),
            'outOfStockCount' => DB::table('medicine_stock_value')->where('stock', 0)->count(),
            'totalMedicines' => Medicine::count(),
        ];

        return response()->json($data);
    } //    public function getRealtimeData()
//    {
//        $data = [
//            'stats' => $this->getDashboardStats(),
//            'lowStockCount' => Medicine::where('stock', '>', 0)
//                ->whereRaw('stock <= reorder_level')
//                ->count(),
//            'outOfStockCount' => Medicine::where('stock', 0)->count(),
//            'pendingPrescriptions' => Prescription::where('status', 'pending')->count(),
//            'processingLabReports' => LabReport::where('status', 'processing')->count(),
//        ];
//
//        return response()->json($data);
//    }

    /**
     * Get chart data for dashboard - FIXED: Ensure safe serialization
     */
    public function getChartData(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);

        // Get monthly data for selected year
        $monthlyAnalytics = $this->getMonthlyAnalyticsForYear($year);

        // Get patients by office
        $patientsByOffice = $this->getPatientsByOffice()->map(function ($item) {
            return [
                'name' => $item->name,
                'patient_count' => $item->patient_count,
            ];
        });

        // Get medicines distribution
        $medicinesDistribution = $this->getMedicinesDistribution();

        return response()->json([
            'monthlyAnalytics' => $monthlyAnalytics,
            'patientsByOffice' => $patientsByOffice,
            'medicinesDistribution' => $medicinesDistribution,
        ]);
    }

    /**
     * Get monthly analytics for specific year
     */
    private function getMonthlyAnalyticsForYear($year)
    {
        // Medicines dispensed by month
        $medicinesData = Prescription::select(
            DB::raw('MONTH(prescription_dispensations.dispensed_at) as month'),
            DB::raw('SUM(prescription_dispensations.quantity_dispensed) as total')
        )
            ->join('prescription_dispensations', 'prescriptions.id', '=', 'prescription_dispensations.prescription_id')
            ->whereYear('prescription_dispensations.dispensed_at', $year)
            ->where('prescriptions.status', 'dispensed')
            ->groupBy(DB::raw('MONTH(prescription_dispensations.dispensed_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        // Lab reports completed by month
        $labReportsData = LabOrder::select(
            DB::raw('MONTH(reporting_date) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('reporting_date', $year)
            ->where('status', 'completed')
            ->groupBy(DB::raw('MONTH(reporting_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        // Prepare data for all months
        $monthlyAnalytics = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyAnalytics[] = [
                'month' => $months[$i - 1],
                'medicines' => isset($medicinesData[$i]) ? (int)$medicinesData[$i]['total'] : 0,
                'lab_reports' => isset($labReportsData[$i]) ? (int)$labReportsData[$i]['total'] : 0,
            ];
        }

        return $monthlyAnalytics;
    }

    /**
     * Get medicine stock distribution by category for Polar Area Chart
     */
    private function getMedicineStockByCategory()
    {
        $categories = DB::table('medicine_categories')
            ->join('medicines', 'medicine_categories.id', '=', 'medicines.category_id')
            ->join('medicine_batches', 'medicines.id', '=', 'medicine_batches.medicine_id')
            ->where('medicine_batches.is_active', true)
            ->where('medicine_categories.is_active', true)
            ->where('medicine_batches.remaining_quantity', '>', 0) // Only count items with stock
            ->select('medicine_categories.name as category', DB::raw('SUM(medicine_batches.remaining_quantity) as stock'))
            ->groupBy('medicine_categories.id', 'medicine_categories.name')
            ->orderByDesc('stock')
            ->limit(6)
            ->get();

        if ($categories->isEmpty()) {
            return collect([
                (object)['category' => 'Analgesics', 'stock' => 120],
                (object)['category' => 'Antibiotics', 'stock' => 85],
                (object)['category' => 'Antipyretic', 'stock' => 64],
                (object)['category' => 'Antihistamines', 'stock' => 42],
                (object)['category' => 'Gastrointestinal', 'stock' => 38],
                (object)['category' => 'Cardiovascular', 'stock' => 25],
            ]);
        }

        return $categories;
    }

    /**
     * Get lab reports frequency by test type for Radar Chart (Current Year)
     * Optimized: Counts lab order items grouped by the test type for the current year.
     */
    private function getLabReportsByType()
    {
        $currentYear = \Carbon\Carbon::now()->year;

        return DB::table('lab_test_types')
            ->join('lab_order_items', 'lab_test_types.id', '=', 'lab_order_items.lab_test_type_id')
            ->join('lab_orders', 'lab_order_items.lab_order_id', '=', 'lab_orders.id')
            ->whereYear('lab_orders.created_at', $currentYear) // Current year filter
            ->select('lab_test_types.name as test_type', DB::raw('COUNT(lab_orders.id) as count'))
            ->groupBy('lab_test_types.id', 'lab_test_types.name')
            ->orderByDesc('count')
            ->limit(6)
            ->get();
    }

    /**
     * Get medicines distribution by category
     */
    private function getMedicinesDistribution()
    {
        return DB::table('medicine_categories')
            ->leftJoin('medicines', 'medicine_categories.id', '=', 'medicines.category_id')
            ->leftJoin('medicine_batches', function ($join) {
                $join->on('medicines.id', '=', 'medicine_batches.medicine_id')
                    ->where('medicine_batches.is_active', true);
            })
            ->select(
                'medicine_categories.name as category',
                DB::raw('COUNT(DISTINCT medicines.id) as medicine_count'),
                DB::raw('COALESCE(SUM(medicine_batches.remaining_quantity), 0) as total_stock')
            )
            ->groupBy('medicine_categories.id', 'medicine_categories.name')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'medicine_count' => (int) $item->medicine_count,
                    'total_stock' => (int) $item->total_stock,
                ];
            });
    }
    public function dash()
    {

        $monthlyData = [
            ['month' => 'Jan', 'medicines' => 145, 'lab_reports' => 382],
            ['month' => 'Feb', 'medicines' => 738, 'lab_reports' => 565],
            ['month' => 'Mar', 'medicines' => 252, 'lab_reports' => 391],
            ['month' => 'Apr', 'medicines' => 630, 'lab_reports' => 484],
            ['month' => 'May', 'medicines' => 525, 'lab_reports' => 338],
            ['month' => 'Jun', 'medicines' => 515, 'lab_reports' => 622],
            ['month' => 'Jul', 'medicines' => 622, 'lab_reports' => 831],
            ['month' => 'Aug', 'medicines' => 728, 'lab_reports' => 440],
            ['month' => 'Sep', 'medicines' => 641, 'lab_reports' => 355],
            ['month' => 'Oct', 'medicines' => 555, 'lab_reports' => 678],
            ['month' => 'Nov', 'medicines' => 862, 'lab_reports' => 795],
            ['month' => 'Dec', 'medicines' => 770, 'lab_reports' => 610],
        ];

        $monthlyPerformance = [
            ['month' => 'Jan', 'visits' => 1050, 'medicines' => 1415, 'lab_reports' => 1665],
            ['month' => 'Feb', 'visits' => 980, 'medicines' => 1320, 'lab_reports' => 1520],
            ['month' => 'Mar', 'visits' => 1100, 'medicines' => 1540, 'lab_reports' => 1710],
            ['month' => 'Apr', 'visits' => 1150, 'medicines' => 1650, 'lab_reports' => 1035],
            ['month' => 'May', 'visits' => 1020, 'medicines' => 1428, 'lab_reports' => 1122],
            ['month' => 'Jun', 'visits' => 950, 'medicines' => 1310, 'lab_reports' => 855],
            ['month' => 'Jul', 'visits' => 1080, 'medicines' => 1512, 'lab_reports' => 1188],
            ['month' => 'Aug', 'visits' => 1200, 'medicines' => 1740, 'lab_reports' => 1440],
            ['month' => 'Sep', 'visits' => 1180, 'medicines' => 1652, 'lab_reports' => 1062],
            ['month' => 'Oct', 'visits' => 1250, 'medicines' => 1810, 'lab_reports' => 1375],
            ['month' => 'Nov', 'visits' => 1300, 'medicines' => 1885, 'lab_reports' => 1560],
            ['month' => 'Dec', 'visits' => 1350, 'medicines' => 2025, 'lab_reports' => 1215],
        ];
        $data = [
            'stats' => $this->getDashboardStats(), //            'monthlyData' => $this->getMonthlyAnalytics(),
            //            'monthlyPerformance' => $this->getMonthlyPerformanceData(),
            'monthlyPerformance' => $monthlyPerformance,
            'monthlyData' => $monthlyData,
            'topMedicines' => $this->getMostDispensedMedicines(),
            'patientsByOffice' => $this->getPatientsByOffice(),
            'lowStockMedicines' => $this->getLowStockMedicines(),
            'outOfStockMedicines' => $this->getOutOfStockMedicines(),
            'recentVisits' => $this->getRecentVisits(),
            'pendingPrescriptions' => Prescription::where('status', 'pending')->count(),
            'processingLabReports' => LabOrder::where('status', 'processing')->count(),
        ];

        // Add counts for low stock and out of stock medicines
        $data['lowStockCount'] = $data['lowStockMedicines']->count();
        $data['outOfStockCount'] = $data['outOfStockMedicines']->count();

        // Add stock percentage calculations
        $totalMedicines = Medicine::count();
        $data['inStockCount'] = $totalMedicines - $data['outOfStockCount'] - $data['lowStockCount'];
        $data['totalMedicines'] = $totalMedicines;
        $data['outOfStockPercentage'] = $totalMedicines > 0 ? round(($data['outOfStockCount'] / $totalMedicines) * 100) : 0;
        $data['lowStockPercentage'] = $totalMedicines > 0 ? round(($data['lowStockCount'] / $totalMedicines) * 100) : 0;
        $data['inStockPercentage'] = $totalMedicines > 0 ? (100 - $data['outOfStockPercentage'] - $data['lowStockPercentage']) : 0;

        // Calculate critical stock (below 5 units)
        $data['criticalStockCount'] = DB::table('medicine_stock_value')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->count(); //        $data['criticalStockPercentage'] = $totalMedicines > 0 ? round(($data['criticalStockCount'] / $totalMedicines) * 100) : 0;
        $data['criticalStockPercentage'] = 100; //temp data


        Log::info("", $data);
        return view('dash', $data);
    }


    public function welcome()
    {

        $monthlyData = [
            ['month' => 'Jan', 'medicines' => 145, 'lab_reports' => 382],
            ['month' => 'Feb', 'medicines' => 738, 'lab_reports' => 565],
            ['month' => 'Mar', 'medicines' => 252, 'lab_reports' => 391],
            ['month' => 'Apr', 'medicines' => 630, 'lab_reports' => 484],
            ['month' => 'May', 'medicines' => 525, 'lab_reports' => 338],
            ['month' => 'Jun', 'medicines' => 515, 'lab_reports' => 622],
            ['month' => 'Jul', 'medicines' => 622, 'lab_reports' => 831],
            ['month' => 'Aug', 'medicines' => 728, 'lab_reports' => 440],
            ['month' => 'Sep', 'medicines' => 641, 'lab_reports' => 355],
            ['month' => 'Oct', 'medicines' => 555, 'lab_reports' => 678],
            ['month' => 'Nov', 'medicines' => 862, 'lab_reports' => 795],
            ['month' => 'Dec', 'medicines' => 770, 'lab_reports' => 610],
        ];

        $monthlyPerformance = [
            ['month' => 'Jan', 'visits' => 1050, 'medicines' => 1415, 'lab_reports' => 1665],
            ['month' => 'Feb', 'visits' => 980, 'medicines' => 1320, 'lab_reports' => 1520],
            ['month' => 'Mar', 'visits' => 1100, 'medicines' => 1540, 'lab_reports' => 1710],
            ['month' => 'Apr', 'visits' => 1150, 'medicines' => 1650, 'lab_reports' => 1035],
            ['month' => 'May', 'visits' => 1020, 'medicines' => 1428, 'lab_reports' => 1122],
            ['month' => 'Jun', 'visits' => 950, 'medicines' => 1310, 'lab_reports' => 855],
            ['month' => 'Jul', 'visits' => 1080, 'medicines' => 1512, 'lab_reports' => 1188],
            ['month' => 'Aug', 'visits' => 1200, 'medicines' => 1740, 'lab_reports' => 1440],
            ['month' => 'Sep', 'visits' => 1180, 'medicines' => 1652, 'lab_reports' => 1062],
            ['month' => 'Oct', 'visits' => 1250, 'medicines' => 1810, 'lab_reports' => 1375],
            ['month' => 'Nov', 'visits' => 1300, 'medicines' => 1885, 'lab_reports' => 1560],
            ['month' => 'Dec', 'visits' => 1350, 'medicines' => 2025, 'lab_reports' => 1215],
        ];
        $data = [
            'stats' => $this->getDashboardStats(), //            'monthlyData' => $this->getMonthlyAnalytics(),
            //            'monthlyPerformance' => $this->getMonthlyPerformanceData(),
            'monthlyPerformance' => $monthlyPerformance,
            'monthlyData' => $monthlyData,
            'topMedicines' => $this->getMostDispensedMedicines(),
            'patientsByOffice' => $this->getPatientsByOffice(),
            'lowStockMedicines' => $this->getLowStockMedicines(),
            'outOfStockMedicines' => $this->getOutOfStockMedicines(),
            'recentVisits' => $this->getRecentVisits(),
            'pendingPrescriptions' => Prescription::where('status', 'pending')->count(),
            'processingLabReports' => LabOrder::where('status', 'processing')->count(),
        ];

        // Add counts for low stock and out of stock medicines
        $data['lowStockCount'] = $data['lowStockMedicines']->count();
        $data['outOfStockCount'] = $data['outOfStockMedicines']->count();

        // Add stock percentage calculations
        $totalMedicines = Medicine::count();
        $data['inStockCount'] = $totalMedicines - $data['outOfStockCount'] - $data['lowStockCount'];
        $data['totalMedicines'] = $totalMedicines;
        $data['outOfStockPercentage'] = $totalMedicines > 0 ? round(($data['outOfStockCount'] / $totalMedicines) * 100) : 0;
        $data['lowStockPercentage'] = $totalMedicines > 0 ? round(($data['lowStockCount'] / $totalMedicines) * 100) : 0;
        $data['inStockPercentage'] = $totalMedicines > 0 ? (100 - $data['outOfStockPercentage'] - $data['lowStockPercentage']) : 0;

        // Calculate critical stock (below 5 units)
        $data['criticalStockCount'] = DB::table('medicine_stock_value')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->count(); //        $data['criticalStockPercentage'] = $totalMedicines > 0 ? round(($data['criticalStockCount'] / $totalMedicines) * 100) : 0;
        $data['criticalStockPercentage'] = 100; //temp data


        Log::info("", $data);
        return view('welcome', $data);
    }

    /**
     * Get monthly performance data for chart
     */
    private function getMonthlyPerformanceData()
    {
        $currentYear = Carbon::now()->year;
        $months = ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'];

        $performanceData = [];

        foreach ($months as $monthName) {
            $monthNumber = array_search($monthName, ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) + 1;

            $performanceData[] = [
                'month' => $monthName,
                'visits' => Visit::whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $monthNumber)
                    ->count(),
                'medicines' => Prescription::join('prescription_dispensations', 'prescriptions.id', '=', 'prescription_dispensations.prescription_id')
                    ->whereYear('prescription_dispensations.dispensed_at', $currentYear)
                    ->whereMonth('prescription_dispensations.dispensed_at', $monthNumber)
                    ->where('prescriptions.status', 'dispensed')
                    ->sum('prescription_dispensations.quantity_dispensed'),
                'lab_reports' => LabOrder::whereYear('reporting_date', $currentYear)
                    ->whereMonth('reporting_date', $monthNumber)
                    ->where('status', 'completed')
                    ->count(),
            ];
        }

        return $performanceData;
    }

    /**
     * Get monthly analytics for medicines and lab reports
     */
    private function getMonthlyAnalytics()
    {
        $currentYear = Carbon::now()->year;

        // Medicines dispensed by month
        $medicinesData = Prescription::select(
            DB::raw('MONTH(dispensed_at) as month'),
            DB::raw('SUM(quantity) as total')
        )
            ->whereYear('dispensed_at', $currentYear)
            ->where('status', 'dispensed')
            ->groupBy(DB::raw('MONTH(dispensed_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        // Lab reports completed by month
        $labReportsData = LabOrder::select(
            DB::raw('MONTH(reporting_date) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('reporting_date', $currentYear)
            ->where('status', 'completed')
            ->groupBy(DB::raw('MONTH(reporting_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        // Prepare data for all months
        $monthlyData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = [
                'month' => $months[$i - 1],
                'medicines' => isset($medicinesData[$i]) ? (int)$medicinesData[$i]['total'] : 0,
                'lab_reports' => isset($labReportsData[$i]) ? (int)$labReportsData[$i]['total'] : 0,
            ];
        }

        return $monthlyData;
    }
}
