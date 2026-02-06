<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        $roleMap = [
            1 => 'Super Admin',
            2 => 'Admin',
            3 => 'Doctor',
            4 => 'Receptionist',
            5 => 'Accountant',
        ];

        $userRoleName = $roleMap[$user->role_id] ?? null;

        if (!$userRoleName) {
            abort(403, 'Unauthorized access');
        }

        // Normalize function
        $normalize = fn($value) =>
        strtolower(str_replace([' ', '-', '_'], '', $value));

        $normalizedUserRole = $normalize($userRoleName);
        $normalizedAllowedRoles = array_map($normalize, $roles);

        if (!in_array($normalizedUserRole, $normalizedAllowedRoles)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
