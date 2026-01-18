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
            'member_patient_ids' => 'nullable|array',
            'member_patient_ids.*' => 'exists:patients,id',
        ]);

        if (in_array($validated['head_patient_id'], $request->member_patient_ids ?? [])) {
            return back()->withErrors([
                'member_patient_ids' => 'Family head cannot be a family member.',
            ]);
        }

        $family = PatientFamily::create([
            'family_name' => $validated['family_name'],
            'head_patient_id' => $validated['head_patient_id'],
        ]);

        // ✅ Correct column name
        foreach ($request->member_patient_ids ?? [] as $patientId) {
            PatientFamilyMember::create([
                'family_id' => $family->id,
                'patient_id' => $patientId,
            ]);
        }

        return redirect()
            ->route('backend.patient-families.show', $family)
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
        // IDs of patients in other families
        $otherMemberIds = PatientFamilyMember::where('family_id', '!=', $patientFamily->id)
            ->pluck('patient_id')
            ->toArray();

        $unavailableIds = array_merge($otherMemberIds, [$patientFamily->head_patient_id]);

        $availablePatients = Patient::whereNotIn('id', $unavailableIds)
            ->orderBy('full_name')
            ->get();

        // Add current family members back
        $currentMembers = $patientFamily->members->pluck('patient');
        $availablePatients = $availablePatients->concat($currentMembers)->unique('id');

        // Add head patient if not already present
        if ($patientFamily->headPatient && !$availablePatients->contains('id', $patientFamily->headPatient->id)) {
            $availablePatients->push($patientFamily->headPatient);
        }

        return view('backend.patient-families.edit', compact('patientFamily', 'availablePatients'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PatientFamily $patientFamily)
    {
        $validated = $request->validate([
            'family_name' => 'required|string|max:255',
            'member_patient_ids' => 'nullable|array',
            'member_patient_ids.*' => 'exists:patients,id',
        ]);

        // Update family name
        $patientFamily->update([
            'family_name' => $validated['family_name'],
        ]);

        $memberIds = $validated['member_patient_ids'] ?? [];

        // Remove family head if accidentally included
        $memberIds = array_diff($memberIds, [$patientFamily->head_patient_id]);

        // Existing members
        $existingMemberIds = $patientFamily->members()->pluck('patient_id')->toArray();

        // 1️⃣ Delete members that were removed
        $toRemove = array_diff($existingMemberIds, $memberIds);
        if (!empty($toRemove)) {
            PatientFamilyMember::where('family_id', $patientFamily->id)
                ->whereIn('patient_id', $toRemove)
                ->delete();
        }

        // 2️⃣ Add new members
        $toAdd = array_diff($memberIds, $existingMemberIds);
        foreach ($toAdd as $patientId) {
            PatientFamilyMember::create([
                'family_id' => $patientFamily->id,
                'patient_id' => $patientId,
            ]);
        }

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
