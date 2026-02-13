<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Patient Count
        $PatientCount = Patient::totalCount();

        // Today's Patient Count
        $PatientToday = Patient::TodayCount();

        // Total User Count
        $totalUserCount = User::totalCount();

        // Appointment Count
        $AppointmentCount = Appointment::totalCount();

        // Appointment Today Count
        $AppointmentToday = Appointment::TodayCount();

        // -------------------------
        // TOTAL PAYMENTS & TODAY'S PAYMENTS
        // -------------------------
        $TotalPayments = Payment::sum('amount');

        $TodaysPayments = Payment::whereDate('payment_date', Carbon::today())
            ->sum('amount');


        // Get the authenticated user
        $user = Auth::user();

        // Role mapping
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
            => view('backend.admin-dashboard', [
                'user' => $user,
                'role' => $role,
                'totalUserCount' => $totalUserCount,
                'PatientCount' => $PatientCount,
                'AppointmentCount' => $AppointmentCount,
                'PatientToday' => $PatientToday,
                'AppointmentToday' => $AppointmentToday,
                'TotalPayments' => $TotalPayments,
                'TodaysPayments' => $TodaysPayments,

            ]),

            'doctor'
            => view('backend.doctor-dashboard', [
                'user' => $user,
                'role' => $role,
                'TotalPayments' => $TotalPayments,
                'TodaysPayments' => $TodaysPayments,
            ]),

            'receptionist'
            => view('backend.receptionist-dashboard', [
                'user' => $user,
                'role' => $role,
                'TotalPayments' => $TotalPayments,
                'TodaysPayments' => $TodaysPayments,
            ]),

            'accountant'
            => view('backend.accountant-dashboard', [
                'user' => $user,
                'role' => $role,
                'TotalPayments' => $TotalPayments,
                'TodaysPayments' => $TodaysPayments,
            ]),

            default
            => view('backend.dashboard', [
                'user' => $user,
                'role' => $role,
                'TotalPayments' => $TotalPayments,
                'TodaysPayments' => $TodaysPayments,
            ]),
        };
    }
}
