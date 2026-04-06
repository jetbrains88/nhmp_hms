<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Office;
use Illuminate\Support\Str;

class BranchService
{
    /**
     * Create a new branch
     */
    public function createBranch(array $data): Branch
    {
        return Branch::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'type' => $data['type'],
            'location' => $data['location'] ?? null,
            'office_id' => $data['office_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Get branches by type
     */
    public function getBranchesByType(string $type)
    {
        return Branch::where('type', $type)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get branch statistics
     */
    public function getBranchStats(int $branchId): array
    {
        $branch = Branch::withCount(['patients', 'users', 'visits', 'appointments'])->find($branchId);

        return [
            'total_patients' => $branch->patients_count,
            'total_staff' => $branch->users_count,
            'visits_today' => $branch->visits()->whereDate('created_at', today())->count(),
            'appointments_today' => $branch->appointments()->whereDate('scheduled_at', today())->count(),
            'active_patients' => $branch->patients()->where('is_active', true)->count(),
            'pending_lab_orders' => $branch->labOrders()->where('status', 'pending')->count(),
            'low_stock_alerts' => $branch->stockAlerts()->where('is_resolved', false)->count(),
        ];
    }

    /**
     * Get all RMO branches grouped by zone
     */
    public function getRmoBranchesByZone()
    {
        return Branch::where('type', 'RMO')
            ->with('office.parent')
            ->get()
            ->groupBy(function ($branch) {
                return $branch->office?->parent?->name ?? 'Unassigned';
            });
    }
}
