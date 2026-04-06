<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BranchSwitchController extends Controller
{
    /**
     * Switch the current branch for the user
     */
    public function switch(Request $request, Branch $branch)
    {
        $user = auth()->user();

        // Check if user has access to this branch
        if (!$user->hasRole('super_admin') && !$user->branches()->where('branch_id', $branch->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this branch'
            ], 403);
        }

        // Store branch in session
        Session::put('current_branch_id', $branch->id);
        Session::put('current_branch_name', $branch->name);
        Session::put('current_branch_type', $branch->type);

        // Log branch switch for audit
        activity()
            ->performedOn($branch)
            ->causedBy($user)
            ->log('switched_branch');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'branch' => [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'type' => $branch->type
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Switched to branch: ' . $branch->name);
    }

    /**
     * Get available branches for current user
     */
    public function getAvailableBranches()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $branches = Branch::where('is_active', true)
                ->select('id', 'name', 'type')
                ->get();
        } else {
            $branches = $user->branches()
                ->where('is_active', true)
                ->select('branches.id', 'branches.name', 'branches.type')
                ->get();
        }

        return response()->json($branches);
    }

    /**
     * Get current branch info
     */
    public function getCurrentBranch()
    {
        $branchId = Session::get('current_branch_id');
        $branch = Branch::find($branchId);

        if (!$branch) {
            // Fallback to user's primary branch
            $user = auth()->user();
            $primaryBranch = $user->branches()->wherePivot('is_primary', true)->first();

            if ($primaryBranch) {
                Session::put('current_branch_id', $primaryBranch->id);
                Session::put('current_branch_name', $primaryBranch->name);
                Session::put('current_branch_type', $primaryBranch->type);
                $branch = $primaryBranch;
            }
        }

        return response()->json([
            'id' => $branch?->id,
            'name' => $branch?->name,
            'type' => $branch?->type
        ]);
    }
}
