<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        $roleMap = [
            1 => 'Super Admin',
            2 => 'Admin',
            3 => 'Doctor',
            4 => 'Receptionist',
            5 => 'Accountant'
        ];

        $userRoleName = $roleMap[$user->role_id] ?? null;

        if (!$user || !in_array($userRoleName, $roles)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
