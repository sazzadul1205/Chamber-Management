<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->gender && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('backend.patients.index', compact('patients'));
    }

    public function create()
    {
        $patients = Patient::active()->get();
        return view('backend.patients.create', compact('patients'));
    }

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

    public function show(Patient $patient)
    {
        $patient->load(['appointments.doctor.user', 'treatments.doctor.user', 'invoices', 'referrer']);

        $stats = [
            'total_visits' => $patient->total_visits,
            'total_treatments' => $patient->treatments->count(),
            'total_invoices' => $patient->invoices->count(),
            'pending_amount' => $patient->total_pending_amount,
        ];

        return view('backend.patients.show', compact('patient', 'stats'));
    }

    public function edit(Patient $patient)
    {
        $patients = Patient::active()->where('id', '!=', $patient->id)->get();
        return view('backend.patients.edit', compact('patient', 'patients'));
    }

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

        $patient->update([
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
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('backend.patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
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

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $patients = Patient::where('full_name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('patient_code', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'text' => "{$patient->patient_code} - {$patient->full_name} ({$patient->phone})",
                    'code' => $patient->patient_code,
                    'name' => $patient->full_name,
                    'phone' => $patient->phone,
                ];
            });

        return response()->json($patients);
    }

    public function medicalHistory(Patient $patient)
    {
        $patient->load(['treatments.procedures', 'appointments']);
        return view('backend.patients.medical-history', compact('patient'));
    }
}
