<?php

namespace App\Policies;

use App\Models\Visit;
use App\Models\User;

class VisitPolicy
{
    /**
     * Determine if user can view any visits
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_visits');
    }

    /**
     * Determine if user can view the visit
     */
    public function view(User $user, Visit $visit)
    {
        try {
            \Log::info('VisitPolicy@view: Start', [
                'user_id' => $user->id,
                'user_roles' => $user->roles->pluck('name'),
                'visit_id' => $visit->id,
                'visit_branch_id' => $visit->branch_id
            ]);

            if (!$user->hasPermission('view_visits')) {
                \Log::warning('VisitPolicy@view: Permission [view_visits] missing');
                return false;
            }

            // Super admin can view all
            if ($user->hasRole('super_admin')) {
                \Log::info('VisitPolicy@view: Allowed as super_admin');
                return true;
            }

            // Check branch access
            // Use current_branch_id attribute or relationship
            $hasBranchAccess = $user->branches()->where('branch_id', $visit->branch_id)->exists();
            \Log::info('VisitPolicy@view: Branch access check', ['has_access' => $hasBranchAccess]);

            if (!$hasBranchAccess) {
                \Log::warning('VisitPolicy@view: Denied due to branch access');
                return false;
            }

            // Role-specific access rules
            $canView = $user->hasPermission('view_all_visits');
            \Log::info('VisitPolicy@view: Initial canView (from view_all_visits)', ['can_view' => $canView]);

            if ($user->hasRole('reception')) {
                // Reception can view all visits in their branch
                \Log::info('VisitPolicy@view: Allowed as reception');
                $canView = true;
            }

            if (!$canView && $user->hasRole('doctor')) {
                // Doctors can view visits assigned to them
                if ($visit->doctor_id === $user->id) {
                    \Log::info('VisitPolicy@view: Allowed as assigned doctor');
                    $canView = true;
                }
            }

            if (!$canView && $user->hasRole('nurse')) {
                // Nurses can view visits where they recorded vitals
                if ($visit->vitals()->where('recorded_by', $user->id)->exists()) {
                    \Log::info('VisitPolicy@view: Allowed as recording nurse');
                    $canView = true;
                }
            }

            if (!$canView && $user->hasRole('lab')) {
                // Lab can view visits with lab orders
                if ($visit->labOrders()->exists()) {
                    \Log::info('VisitPolicy@view: Allowed as lab staff');
                    $canView = true;
                }
            }

            \Log::info('VisitPolicy@view: Final decision', ['can_view' => $canView]);
            return $canView;
        } catch (\Exception $e) {
            \Log::error('VisitPolicy@view: Exception occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Determine if user can create visits
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_visits');
    }

    /**
     * Determine if user can update the visit
     */
    public function update(User $user, Visit $visit): bool
    {
        if (!$user->hasPermission('edit_visits')) {
            return false;
        }

        // Super admin can update all
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Check branch access
        if (!$user->branches()->where('branch_id', $visit->branch_id)->exists()) {
            return false;
        }

        // Role-specific update rules
        if ($user->hasRole('doctor')) {
            // Doctors can only update their own visits
            return $visit->doctor_id === $user->id;
        }

        if ($user->hasRole('reception')) {
            // Reception can update status of any visit in their branch
            return true;
        }

        return false;
    }

    /**
     * Determine if user can delete the visit
     */
    public function delete(User $user, Visit $visit): bool
    {
        if (!$user->hasPermission('delete_visits')) {
            return false;
        }

        // Only super admin and admin can delete visits
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Determine if user can start consultation for this visit
     */
    public function consult(User $user, Visit $visit): bool
    {
        if (!$user->hasRole('doctor')) {
            return false;
        }

        // Doctor can consult if assigned to this visit OR if no doctor assigned yet
        return $visit->doctor_id === null || $visit->doctor_id === $user->id;
    }

    /**
     * Determine if user can record vitals for this visit
     */
    public function recordVitals(User $user, Visit $visit): bool
    {
        if (!$user->hasPermission('record_vitals')) {
            return false;
        }

        // Check branch access
        if (!$user->branches()->where('branch_id', $visit->branch_id)->exists()) {
            return false;
        }

        // Nurses can record vitals for any visit in their branch
        return $user->hasRole('nurse');
    }

    /**
     * Determine if user can view visit queue
     */
    public function viewQueue(User $user, int $branchId): bool
    {
        if (!$user->hasPermission('view_waiting_queue')) {
            return false;
        }

        // Check branch access
        return $user->branches()->where('branch_id', $branchId)->exists();
    }

    /**
     * Determine if user can update visit status
     */
    public function updateStatus(User $user, Visit $visit): bool
    {
        if (!$user->hasPermission('update_visit_status')) {
            return false;
        }

        // Check branch access
        if (!$user->branches()->where('branch_id', $visit->branch_id)->exists()) {
            return false;
        }

        // Reception can update status of any visit
        if ($user->hasRole('reception')) {
            return true;
        }

        // Doctors can update status of their own visits
        if ($user->hasRole('doctor') && $visit->doctor_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if user can assign doctor to visit
     */
    public function assignDoctor(User $user, Visit $visit): bool
    {
        // Only reception and admin can assign doctors
        return $user->hasAnyRole(['reception', 'admin', 'super_admin']);
    }

    /**
     * Determine if user can cancel the visit
     */
    public function cancel(User $user, Visit $visit): bool
    {
        // Only reception and doctor can cancel visits
        if (!$user->hasAnyRole(['reception', 'doctor'])) {
            return false;
        }

        // Check branch access
        if (!$user->branches()->where('branch_id', $visit->branch_id)->exists()) {
            return false;
        }

        // Doctors can only cancel their own visits
        if ($user->hasRole('doctor') && $visit->doctor_id !== $user->id) {
            return false;
        }

        // Can only cancel if not already completed
        return !in_array($visit->status, ['completed', 'cancelled']);
    }
}