<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientFamily;
use App\Models\PatientFamilyMember;
use Illuminate\Http\Request;

class PatientFamilyMemberController extends Controller
{
    /**
     * Show form to add member to family.
     */
    public function create(PatientFamily $family)
    {
        // Get patients not already in any family
        $existingMemberIds = PatientFamilyMember::pluck('patient_id')->toArray();
        $availablePatients = Patient::whereNotIn('id', $existingMemberIds)
            ->where('id', '!=', $family->head_patient_id)
            ->orderBy('full_name')
            ->get();

        return view('patient-family-members.create', compact('family', 'availablePatients'));
    }

    /**
     * Add member to family.
     */
    public function store(Request $request, PatientFamily $family)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id|unique:patient_family_members,patient_id',
        ]);

        // Check if patient is already in a family
        $existingMembership = PatientFamilyMember::where('patient_id', $validated['patient_id'])->first();
        if ($existingMembership) {
            return back()->with('error', 'This patient already belongs to a family.');
        }

        // Check if patient is the family head
        if ($family->head_patient_id == $validated['patient_id']) {
            return back()->with('error', 'Family head is automatically a member.');
        }

        PatientFamilyMember::create([
            'family_id' => $family->id,
            'patient_id' => $validated['patient_id'],
        ]);

        return redirect()->route('backend.patient-families.show', $family)
            ->with('success', 'Family member added successfully.');
    }

    /**
     * Remove member from family.
     */
    public function destroy(PatientFamilyMember $member)
    {
        $family = $member->family;

        // Prevent removing family head
        if ($member->patient_id == $family->head_patient_id) {
            return redirect()->route('backend.patient-families.show', $family)
                ->with('error', 'Cannot remove family head. Delete the family instead.');
        }

        $member->delete();

        return redirect()->route('backend.patient-families.show', $family)
            ->with('success', 'Family member removed successfully.');
    }
}
