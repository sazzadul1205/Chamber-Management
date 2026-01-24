<?php

namespace App\Http\Controllers;

use App\Models\MedicalFile;
use App\Models\Patient;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalFileController extends Controller
{
    public function index()
    {
        $medicalFiles = MedicalFile::with(['patient', 'treatment', 'uploadedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('medical_files.index', compact('medicalFiles'));
    }

    public function create(Request $request)
    {
        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $treatments = Treatment::where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        // If patient_id is provided, filter treatments
        if ($request->has('patient_id')) {
            $patientId = $request->patient_id;
            $treatments = Treatment::where('patient_id', $patientId)
                ->where('status', '!=', 'cancelled')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('medical_files.create', compact('patients', 'treatments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'file_type' => 'required|in:xray,photo,document,prescription,report,other',
            'description' => 'nullable|string|max:500',
            'medical_file' => 'required|file|max:10240', // 10MB max
            'is_confidential' => 'boolean'
        ]);

        // Handle file upload
        $file = $request->file('medical_file');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('medical_files/' . date('Y/m'), 'public');
        $size = $file->getSize();

        $medicalFile = MedicalFile::create([
            'file_code' => MedicalFile::generateFileCode(),
            'patient_id' => $request->patient_id,
            'treatment_id' => $request->treatment_id,
            'file_type' => $request->file_type,
            'file_name' => $originalName,
            'file_path' => $path,
            'file_size' => $size,
            'description' => $request->description,
            'uploaded_at' => now(),
            'uploaded_by' => 1, // Default admin user ID
            'is_confidential' => false // Always false - no confidentiality needed
        ]);

        return redirect()->route('medical_files.show', $medicalFile->id)
            ->with('success', 'Medical file uploaded successfully.');
    }

    public function show($id)
    {
        $medicalFile = MedicalFile::with(['patient', 'treatment', 'uploadedBy'])->findOrFail($id);

        return view('medical_files.show', compact('medicalFile'));
    }

    public function edit($id)
    {
        $medicalFile = MedicalFile::findOrFail($id);
        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $treatments = Treatment::where('patient_id', $medicalFile->patient_id)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('medical_files.edit', compact('medicalFile', 'patients', 'treatments'));
    }

    public function update(Request $request, $id)
    {
        $medicalFile = MedicalFile::findOrFail($id);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'file_type' => 'required|in:xray,photo,document,prescription,report,other',
            'description' => 'nullable|string|max:500'
        ]);

        $medicalFile->update([
            'patient_id' => $request->patient_id,
            'treatment_id' => $request->treatment_id,
            'file_type' => $request->file_type,
            'description' => $request->description,
            'is_confidential' => false // Always false
        ]);

        return redirect()->route('medical_files.show', $medicalFile->id)
            ->with('success', 'Medical file updated successfully.');
    }

    public function destroy($id)
    {
        $medicalFile = MedicalFile::findOrFail($id);

        // Delete physical file
        Storage::disk('public')->delete($medicalFile->file_path);

        // Delete database record
        $medicalFile->delete();

        return redirect()->route('medical_files.index')
            ->with('success', 'Medical file deleted successfully.');
    }

    public function download($id)
    {
        $medicalFile = MedicalFile::findOrFail($id);

        $path = storage_path('app/public/' . $medicalFile->file_path);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return response()->download($path, $medicalFile->file_name);
    }

    public function getFilesByPatient($patientId)
    {
        $files = MedicalFile::where('patient_id', $patientId)
            ->with('treatment')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($files);
    }
}
