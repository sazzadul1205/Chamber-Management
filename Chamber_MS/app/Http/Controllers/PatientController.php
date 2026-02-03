<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // =========================
    // LIST PATIENTS WITH FILTERS
    // =========================
    public function index(Request $request)
    {
        $query = Patient::query();

        // Search by name, phone, or code
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by gender
        if ($request->gender && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }

        // Paginate 20 per page
        $patients = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('backend.patients.index', compact('patients'));
    }

    // =========================
    // SHOW CREATE FORM
    // =========================
    public function create()
    {
        // List active patients for referral dropdown
        $patients = Patient::active()->get();
        return view('backend.patients.create', compact('patients'));
    }

    // =========================
    // STORE NEW PATIENT
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'phone' => 'required|string|max:20|unique:patients,phone',
            'email' => 'nullable|email|max:100|unique:patients,email',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'referred_by' => 'nullable|exists:patients,id',
            'status' => 'required|in:active,inactive,deceased',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        $patient = Patient::create([
            'patient_code' => Patient::generatePatientCode(),
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'emergency_contact' => $request->emergency_contact,
            'referred_by' => $request->referred_by,
            'status' => $request->status,
            'medical_history' => $request->medical_history,
            'allergies' => $request->allergies,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('backend.patients.index')
            ->with('success', 'Patient created successfully.');
    }

    // =========================
    // SHOW SINGLE PATIENT
    // =========================
    public function show(Patient $patient)
    {
        // Load all related data
        $patient->load([
            'appointments.doctor.user',
            'treatments.doctor.user',
            'invoices',
            'referrer'
        ]);

        // Aggregate stats for dashboard
        $stats = [
            'total_visits' => $patient->total_visits,
            'total_treatments' => $patient->treatments->count(),
            'total_invoices' => $patient->invoices->count(),
            'pending_amount' => $patient->total_pending_amount,
        ];

        return view('backend.patients.show', compact('patient', 'stats'));
    }

    // =========================
    // SHOW EDIT FORM
    // =========================
    public function edit(Patient $patient)
    {
        // Exclude self from referral list
        $patients = Patient::active()->where('id', '!=', $patient->id)->get();
        return view('backend.patients.edit', compact('patient', 'patients'));
    }

    // =========================
    // UPDATE PATIENT
    // =========================
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'phone' => 'required|string|max:20|unique:patients,phone,' . $patient->id,
            'email' => 'nullable|email|max:100|unique:patients,email,' . $patient->id,
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'referred_by' => 'nullable|exists:patients,id',
            'status' => 'required|in:active,inactive,deceased',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        $patient->update(array_merge(
            $request->only([
                'full_name',
                'gender',
                'date_of_birth',
                'phone',
                'email',
                'address',
                'emergency_contact',
                'referred_by',
                'status',
                'medical_history',
                'allergies'
            ]),
            ['updated_by' => auth()->id()]
        ));

        return redirect()
            ->route('backend.patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    // =========================
    // DELETE PATIENT
    // =========================
    public function destroy(Patient $patient)
    {
        // Prevent deletion if linked with appointments or treatments
        if ($patient->appointments()->exists()) {
            return back()->with('error', 'Cannot delete patient with appointments.');
        }

        if ($patient->treatments()->exists()) {
            return back()->with('error', 'Cannot delete patient with treatments.');
        }

        $patient->delete();

        return redirect()
            ->route('backend.patients.index')
            ->with('success', 'Patient deleted.');
    }

    // =========================
    // QUICK ADD PATIENT (AJAX)
    // =========================
    public function quickAdd(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:patients,phone',
        ]);

        $patient = Patient::create([
            'patient_code' => Patient::generatePatientCode(),
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'patient' => [
                'id' => $patient->id,
                'code' => $patient->patient_code,
                'name' => $patient->full_name,
                'phone' => $patient->phone,
            ]
        ]);
    }

    // =========================
    // SEARCH PATIENT (AJAX)
    // =========================
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $patients = Patient::where('full_name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('patient_code', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(fn($patient) => [
                'id' => $patient->id,
                'text' => "{$patient->patient_code} - {$patient->full_name} ({$patient->phone})",
                'code' => $patient->patient_code,
                'name' => $patient->full_name,
                'phone' => $patient->phone,
            ]);

        return response()->json($patients);
    }

    // =========================
    // MEDICAL HISTORY VIEW
    // =========================
    public function medicalHistory(Patient $patient)
    {
        $patient->load(['treatments.procedures', 'appointments']);
        return view('backend.patients.medical-history', compact('patient'));
    }

    // =========================
    // TOGGLE PATIENT STATUS
    // =========================
    public function toggleStatus(Patient $patient)
    {
        try {
            // Only allow toggling between active and inactive
            if ($patient->status === 'active') {
                $patient->update(['status' => 'inactive']);
            } elseif ($patient->status === 'inactive') {
                $patient->update(['status' => 'active']);
            } else {
                return back()->with('error', 'Cannot toggle deceased patients');
            }

            return back()->with('success', 'Patient status updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status');
        }
    }
}
