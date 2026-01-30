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
    public function index(Request $request)
    {
        $query = MedicalFile::with([
            'patient',
            'treatment',
            'requestedBy',
            'uploadedBy'
        ]);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('treatment_id')) {
            $query->where('treatment_id', $request->treatment_id);
        }

        if ($request->filled('requested_by')) {
            $query->where('requested_by', $request->requested_by);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('requested_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('requested_date', '<=', $request->date_to);
        }

        $medicalFiles = $query->latest('requested_date')->paginate(20);

        $patients = Patient::active()->get();
        $treatments = Treatment::active()->get();

        // Get the specific treatment if provided (for pre-selection)
        $selectedTreatment = null;
        if ($request->filled('treatment_id')) {
            $selectedTreatment = Treatment::find($request->treatment_id);
        }

        return view('backend.medical_files.index', compact(
            'medicalFiles',
            'patients',
            'treatments',
            'selectedTreatment' 
        ));
    }

    
    // ==================================================
    // REQUEST NEW TEST (CREATE FORM)
    // ==================================================
    public function create(Request $request)
    {
        $patients = Patient::active()->orderBy('full_name')->get();

        // Get current treatment if provided
        $currentTreatment = null;
        if ($request->filled('treatment_id')) {
            $currentTreatment = Treatment::with('patient')
                ->where('id', $request->treatment_id)
                ->first();
        }

        if ($request->filled('patient_id')) {
            $currentPatient = Patient::find($request->patient_id);
            $patientTreatments = Treatment::where('patient_id', $request->patient_id)
                ->where('status', '!=', 'cancelled')
                ->get();
        } elseif ($currentTreatment) {
            // If treatment is provided, get patient from treatment
            $currentPatient = $currentTreatment->patient;
            $patientTreatments = Treatment::where('patient_id', $currentPatient->id)
                ->where('status', '!=', 'cancelled')
                ->get();
        } else {
            $currentPatient = null;
            $patientTreatments = collect();
        }

        return view('backend.medical_files.create', compact(
            'patients',
            'currentPatient',
            'currentTreatment',
            'patientTreatments'
        ));
    }

    // ==================================================
    // STORE TEST REQUEST
    // ==================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'file_type' => 'required|in:xray,lab_report,ct_scan,photo,document,prescription,report,other',
            'requested_notes' => 'required|string|max:1000',
            'expected_delivery_date' => 'nullable|date|after_or_equal:today',
        ]);

        $medicalFile = MedicalFile::create([
            'file_code' => MedicalFile::generateFileCode(),
            'patient_id' => $validated['patient_id'],
            'treatment_id' => $validated['treatment_id'],
            'file_type' => $validated['file_type'],
            'file_name' => 'Requested: ' . MedicalFile::fileTypes()[$validated['file_type']],
            'requested_date' => now(),
            'requested_by' => auth()->id(),
            'requested_notes' => $validated['requested_notes'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
            'status' => MedicalFile::STATUS_REQUESTED,
        ]);

        if ($validated['treatment_id']) {
            return redirect()->route('backend.treatments.show', $validated['treatment_id'])
                ->with('success', 'Test request created successfully. Patient can now go for the test.');
        }

        return redirect()->route('backend.medical-files.show', $medicalFile)
            ->with('success', 'Test request created successfully.');
    }

    // ==================================================
    // SHOW TEST REQUEST DETAILS
    // ==================================================
    public function show(MedicalFile $medicalFile)
    {
        $medicalFile->load([
            'patient',
            'treatment',
            'requestedBy',
            'uploadedBy'
        ]);

        return view('backend.medical_files.show', compact('medicalFile'));
    }

    // ==================================================
    // EDIT TEST REQUEST (BEFORE UPLOAD)
    // ==================================================
    public function edit(MedicalFile $medicalFile)
    {
        if ($medicalFile->isUploaded) {
            return back()->with('error', 'Cannot edit a completed test request.');
        }

        $patients = Patient::active()->orderBy('full_name')->get();

        $treatments = Treatment::where('patient_id', $medicalFile->patient_id)
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        return view('backend.medical_files.edit', compact(
            'medicalFile',
            'patients',
            'treatments'
        ));
    }

    // ==================================================
    // UPDATE TEST REQUEST
    // ==================================================
    public function update(Request $request, MedicalFile $medicalFile)
    {
        if ($medicalFile->isUploaded) {
            return back()->with('error', 'Cannot edit a completed test request.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'file_type' => 'required|in:xray,lab_report,ct_scan,photo,document,prescription,report,other',
            'requested_notes' => 'required|string|max:1000',
            'expected_delivery_date' => 'nullable|date',
            'status' => 'required|in:requested,pending,cancelled',
        ]);

        $medicalFile->update([
            'patient_id' => $validated['patient_id'],
            'treatment_id' => $validated['treatment_id'],
            'file_type' => $validated['file_type'],
            'file_name' => 'Requested: ' . MedicalFile::fileTypes()[$validated['file_type']],
            'requested_notes' => $validated['requested_notes'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('backend.medical-files.show', $medicalFile)
            ->with('success', 'Test request updated successfully.');
    }

    // ==================================================
    // UPLOAD TEST RESULT
    // ==================================================
    public function uploadResult(Request $request, MedicalFile $medicalFile)
    {
        if ($medicalFile->isUploaded) {
            return back()->with('error', 'Test result already uploaded.');
        }

        $validated = $request->validate([
            'medical_file' => 'required|file|max:10240', // 10MB
            'description' => 'nullable|string|max:500',
        ]);

        // File upload
        $file = $request->file('medical_file');

        // Generate unique filename
        $fileName = 'RESULT_' . $medicalFile->file_code . '_' . time() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'medical_files/' . now()->format('Y/m'),
            $fileName,
            'public'
        );

        // Update the medical file
        $medicalFile->update([
            'status' => MedicalFile::STATUS_COMPLETED,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'description' => $validated['description'],
            'uploaded_at' => now(),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('backend.medical-files.show', $medicalFile)
            ->with('success', 'Test result uploaded successfully.');
    }

    // ==================================================
    // MARK AS PENDING
    // ==================================================
    public function markAsPending(MedicalFile $medicalFile)
    {
        if ($medicalFile->isUploaded) {
            return back()->with('error', 'Cannot mark completed test as pending.');
        }

        $medicalFile->markAsPending();

        return back()->with('success', 'Test marked as pending.');
    }

    // ==================================================
    // CANCEL TEST REQUEST
    // ==================================================
    public function cancel(MedicalFile $medicalFile)
    {
        if ($medicalFile->isUploaded) {
            return back()->with('error', 'Cannot cancel a completed test.');
        }

        $medicalFile->cancelRequest();

        return back()->with('success', 'Test request cancelled.');
    }

    // ==================================================
    // DELETE TEST REQUEST
    // ==================================================
    public function destroy(MedicalFile $medicalFile)
    {
        if ($medicalFile->isUploaded) {
            Storage::disk('public')->delete($medicalFile->file_path);
        }

        $medicalFile->delete();

        return redirect()->route('backend.medical-files.index')
            ->with('success', 'Test request deleted successfully.');
    }

    // ==================================================
    // DOWNLOAD FILE
    // ==================================================
    public function download(MedicalFile $medicalFile)
    {
        if (!$medicalFile->isUploaded) {
            return back()->with('error', 'Test result not yet uploaded.');
        }

        return response()->download(
            storage_path('app/public/' . $medicalFile->file_path),
            $medicalFile->file_name
        );
    }

    // ==================================================
    // AJAX: GET PATIENT'S PENDING TESTS
    // ==================================================
    public function getPendingTests($patientId)
    {
        return MedicalFile::where('patient_id', $patientId)
            ->where('status', '!=', MedicalFile::STATUS_COMPLETED)
            ->where('status', '!=', MedicalFile::STATUS_CANCELLED)
            ->with('treatment')
            ->latest()
            ->get();
    }
}
