<?php

namespace App\Http\Controllers;

use App\Models\PatientFamily;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientFamilyController extends Controller
{
    // =========================
    // LIST ALL FAMILIES
    // =========================
    public function index(Request $request)
    {
        $query = PatientFamily::with(['head', 'members.patient']);

        // Search by family code or name
        if ($request->filled('search')) {
            $query->where('family_code', 'like', "%{$request->search}%")
                ->orWhere('family_name', 'like', "%{$request->search}%");
        }

        $families = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('patient-families.index', compact('families'));
    }

    // =========================
    // SHOW CREATE FORM
    // =========================
    public function create()
    {
        $patients = Patient::active()->get(); // Tailwind-ready: populate select dropdown
        return view('patient-families.create', compact('patients'));
    }

    // =========================
    // STORE NEW FAMILY
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'family_name' => 'required|string|max:100',
            'head_patient_id' => 'required|exists:patients,id',
        ]);

        DB::transaction(function () use ($request) {
            $family = PatientFamily::create([
                'family_code' => PatientFamily::generateFamilyCode(),
                'family_name' => $request->family_name,
                'head_patient_id' => $request->head_patient_id,
            ]);

            // Add head as first family member
            $family->members()->create([
                'patient_id' => $request->head_patient_id,
                'relationship' => 'self',
                'is_head' => true,
            ]);
        });

        return redirect()
            ->route('patient-families.index')
            ->with('success', 'Family created successfully.');
    }

    // =========================
    // SHOW SINGLE FAMILY
    // =========================
    public function show(PatientFamily $patientFamily)
    {
        $patientFamily->load(['head', 'members.patient']);

        // Get patients not in any family (for adding members)
        $availablePatients = Patient::active()
            ->whereDoesntHave('family')
            ->where('id', '!=', $patientFamily->head_patient_id)
            ->get();

        return view('patient-families.show', compact('patientFamily', 'availablePatients'));
    }

    // =========================
    // SHOW EDIT FORM
    // =========================
    public function edit(PatientFamily $patientFamily)
    {
        $patients = Patient::active()->get();
        return view('patient-families.edit', compact('patientFamily', 'patients'));
    }

    // =========================
    // UPDATE FAMILY
    // =========================
    public function update(Request $request, PatientFamily $patientFamily)
    {
        $request->validate([
            'family_name' => 'required|string|max:100',
            'head_patient_id' => 'required|exists:patients,id',
        ]);

        $patientFamily->update([
            'family_name' => $request->family_name,
            'head_patient_id' => $request->head_patient_id,
        ]);

        // Update head member in pivot table
        $patientFamily->members()
            ->where('patient_id', $patientFamily->head_patient_id)
            ->update(['is_head' => true]);

        return redirect()
            ->route('patient-families.index')
            ->with('success', 'Family updated successfully.');
    }

    // =========================
    // DELETE FAMILY
    // =========================
    public function destroy(PatientFamily $patientFamily)
    {
        $patientFamily->delete();

        return redirect()
            ->route('patient-families.index')
            ->with('success', 'Family deleted successfully.');
    }

    // =========================
    // ADD MEMBER TO FAMILY
    // =========================
    public function addMember(Request $request, PatientFamily $patientFamily)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id|unique:patient_family_members,patient_id,NULL,id,family_id,' . $patientFamily->id,
            'relationship' => 'required|string|max:20',
        ]);

        $patientFamily->members()->create([
            'patient_id' => $request->patient_id,
            'relationship' => $request->relationship,
            'is_head' => false,
        ]);

        return back()->with('success', 'Member added successfully.');
    }

    // =========================
    // REMOVE MEMBER FROM FAMILY
    // =========================
    public function removeMember(PatientFamily $patientFamily, Patient $patient)
    {
        if ($patient->id === $patientFamily->head_patient_id) {
            return back()->with('error', 'Cannot remove head of family.');
        }

        $patientFamily->members()->where('patient_id', $patient->id)->delete();

        return back()->with('success', 'Member removed successfully.');
    }

    // =========================
    // SET FAMILY HEAD
    // =========================
    public function setHead(PatientFamily $patientFamily, Patient $patient)
    {
        // Reset current head
        $patientFamily->members()->where('is_head', true)->update(['is_head' => false]);

        // Assign new head
        $patientFamily->members()
            ->where('patient_id', $patient->id)
            ->update(['is_head' => true, 'relationship' => 'self']);

        // Update family reference
        $patientFamily->update(['head_patient_id' => $patient->id]);

        return back()->with('success', 'Family head updated successfully.');
    }

    // =========================
    // AJAX SEARCH FOR FAMILIES
    // =========================
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $families = PatientFamily::where('family_code', 'like', "%{$query}%")
            ->orWhere('family_name', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(fn($family) => [
                'id' => $family->id,
                'text' => "{$family->family_code} - {$family->family_name}",
                'code' => $family->family_code,
                'name' => $family->family_name,
            ]);

        return response()->json($families);
    }
}
