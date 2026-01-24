<?php

namespace App\Http\Controllers;

use App\Models\PatientFamily;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientFamilyController extends Controller
{
    public function index(Request $request)
    {
        $query = PatientFamily::with(['head', 'members.patient']);

        if ($request->filled('search')) {
            $query->where('family_code', 'like', "%{$request->search}%")
                ->orWhere('family_name', 'like', "%{$request->search}%");
        }

        $families = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('patient-families.index', compact('families'));
    }

    public function create()
    {
        $patients = Patient::active()->get();
        return view('patient-families.create', compact('patients'));
    }

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

            // Add head as family member
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

    public function show(PatientFamily $patientFamily)
    {
        $patientFamily->load(['head', 'members.patient']);
        $availablePatients = Patient::active()
            ->whereDoesntHave('family')
            ->where('id', '!=', $patientFamily->head_patient_id)
            ->get();

        return view('patient-families.show', compact('patientFamily', 'availablePatients'));
    }

    public function edit(PatientFamily $patientFamily)
    {
        $patients = Patient::active()->get();
        return view('patient-families.edit', compact('patientFamily', 'patients'));
    }

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

        // Update head member
        $patientFamily->members()
            ->where('patient_id', $patientFamily->head_patient_id)
            ->update(['is_head' => true]);

        return redirect()
            ->route('patient-families.index')
            ->with('success', 'Family updated successfully.');
    }

    public function destroy(PatientFamily $patientFamily)
    {
        $patientFamily->delete();

        return redirect()
            ->route('patient-families.index')
            ->with('success', 'Family deleted successfully.');
    }

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

    public function removeMember(PatientFamily $patientFamily, Patient $patient)
    {
        if ($patient->id === $patientFamily->head_patient_id) {
            return back()->with('error', 'Cannot remove head of family.');
        }

        $patientFamily->members()->where('patient_id', $patient->id)->delete();

        return back()->with('success', 'Member removed successfully.');
    }

    public function setHead(PatientFamily $patientFamily, Patient $patient)
    {
        // Remove head status from current head
        $patientFamily->members()
            ->where('is_head', true)
            ->update(['is_head' => false]);

        // Set new head
        $patientFamily->members()
            ->where('patient_id', $patient->id)
            ->update(['is_head' => true, 'relationship' => 'self']);

        // Update family head reference
        $patientFamily->update(['head_patient_id' => $patient->id]);

        return back()->with('success', 'Family head updated successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $families = PatientFamily::where('family_code', 'like', "%{$query}%")
            ->orWhere('family_name', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($family) {
                return [
                    'id' => $family->id,
                    'text' => "{$family->family_code} - {$family->family_name}",
                    'code' => $family->family_code,
                    'name' => $family->family_name,
                ];
            });

        return response()->json($families);
    }
}
