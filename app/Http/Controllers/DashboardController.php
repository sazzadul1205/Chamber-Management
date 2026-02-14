<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Treatment;
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
            => (function () use ($user, $role, $TotalPayments, $TodaysPayments) {
                $doctorId = optional($user->doctor)->id;

                $doctorAppointmentCount = 0;
                $doctorAppointmentToday = 0;
                $doctorWaitingCount = 0;
                $doctorInProgressCount = 0;
                $doctorActiveTreatmentCount = 0;
                $doctorPatientCount = 0;
                $doctorPrescriptionToday = 0;
                $doctorUpcomingFollowups = 0;

                if ($doctorId) {
                    $doctorAppointments = Appointment::where('doctor_id', $doctorId);
                    $doctorAppointmentCount = (clone $doctorAppointments)->count();
                    $doctorAppointmentToday = (clone $doctorAppointments)
                        ->whereDate('appointment_date', Carbon::today())
                        ->count();
                    $doctorWaitingCount = (clone $doctorAppointments)
                        ->whereDate('appointment_date', Carbon::today())
                        ->whereIn('status', ['scheduled', 'checked_in'])
                        ->count();
                    $doctorInProgressCount = (clone $doctorAppointments)
                        ->whereDate('appointment_date', Carbon::today())
                        ->where('status', 'in_progress')
                        ->count();

                    $doctorTreatments = Treatment::where('doctor_id', $doctorId);
                    $doctorActiveTreatmentCount = (clone $doctorTreatments)->active()->count();
                    $doctorPatientCount = (clone $doctorTreatments)->distinct('patient_id')->count('patient_id');
                    $doctorUpcomingFollowups = (clone $doctorTreatments)
                        ->whereDate('followup_date', '>=', Carbon::today())
                        ->whereNotNull('followup_date')
                        ->count();

                    $doctorPrescriptionToday = Prescription::whereHas('treatment', function ($query) use ($doctorId) {
                        $query->where('doctor_id', $doctorId);
                    })->whereDate('prescription_date', Carbon::today())->count();
                }

                return view('backend.doctor-dashboard', [
                    'user' => $user,
                    'role' => $role,
                    'TotalPayments' => $TotalPayments,
                    'TodaysPayments' => $TodaysPayments,
                    'doctorAppointmentCount' => $doctorAppointmentCount,
                    'doctorAppointmentToday' => $doctorAppointmentToday,
                    'doctorWaitingCount' => $doctorWaitingCount,
                    'doctorInProgressCount' => $doctorInProgressCount,
                    'doctorActiveTreatmentCount' => $doctorActiveTreatmentCount,
                    'doctorPatientCount' => $doctorPatientCount,
                    'doctorPrescriptionToday' => $doctorPrescriptionToday,
                    'doctorUpcomingFollowups' => $doctorUpcomingFollowups,
                    'doctorId' => $doctorId,
                ]);
            })(),

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
