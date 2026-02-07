<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $roleMap = [
            1 => 'super_admin',
            2 => 'admin',
            3 => 'doctor',
            4 => 'receptionist',
            5 => 'accountant',
        ];

        $role = $roleMap[$user->role_id] ?? null;

        return match ($role) {
            'super_admin', 'admin'
            => view('backend.admin-dashboard'),

            'doctor'
            => view('backend.doctor-dashboard'),

            'receptionist'
            => view('backend.receptionist-dashboard'),

            'accountant'
            => view('backend.accountant-dashboard'),

            default
            => view('backend.dashboard'),
        };
    }
}
