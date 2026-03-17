<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BranchContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        \Log::info('BranchContext: Processing for user', ['user_id' => $user->id, 'session_id' => Session::getId()]);

        // Check if user has any branch access
        if ($user->branches()->count() === 0) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'No branch access assigned to your account.');
        }

        // Set current branch if not set
        if (!Session::has('current_branch_id')) {
            $this->setDefaultBranch($user);
        }

        // Validate current branch access
        $currentBranchId = Session::get('current_branch_id');
        if (!$this->userHasBranchAccess($user, $currentBranchId)) {
            $this->setDefaultBranch($user);
        }

        // Share branch info with views
        $this->shareBranchInfo();

        \Log::info('BranchContext: Branch verified', [
            'branch_id' => Session::get('current_branch_id'),
            'branch_name' => Session::get('current_branch_name')
        ]);

        // Add branch_id to request for easy access
        $request->merge(['branch_id' => Session::get('current_branch_id')]);

        return $next($request);
    }

    /**
     * Set default branch for user
     */
    private function setDefaultBranch($user): void
    {
        // Try to get primary branch
        $primaryBranch = null;
        foreach ($user->branches as $branch) {
            if ($branch->pivot->is_primary) {
                $primaryBranch = $branch;
                break;
            }
        }

        $branch = $primaryBranch ?? $user->branches->first();

        if ($branch) {
            Session::put('current_branch_id', $branch->id);
            Session::put('current_branch_name', $branch->name);
            Session::put('current_branch_type', $branch->type);
        }
    }

    /**
     * Check if user has access to specific branch
     */
    private function userHasBranchAccess($user, $branchId): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        foreach ($user->branches as $branch) {
            if ($branch->id == $branchId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Share branch info with all views
     */
    private function shareBranchInfo(): void
    {
        view()->share('currentBranch', (object) [
            'id' => Session::get('current_branch_id'),
            'name' => Session::get('current_branch_name'),
            'type' => Session::get('current_branch_type'),
        ]);
    }
}