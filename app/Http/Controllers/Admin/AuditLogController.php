<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\ReportService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display audit logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'branch', 'details']);
        $query = $this->applyFiltersToQuery($query, $request);

        $logs = $query->orderByDesc('created_at')->paginate(20);

        // Get filter options
        $entityTypes = AuditLog::select('entity_type')->distinct()->pluck('entity_type');
        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.audit.index', compact('logs', 'entityTypes', 'actions'));
    }

    /**
     * Show audit log details
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'branch', 'details']);

        return view('admin.audit.show', compact('auditLog'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $query = AuditLog::with(['user', 'branch']);
        $query = $this->applyFiltersToQuery($query, $request);

        $logs = $query->orderByDesc('created_at')->get();

        $csvData = $logs->map(function ($log) {
            return [
                'Date' => $log->created_at->format('Y-m-d H:i:s'),
                'User' => $log->user?->name ?? 'System',
                'Branch' => $log->branch?->name ?? 'N/A',
                'Action' => $log->action,
                'Entity' => class_basename($log->entity_type),
                'Entity ID' => $log->entity_id,
                'IP Address' => $log->ip_address,
            ];
        })->toArray();

        $csv = app(ReportService::class)->toCsv(
            $csvData,
            ['Date', 'User', 'Branch', 'Action', 'Entity', 'Entity ID', 'IP Address']
        );

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit-log-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Get audit log statistics
     */
    public function stats(Request $request)
    {
        $query = AuditLog::query();
        $query = $this->applyFiltersToQuery($query, $request);

        $statsQuery = clone $query;

        $stats = [
            'total' => $statsQuery->count(),
            'created' => (clone $query)->where('action', 'created')->count(),
            'updated' => (clone $query)->where('action', 'updated')->count(),
            'deleted' => (clone $query)->where('action', 'deleted')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get paginated audit data for AJAX table
     */
    public function data(Request $request)
    {
        $query = AuditLog::with(['user', 'branch', 'details']);
        $query = $this->applyFiltersToQuery($query, $request);

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        // Ensure direction is valid
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';
        
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage);

        return response()->json($logs);
    }

    /**
     * Apply common filters to audit log query
     */
    protected function applyFiltersToQuery($query, Request $request)
    {
        // Global Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('entity_type', 'LIKE', "%{$search}%")
                    ->orWhere('ip_address', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Action Filter
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Entity Type Filter
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        // User Filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Security Isolation: Non-super admins are always restricted to their assigned branch
        if (!auth()->user()->isSuperAdmin()) {
            $userBranchId = auth()->user()->primary_branch_id;
            if ($userBranchId) {
                $query->where('branch_id', $userBranchId);
            }
        }

        // Optional Branch Filter (Explicitly selected by the user)
        if ($request->filled('branch_id')) {
            $filterBranchId = $request->branch_id;
            if ($filterBranchId === 'null' || $filterBranchId === 'system') {
                $query->whereNull('branch_id');
            } else {
                $query->where('branch_id', (int) $filterBranchId);
            }
        }

        // Date Range Filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }
}
