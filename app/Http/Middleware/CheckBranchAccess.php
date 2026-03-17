<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBranchAccess
{
    public function handle(Request $request, Closure $next, $paramName = 'branch'): Response
    {
        $user = auth()->user();
        
        // Super admins can access all branches
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }
        
        $branchId = $request->route($paramName);
        
        if ($branchId) {
            $hasAccess = $user->branches()->where('branch_id', $branchId)->exists();
            
            \Log::info('CheckBranchAccess: Checking access', [
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'has_access' => $hasAccess
            ]);

            if (!$hasAccess) {
                \Log::warning('CheckBranchAccess: Forbidden access', [
                    'user_id' => $user->id,
                    'branch_id' => $branchId,
                    'url' => $request->fullUrl()
                ]);
                abort(403, 'You do not have access to this branch.');
            }
        }
        
        return $next($request);
    }
}