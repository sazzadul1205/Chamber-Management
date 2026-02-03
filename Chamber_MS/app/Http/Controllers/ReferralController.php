<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    // =========================
    // REFERRAL TRACKING DASHBOARD
    // =========================
    public function index(Request $request)
    {
        $query = Patient::whereHas('referredPatients');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('patient_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Get referral stats
        $referrers = $query->withCount(['referredPatients'])
            ->withSum('referredPatients', 'id') // Placeholder for more stats
            ->orderBy('referred_patients_count', 'desc')
            ->paginate(20);

        // Overall stats
        $stats = $this->getReferralStats();

        return view('backend.referrals.index', compact('referrers', 'stats'));
    }

    // =========================
    // SHOW REFERRER DETAILS
    // =========================
    public function show(Patient $patient)
    {
        $referredPatients = $patient->referredPatients()
            ->withCount(['appointments', 'treatments'])
            ->withSum('invoices', 'total_amount')
            ->latest()
            ->paginate(20);

        $stats = $patient->referral_stats;
        $monthlyData = $this->getMonthlyReferralData($patient);

        return view('backend.referrals.show', compact('patient', 'referredPatients', 'stats', 'monthlyData'));
    }

    // =========================
    // REFERRAL REPORT
    // =========================
    public function report(Request $request)
    {
        $query = Patient::query();

        // Date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Group by referrer
        $report = $query->select([
            'referred_by',
            DB::raw('COUNT(*) as total_referred'),
            DB::raw('COUNT(CASE WHEN status = "active" THEN 1 END) as active_patients'),
            DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_patients'),
            DB::raw('SUM(
                    (SELECT SUM(total_amount) FROM invoices WHERE invoices.patient_id = patients.id)
                ) as total_revenue')
        ])
            ->whereNotNull('referred_by')
            ->groupBy('referred_by')
            ->with('referrer')
            ->orderByDesc('total_referred')
            ->get();

        return view('backend.referrals.report', compact('report'));
    }

    // =========================
    // GET OVERALL STATS
    // =========================
    private function getReferralStats()
    {
        return [
            'total_referrers' => Patient::whereHas('referredPatients')->count(),
            'total_referred' => Patient::whereNotNull('referred_by')->count(),
            'active_referred' => Patient::whereNotNull('referred_by')->where('status', 'active')->count(),
            'total_revenue' => Patient::whereNotNull('referred_by')
                ->withSum('invoices', 'total_amount')
                ->get()
                ->sum('invoices_sum_total_amount') ?? 0,
        ];
    }

    // =========================
    // GET MONTHLY DATA FOR CHARTS
    // =========================
    private function getMonthlyReferralData(Patient $patient)
    {
        $data = [];
        $months = collect(range(0, 11))->map(function ($month) {
            return now()->subMonths($month)->format('Y-m');
        })->reverse();

        foreach ($months as $month) {
            $count = $patient->referredPatients()
                ->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->count();

            $data[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'count' => $count
            ];
        }

        return $data;
    }
}
