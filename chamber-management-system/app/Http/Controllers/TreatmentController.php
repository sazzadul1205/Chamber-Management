<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $status = request('status');

        $treatments = Treatment::with(['patient', 'doctor.user', 'appointment'])
            ->when($search, function ($query, $search) {
                return $query->search($search);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('backend.treatments.index', compact('treatments', 'search', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors = Doctor::with('user')->active()->get();

        $appointment = null;
        if ($request->has('appointment_id')) {
            $appointment = Appointment::with(['patient', 'doctor.user'])->find($request->appointment_id);
        }

        // Get recent appointments for dropdown
        $appointments = Appointment::with(['patient', 'doctor.user'])
            ->whereDate('appointment_date', '>=', now()->subDays(30))
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('backend.treatments.create', compact(
            'patients',
            'doctors',
            'appointments',
            'appointment'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'diagnosis' => 'required|string|max:2000',
            'status' => 'required|in:ongoing,completed,cancelled',
        ]);

        // Check if appointment already has a treatment
        if ($validated['appointment_id']) {
            $existingTreatment = Treatment::where('appointment_id', $validated['appointment_id'])->first();
            if ($existingTreatment) {
                return back()->withInput()
                    ->with('error', 'This appointment already has a treatment record.');
            }
        }

        $treatment = Treatment::create($validated);

        return redirect()->route('backend.treatments.show', $treatment)
            ->with('success', 'Treatment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Treatment $treatment)
    {
        $treatment->load([
            'patient',
            'doctor.user',
            'appointment',
            'procedures',
            'prescriptions.medicines',
            'medicalFiles',
            'creator',
            'updater'
        ]);

        return view('backend.treatments.show', compact('treatment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Treatment $treatment)
    {
        $treatment->load(['patient', 'doctor.user', 'appointment']);

        $patients = Patient::orderBy('full_name')->get();
        $doctors = Doctor::with('user')->active()->get();
        $appointments = Appointment::with(['patient', 'doctor.user'])
            ->where('patient_id', $treatment->patient_id)
            ->whereDate('appointment_date', '>=', now()->subDays(30))
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('backend.treatments.edit', compact(
            'treatment',
            'patients',
            'doctors',
            'appointments'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Treatment $treatment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'diagnosis' => 'required|string|max:2000',
            'status' => 'required|in:ongoing,completed,cancelled',
        ]);

        $treatment->update($validated);

        return redirect()->route('backend.treatments.show', $treatment)
            ->with('success', 'Treatment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Treatment $treatment)
    {
        // Check if treatment has related records
        if (
            $treatment->procedures()->exists() ||
            $treatment->prescriptions()->exists() ||
            $treatment->medicalFiles()->exists()
        ) {
            return redirect()->route('backend.treatments.show', $treatment)
                ->with('error', 'Cannot delete treatment with related records. Please delete procedures, prescriptions, or files first.');
        }

        $treatment->delete();

        return redirect()->route('backend.treatments.index')
            ->with('success', 'Treatment deleted successfully.');
    }

    /**
     * Update treatment status.
     */
    public function updateStatus(Request $request, Treatment $treatment)
    {
        $validated = $request->validate([
            'status' => 'required|in:ongoing,completed,cancelled',
        ]);

        $treatment->update($validated);

        return redirect()->route('backend.treatments.show', $treatment)
            ->with('success', 'Treatment status updated successfully.');
    }

    /**
     * Create treatment from appointment.
     */
    public function createFromAppointment(Appointment $appointment)
    {
        // Check if appointment already has treatment
        if ($appointment->treatment) {
            return redirect()->route('backend.treatments.show', $appointment->treatment)
                ->with('info', 'Treatment already exists for this appointment.');
        }

        return view('backend.treatments.create-from-appointment', compact('appointment'));
    }

    /**
     * Store treatment from appointment.
     */
    public function storeFromAppointment(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'diagnosis' => 'required|string|max:2000',
            'status' => 'required|in:ongoing,completed,cancelled',
        ]);

        // Create treatment with appointment data
        $treatmentData = array_merge($validated, [
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'appointment_id' => $appointment->id,
        ]);

        $treatment = Treatment::create($treatmentData);

        return redirect()->route('backend.treatments.show', $treatment)
            ->with('success', 'Treatment created from appointment successfully.');
    }

    /**
     * Get patient treatment history.
     */
    public function patientHistory($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $treatments = Treatment::with(['doctor.user', 'appointment'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.treatments.patient-history', compact('patient', 'treatments'));
    }
}
