<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401);
        }
        
        // Super admins can access all
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }
        
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }
        
        \Log::warning('RoleMiddleware: Forbidden access', [
            'user_id' => $user->id,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'required_roles' => $roles,
            'url' => $request->fullUrl()
        ]);
        
        abort(403, 'You do not have the required role to access this page.');
    }
}