<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabTestType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display lab dashboard.
     */
    public function index()
    {
        $branchId = session('current_branch_id');

        $stats = [
            'pending_orders' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'pending')
                ->count(),
            'processing_orders' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'processing')
                ->count(),
            'completed_today' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count(),
            'verified_today' => LabOrder::where('branch_id', $branchId)
                ->where('is_verified', true)
                ->whereDate('verified_at', today())
                ->count(),
            'total_test_types' => LabTestType::count(),
            'urgent_orders' => LabOrder::where('branch_id', $branchId)
                ->where('priority', 'urgent')
                ->whereIn('status', ['pending', 'processing'])
                ->count(),
        ];

        // Recent orders
        $recentOrders = LabOrder::with(['patient', 'doctor'])
            ->where('branch_id', $branchId)
            ->latest()
            ->take(10)
            ->get();

        // Pending items
        $pendingItems = LabOrderItem::with(['labOrder.patient', 'labTestType'])
            ->whereHas('labOrder', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->take(15)
            ->get();

        // Stats by test type
        $testTypeStats = LabOrderItem::select(
            'lab_test_type_id',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
        )
            ->whereHas('labOrder', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereMonth('created_at', now()->month)
            ->groupBy('lab_test_type_id')
            ->with('labTestType')
            ->get();

        return view('lab.dashboard', compact('stats', 'recentOrders', 'pendingItems', 'testTypeStats'));
    }

    /**
     * Get statistics for AJAX.
     */
    public function statistics()
    {
        $branchId = session('current_branch_id');

        $stats = [
            'pending' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'pending')
                ->count(),
            'processing' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'processing')
                ->count(),
            'completed' => LabOrder::where('branch_id', $branchId)
                ->where('status', 'completed')
                ->count(),
            'verified' => LabOrder::where('branch_id', $branchId)
                ->where('is_verified', true)
                ->count(),
        ];

        return response()->json($stats);
    }
}
