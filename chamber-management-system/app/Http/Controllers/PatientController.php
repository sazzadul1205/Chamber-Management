<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $patients = Patient::with('referredByPatient')
            ->when($search, function ($query, $search) {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('patient_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(9);

        $patients->appends($request->all());

        return view('backend.patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Existing patients for referral dropdown
        $patients = Patient::orderBy('full_name')->get();

        // Referral types
        $referralTypes = ['patient', 'doctor', 'magazine', 'other'];

        return view('backend.patients.create', compact('patients', 'referralTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:patients',
            'email' => 'nullable|email|unique:patients',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'nullable|string|max:1000',
            'referral_type' => 'nullable|in:patient,doctor,magazine,other',
            'referred_by_patient_id' => 'nullable|exists:patients,id',
            'referred_by_text' => 'nullable|string|max:255',
        ]);

        // Adjust referred fields based on type
        if ($validated['referral_type'] !== 'patient') {
            $validated['referred_by_patient_id'] = null;
        } else {
            $validated['referred_by_text'] = null;
        }

        Patient::create($validated);

        return redirect()->route('backend.patients.index')
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $patient->load(['creator', 'updater', 'referredBy', 'referredPatients']);
        return view('backend.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $patients = Patient::where('id', '!=', $patient->id)
            ->orderBy('full_name')
            ->get();

        $referralTypes = ['patient', 'doctor', 'magazine', 'other'];

        return view('backend.patients.edit', compact('patient', 'patients', 'referralTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('patients')->ignore($patient->id)],
            'email' => ['nullable', 'email', Rule::unique('patients')->ignore($patient->id)],
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'nullable|string|max:1000',
            'referral_type' => 'nullable|in:patient,doctor,magazine,other',
            'referred_by_patient_id' => 'nullable|exists:patients,id',
            'referred_by_text' => 'nullable|string|max:255',
        ]);

        if ($validated['referral_type'] !== 'patient') {
            $validated['referred_by_patient_id'] = null;
        } else {
            $validated['referred_by_text'] = null;
        }

        $patient->update($validated);

        return redirect()->route('backend.patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('backend.patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    /**
     * Restore a soft-deleted patient.
     */
    public function restore($id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        $patient->restore();

        return redirect()->route('backend.patients.index')
            ->with('success', 'Patient restored successfully.');
    }

    /**
     * Permanently delete a patient.
     */
    public function forceDelete($id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        $patient->forceDelete();

        return redirect()->route('backend.patients.index')
            ->with('success', 'Patient permanently deleted.');
    }

    /**
     * Show deleted patients.
     */
    public function deleted()
    {
        $patients = Patient::onlyTrashed()->latest()->paginate(15);
        return view('backend.patients.deleted', compact('patients'));
    }
}
