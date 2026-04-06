<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\StockAlert;
use Illuminate\Http\Request;

class StockAlertController extends Controller
{
    /**
     * Display stock alerts
     */
    public function index(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;
        
        $stats = $this->calculateStats($branchId);
        $medicines = Medicine::where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhere('is_global', true);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return view('pharmacy.alerts.index', compact('stats', 'medicines'));
    }

    /**
     * AJAX: Get paginated alerts data
     */
    public function getAlertsData(Request $request)
    {
        $branchId = auth()->user()->current_branch_id;
        
        $query = StockAlert::with(['medicine.category', 'resolvedBy'])
            ->where('stock_alerts.branch_id', $branchId);
            
        // Filtering
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('medicine', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('alert_type')) {
            $query->where('alert_type', $request->alert_type);
        }
        
        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_resolved', false);
            } elseif ($request->status === 'resolved') {
                $query->where('is_resolved', true);
            }
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sorting
        $sortField = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        
        if ($sortField === 'medicine.name') {
            $query->join('medicines', 'stock_alerts.medicine_id', '=', 'medicines.id')
                  ->orderBy('medicines.name', $direction)
                  ->select('stock_alerts.*');
        } elseif (str_contains($sortField, '.')) {
            // Safety for other potential joined sorts
            $query->orderBy($sortField, $direction);
        } else {
            $query->orderBy('stock_alerts.' . $sortField, $direction);
        }
        
        $perPage = $request->input('per_page', 15);
        $alerts = $query->paginate($perPage);
        
        return response()->json($alerts);
    }

    /**
     * AJAX: Get stats for alerts
     */
    public function getStats()
    {
        $branchId = auth()->user()->current_branch_id;
        return response()->json($this->calculateStats($branchId));
    }

    /**
     * Resolve alert
     */
    public function resolve(Request $request, StockAlert $alert)
    {
        $request->validate([
            'resolution_notes' => 'nullable|string|max:500',
        ]);
        
        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
            'resolution_notes' => $request->resolution_notes,
        ]);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alert resolved successfully'
            ]);
        }
        
        return redirect()->back()->with('success', 'Alert resolved');
    }

    /**
     * Bulk resolve alerts
     */
    public function bulkResolve(Request $request)
    {
        $request->validate([
            'alert_ids' => 'required|array',
            'alert_ids.*' => 'exists:stock_alerts,id',
            'resolution_notes' => 'nullable|string|max:500',
        ]);
        
        $branchId = auth()->user()->current_branch_id;
        
        StockAlert::whereIn('id', $request->alert_ids)
            ->where('branch_id', $branchId)
            ->where('is_resolved', false)
            ->update([
                'is_resolved' => true,
                'resolved_at' => now(),
                'resolved_by' => auth()->id(),
                'resolution_notes' => $request->resolution_notes,
            ]);
            
        return response()->json([
            'success' => true,
            'message' => count($request->alert_ids) . ' alerts resolved successfully'
        ]);
    }

    /**
     * Resolve all alerts for a medicine
     */
    public function resolveAll(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'resolution_notes' => 'nullable|string|max:500',
        ]);
        
        $count = StockAlert::where('medicine_id', $request->medicine_id)
            ->where('branch_id', auth()->user()->current_branch_id)
            ->where('is_resolved', false)
            ->update([
                'is_resolved' => true,
                'resolved_at' => now(),
                'resolved_by' => auth()->id(),
                'resolution_notes' => $request->resolution_notes,
            ]);
        
        return redirect()
            ->back()
            ->with('success', $count . ' alerts resolved');
    }

    /**
     * Internal: Calculate stats helper
     */
    private function calculateStats($branchId)
    {
        return [
            'total_active' => StockAlert::where('stock_alerts.branch_id', $branchId)->where('is_resolved', false)->count(),
            'low_stock' => StockAlert::where('stock_alerts.branch_id', $branchId)->where('is_resolved', false)->where('alert_type', 'low_stock')->count(),
            'out_of_stock' => StockAlert::where('stock_alerts.branch_id', $branchId)->where('is_resolved', false)->where('alert_type', 'out_of_stock')->count(),
            'expiring_soon' => StockAlert::where('stock_alerts.branch_id', $branchId)->where('is_resolved', false)->where('alert_type', 'expiring_soon')->count(),
            'total_resolved' => StockAlert::where('stock_alerts.branch_id', $branchId)->where('is_resolved', true)->count(),
        ];
    }
}