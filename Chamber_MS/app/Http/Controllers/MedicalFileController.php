<?php

namespace App\Http\Controllers;

use App\Models\MedicalFile;
use App\Models\Patient;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalFileController extends Controller
{
    // ==================================================
    // LIST ALL MEDICAL FILES
    // ==================================================
    public function index()
    {
        $medicalFiles = MedicalFile::with([
            'patient',
            'treatment',
            'uploadedBy'
        ])
            ->latest()
            ->paginate(20);

        return view('medical_files.index', compact('medicalFiles'));
    }

    // ==================================================
    // SHOW CREATE FORM
    // ==================================================
    public function create(Request $request)
    {
        $patients = Patient::where('status', 'active')
            ->orderBy('full_name')
            ->get();

        $treatmentsQuery = Treatment::where('status', '!=', 'cancelled')
            ->latest();

        if ($request->filled('patient_id')) {
            $treatmentsQuery->where('patient_id', $request->patient_id);
        }

        $treatments = $treatmentsQuery->get();

        return view('medical_files.create', compact('patients', 'treatments'));
    }

    // ==================================================
    // STORE MEDICAL FILE
    // ==================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'treatment_id'   => 'nullable|exists:treatments,id',
            'file_type'      => 'required|in:xray,photo,document,prescription,report,other',
            'description'    => 'nullable|string|max:500',
            'medical_file'   => 'required|file|max:10240', // 10MB
        ]);

        // File upload
        $file = $request->file('medical_file');

        $path = $file->store(
            'medical_files/' . now()->format('Y/m'),
            'public'
        );

        $medicalFile = MedicalFile::create([
            'file_code'       => MedicalFile::generateFileCode(),
            'patient_id'      => $validated['patient_id'],
            'treatment_id'    => $validated['treatment_id'],
            'file_type'       => $validated['file_type'],
            'file_name'       => $file->getClientOriginalName(),
            'file_path'       => $path,
            'file_size'       => $file->getSize(),
            'description'     => $validated['description'],
            'uploaded_at'     => now(),
            'uploaded_by'     => auth()->id(),
            'is_confidential' => false,
        ]);

        return redirect()
            ->route('medical_files.show', $medicalFile)
            ->with('success', 'Medical file uploaded successfully.');
    }

    // ==================================================
    // SHOW SINGLE MEDICAL FILE
    // ==================================================
    public function show($id)
    {
        $medicalFile = MedicalFile::with([
            'patient',
            'treatment',
            'uploadedBy'
        ])
            ->findOrFail($id);

        return view('medical_files.show', compact('medicalFile'));
    }

    // ==================================================
    // SHOW EDIT FORM
    // ==================================================
    public function edit($id)
    {
        $medicalFile = MedicalFile::findOrFail($id);

        $patients = Patient::where('status', 'active')
            ->orderBy('full_name')
            ->get();

        $treatments = Treatment::where('patient_id', $medicalFile->patient_id)
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        return view('medical_files.edit', compact(
            'medicalFile',
            'patients',
            'treatments'
        ));
    }

    // ==================================================
    // UPDATE MEDICAL FILE (NO FILE CHANGE)
    // ==================================================
    public function update(Request $request, $id)
    {
        $medicalFile = MedicalFile::findOrFail($id);

        $validated = $request->validate([
            'patient_id'   => 'required|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'file_type'    => 'required|in:xray,photo,document,prescription,report,other',
            'description'  => 'nullable|string|max:500',
        ]);

        $medicalFile->update([
            'patient_id'      => $validated['patient_id'],
            'treatment_id'    => $validated['treatment_id'],
            'file_type'       => $validated['file_type'],
            'description'     => $validated['description'],
            'is_confidential' => false,
        ]);

        return redirect()
            ->route('medical_files.show', $medicalFile)
            ->with('success', 'Medical file updated successfully.');
    }

    // ==================================================
    // DELETE MEDICAL FILE
    // ==================================================
    public function destroy($id)
    {
        $medicalFile = MedicalFile::findOrFail($id);

        Storage::disk('public')->delete($medicalFile->file_path);
        $medicalFile->delete();

        return redirect()
            ->route('medical_files.index')
            ->with('success', 'Medical file deleted successfully.');
    }

    // ==================================================
    // DOWNLOAD FILE
    // ==================================================
    public function download($id)
    {
        $medicalFile = MedicalFile::findOrFail($id);

        return response()->download(
            storage_path('app/public/' . $medicalFile->file_path),
            $medicalFile->file_name
        );
    }

    // ==================================================
    // AJAX: FILES BY PATIENT
    // ==================================================
    public function getFilesByPatient($patientId)
    {
        return MedicalFile::where('patient_id', $patientId)
            ->with('treatment')
            ->latest()
            ->get();
    }
}
