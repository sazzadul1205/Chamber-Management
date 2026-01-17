<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientFamily;
use App\Models\PatientFamilyMember;
use Illuminate\Http\Request;

class PatientFamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $families = PatientFamily::with(['headPatient', 'members.patient'])
            ->when($search, function ($query, $search) {
                return $query->search($search);
            })
            ->latest()
            ->paginate(15);

        return view('backend.patient-families.index', compact('families', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get patients who are not already family heads and not in any family
        $existingHeadIds = PatientFamily::pluck('head_patient_id')->toArray();
        $existingMemberIds = \App\Models\PatientFamilyMember::pluck('patient_id')->toArray();
        $unavailablePatientIds = array_unique(array_merge($existingHeadIds, $existingMemberIds));

        $availablePatients = Patient::whereNotIn('id', $unavailablePatientIds)
            ->orderBy('full_name')
            ->get();

        return view('backend.patient-families.create', compact('availablePatients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'family_name' => 'required|string|max:255',
            'head_patient_id' => 'required|exists:patients,id|unique:patient_families,head_patient_id',
        ]);

        // Check if patient is already in a family
        $existingMembership = \App\Models\PatientFamilyMember::where('patient_id', $validated['head_patient_id'])->first();
        if ($existingMembership) {
            return back()->with('error', 'This patient already belongs to another family.');
        }

        $family = PatientFamily::create($validated);

        return redirect()->route('backend.patient-families.show', $family)
            ->with('success', 'Family created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PatientFamily $patientFamily)
    {
        $patientFamily->load([
            'headPatient',
            'members.patient',
            'patients' // All patients including head
        ]);

        return view('backend.patient-families.show', compact('patientFamily'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatientFamily $patientFamily)
    {
        // Get IDs of patients already in families (including head)
        $existingMemberIds = PatientFamilyMember::pluck('patient_id')->toArray();
        $unavailableIds = array_merge($existingMemberIds, [$patientFamily->head_patient_id]);

        // Get patients that can be added
        $availablePatients = Patient::whereNotIn('id', $unavailableIds)
            ->orderBy('full_name')
            ->get();

        return view('backend.patient-families.edit', compact('patientFamily', 'availablePatients'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PatientFamily $patientFamily)
    {
        $validated = $request->validate([
            'family_name' => 'required|string|max:255',
        ]);

        $patientFamily->update($validated);

        return redirect()->route('backend.patient-families.show', $patientFamily)
            ->with('success', 'Family updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientFamily $patientFamily)
    {
        // Delete all family members first
        $patientFamily->members()->delete();
        $patientFamily->delete();

        return redirect()->route('backend.patient-families.index')
            ->with('success', 'Family deleted successfully.');
    }
}
