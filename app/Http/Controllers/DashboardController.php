<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * =========================================================================
     * DASHBOARD INDEX - ROLE-BASED VIEW ROUTING
     * =========================================================================
     * 
     * Main dashboard controller that routes users to appropriate dashboards
     * based on their role. Uses a match statement for clean role-based routing.
     * 
     * Role ID mapping:
     * - 1: Super Admin
     * - 2: Admin
     * - 3: Doctor
     * - 4: Receptionist
     * - 5: Accountant
     * 
     * Each role gets a tailored dashboard view with relevant widgets and data.
     * Default dashboard is shown for unauthenticated or unknown roles.
     * 
     * @return \Illuminate\View\View Role-specific dashboard view
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Define role ID to role name mapping
        // This should match the roles defined in your database or constants
        $roleMap = [
            1 => 'super_admin',
            2 => 'admin',
            3 => 'doctor',
            4 => 'receptionist',
            5 => 'accountant',
        ];

        // Determine the user's role name based on role_id
        $role = $roleMap[$user->role_id] ?? null;

        // Route to appropriate dashboard based on role
        return match ($role) {
            // -------------------------------
            // ADMINISTRATOR DASHBOARDS
            // -------------------------------
            'super_admin', 'admin'
            => view('backend.admin-dashboard', [
                'user' => $user,
                'role' => $role
            ]),

            // -------------------------------
            // DOCTOR DASHBOARD
            // -------------------------------
            'doctor'
            => view('backend.doctor-dashboard', [
                'user' => $user,
                'role' => $role
            ]),

            // -------------------------------
            // RECEPTIONIST DASHBOARD
            // -------------------------------
            'receptionist'
            => view('backend.receptionist-dashboard', [
                'user' => $user,
                'role' => $role
            ]),

            // -------------------------------
            // ACCOUNTANT DASHBOARD
            // -------------------------------
            'accountant'
            => view('backend.accountant-dashboard', [
                'user' => $user,
                'role' => $role
            ]),

            // -------------------------------
            // DEFAULT / FALLBACK DASHBOARD
            // -------------------------------
            default
            => view('backend.dashboard', [
                'user' => $user,
                'role' => $role
            ]),
        };
    }
}