<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\DentalChair;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    // =========================
    // LIST TREATMENTS
    // =========================
    public function index(Request $request)
    {
        $query = Treatment::with(['patient', 'doctor.user', 'appointment']);

        // Filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('treatment_type')) {
            $query->where('treatment_type', $request->treatment_type);
        }

        $treatments = $query->orderBy('created_at', 'desc')->paginate(20);

        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();

        // Calculate stats
        $totalCount = Treatment::count();
        $inProgressCount = Treatment::where('status', 'in_progress')->count();
        $completedCount = Treatment::where('status', 'completed')->count();

        // Calculate total revenue from completed treatments
        $totalRevenue = Treatment::where('status', 'completed')
            ->sum('total_actual_cost') ?? 0;

        return view('backend.treatments.index', compact(
            'treatments',
            'patients',
            'doctors',
            'totalCount',
            'inProgressCount',
            'completedCount',
            'totalRevenue'
        ));
    }

    // =========================
    // CREATE TREATMENT FORM
    // =========================
    public function create(Request $request)
    {
        // Get active patients
        $patients = Patient::active()->get();

        // Get active doctors with user info
        $doctors = Doctor::with('user')->active()->get();

        // Get appointments that are not completed/cancelled and not linked to treatment
        $query = Appointment::with(['patient', 'doctor.user'])
            ->whereIn('status', ['checked_in', 'in_progress'])
            ->whereDoesntHave('treatment');

        // Filter by patient if specified
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by doctor if specified
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by today and future appointments by default
        if (!$request->filled('date')) {
            $query->whereDate('appointment_date', '>=', today());
        } else {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        return view('backend.treatments.create', compact(
            'patients',
            'doctors',
            'appointments'
        ));
    }

    // =========================
    // STORE NEW TREATMENT
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'treatment_type' => 'required|in:single_visit,multi_visit',
            'estimated_sessions' => 'required|integer|min:1',
            'treatment_date' => 'required|date',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'nullable|string',
            'total_estimated_cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:planned,in_progress,completed,cancelled,on_hold',
        ]);

        // Check if appointment is provided
        $appointment = null;
        if ($request->appointment_id) {
            $appointment = Appointment::find($request->appointment_id);

            // Check if appointment already has a treatment
            if ($appointment && $appointment->treatment()->exists()) {
                return back()->with('error', 'This appointment is already linked to a treatment.');
            }

            // Check if appointment is in a valid state
            if ($appointment && !in_array($appointment->status, ['checked_in', 'in_progress'])) {
                return back()->with('error', 'Treatment can only be created for appointments that are checked in or in progress.');
            }

            // Auto-fill patient and doctor from appointment
            if ($appointment) {
                $request->merge([
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $appointment->doctor_id,
                    'treatment_date' => $appointment->appointment_date->format('Y-m-d'),
                ]);
            }
        }

        // Create the treatment
        $treatment = Treatment::create([
            'treatment_code' => Treatment::generateTreatmentCode(),
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_id' => $request->appointment_id,
            'initial_appointment_id' => $request->appointment_id,
            'treatment_type' => $request->treatment_type,
            'estimated_sessions' => $request->estimated_sessions,
            'treatment_date' => $request->treatment_date,
            'diagnosis' => $request->diagnosis,
            'treatment_plan' => $request->treatment_plan,
            'total_estimated_cost' => $request->total_estimated_cost,
            'discount' => $request->discount ?? 0,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Update appointment to in_progress when treatment is created
        if ($appointment && $appointment->status != 'in_progress') {
            $appointment->update(['status' => 'in_progress']);
        }

        // REMOVE OR COMMENT THIS LINE:
        // dd($treatment); // <-- This is causing the issue

        return redirect()->route('backend.treatments.show', $treatment)
            ->with('success', 'Treatment created successfully.');
    }

    // =========================
    // SHOW TREATMENT DETAILS
    // =========================
    public function show(Treatment $treatment)
    {
        // Load ALL relationships including payments
        $treatment->load([
            'patient',
            'doctor.user',
            'appointment',
            'procedures.payments',
            'sessions.payments',
            'prescriptions',
            'invoices.payments',
            'medicalFiles.uploadedBy'
        ]);

        // Calculate session costs - FIXED with null-safe operator
        $sessionCosts = $treatment->sessions->map(function ($session) {
            $sessionPaid = optional($session->payments)->sum('amount') ?? 0;
            $sessionBalance = max(0, ($session->cost_for_session ?? 0) - $sessionPaid);

            return [
                'id' => $session->id,
                'session_number' => $session->session_number,
                'title' => $session->session_title,
                'scheduled_date' => $session->scheduled_date,
                'status' => $session->status,
                'cost' => $session->cost_for_session ?? 0,
                'paid' => $sessionPaid,
                'balance' => $sessionBalance,
                'duration' => $session->duration_planned,
                'actual_duration' => $session->duration_actual
            ];
        });

        // Calculate procedure costs - FIXED with null-safe operator
        $procedureCosts = $treatment->procedures->map(function ($procedure) {
            $procedurePaid = optional($procedure->payments)->sum('amount') ?? 0;
            $procedureBalance = max(0, ($procedure->cost ?? 0) - $procedurePaid);

            return [
                'id' => $procedure->id,
                'code' => $procedure->procedure_code,
                'name' => $procedure->procedure_name,
                'cost' => $procedure->cost ?? 0,
                'paid' => $procedurePaid,
                'balance' => $procedureBalance,
                'status' => $procedure->status
            ];
        });

        // Calculate totals
        $totalSessionCost = $sessionCosts->sum('cost');
        $totalSessionPaid = $sessionCosts->sum('paid');
        $totalProceduresCost = $procedureCosts->sum('cost');
        $totalProceduresPaid = $procedureCosts->sum('paid');

        $subtotal = $totalSessionCost + $totalProceduresCost;
        $totalPaidFromItems = $totalSessionPaid + $totalProceduresPaid;

        // Calculate payment totals from ALL sources
        $totalPaid = 0;

        // 1. Payments from invoices
        foreach ($treatment->invoices as $invoice) {
            $totalPaid += optional($invoice->payments)->sum('amount') ?? 0;
        }

        // 2. Direct session payments (already counted above)
        // 3. Direct procedure payments (already counted above)

        // Use whichever is greater (in case there are direct payments)
        $totalPaid = max($totalPaid, $totalPaidFromItems);

        // Calculate treatment final cost
        $finalActualCost = max(
            $subtotal - ($treatment->discount ?? 0),
            ($treatment->total_actual_cost ?? 0) - ($treatment->discount ?? 0)
        );

        // Treatment cost breakdown
        $costBreakdown = [
            'estimated_cost' => $treatment->total_estimated_cost,
            'actual_cost' => $treatment->total_actual_cost,
            'session_costs' => $totalSessionCost,
            'session_paid' => $totalSessionPaid,
            'procedure_costs' => $totalProceduresCost,
            'procedure_paid' => $totalProceduresPaid,
            'discount' => $treatment->discount ?? 0,
            'final_estimated' => ($treatment->total_estimated_cost ?? 0) - ($treatment->discount ?? 0),
            'final_actual' => $finalActualCost
        ];

        // Payment calculations
        $paidAmount = $totalPaid;
        $balanceDue = max(0, $finalActualCost - $totalPaid);
        $paymentPercentage = $finalActualCost > 0 ? round(($totalPaid / $finalActualCost) * 100, 2) : 0;

        return view('backend.treatments.show', compact(
            'treatment',
            'sessionCosts',
            'procedureCosts',
            'costBreakdown',
            'subtotal',
            'paidAmount',
            'balanceDue',
            'paymentPercentage'
        ));
    }

    // =========================
    // EDIT TREATMENT
    // =========================
    public function edit(Treatment $treatment)
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();
        $appointments = Appointment::whereIn('status', ['completed', 'in_progress'])
            ->where(function ($query) use ($treatment) {
                $query->whereDoesntHave('treatment')
                    ->orWhere('id', $treatment->appointment_id);
            })
            ->get();

        $chairs = DentalChair::active()->get();

        return view('backend.treatments.edit', compact('treatment', 'patients', 'doctors', 'appointments', 'chairs'));
    }

    // =========================
    // UPDATE TREATMENT
    // =========================
    public function update(Request $request, Treatment $treatment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'treatment_type' => 'required|in:single_visit,multi_visit',
            'estimated_sessions' => 'required|integer|min:1',
            'completed_sessions' => 'required|integer|min:0|max:' . $request->estimated_sessions,
            'treatment_date' => 'required|date',
            'start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_end_date' => 'nullable|date|after_or_equal:start_date',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'nullable|string',
            'total_estimated_cost' => 'nullable|numeric|min:0',
            'total_actual_cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:planned,in_progress,completed,cancelled,on_hold',
            'followup_date' => 'nullable|date',
            'followup_notes' => 'nullable|string',
        ]);

        $treatment->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_id' => $request->appointment_id,
            'treatment_type' => $request->treatment_type,
            'estimated_sessions' => $request->estimated_sessions,
            'completed_sessions' => $request->completed_sessions,
            'treatment_date' => $request->treatment_date,
            'start_date' => $request->start_date,
            'expected_end_date' => $request->expected_end_date,
            'actual_end_date' => $request->actual_end_date,
            'diagnosis' => $request->diagnosis,
            'treatment_plan' => $request->treatment_plan,
            'total_estimated_cost' => $request->total_estimated_cost,
            'total_actual_cost' => $request->total_actual_cost,
            'discount' => $request->discount ?? 0,
            'status' => $request->status,
            'followup_date' => $request->followup_date,
            'followup_notes' => $request->followup_notes,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('backend.treatments.show', $treatment)
            ->with('success', 'Treatment updated successfully.');
    }

    // =========================
    // DELETE TREATMENT
    // =========================
    public function destroy(Treatment $treatment)
    {
        if ($treatment->procedures()->exists() || $treatment->invoices()->exists()) {
            return back()->with('error', 'Cannot delete treatment with associated procedures or invoices.');
        }

        $treatment->delete();

        return redirect()->route('backend.treatments.index')
            ->with('success', 'Treatment deleted successfully.');
    }

    // =========================
    // STATUS ACTIONS (start, complete, hold, resume, add session)
    // =========================
    public function start(Treatment $treatment)
    {
        if ($treatment->status != 'planned') {
            return back()->with('error', 'Only planned treatments can be started.');
        }

        $treatment->start();

        return back()->with('success', 'Treatment started successfully.');
    }

    public function complete(Treatment $treatment)
    {
        if (!in_array($treatment->status, ['planned', 'in_progress'])) {
            return back()->with('error', 'Only planned or in-progress treatments can be completed.');
        }

        $treatment->complete();

        return back()->with('success', 'Treatment completed successfully.');
    }

    public function cancel(Treatment $treatment)
    {
        if (!in_array($treatment->status, ['planned', 'in_progress'])) {
            return back()->with('error', 'Only planned or in-progress treatments can be cancelled.');
        }

        $treatment->cancel();

        return back()->with('success', 'Treatment cancelled successfully.');
    }

    public function hold(Treatment $treatment)
    {
        if (!in_array($treatment->status, ['planned', 'in_progress'])) {
            return back()->with('error', 'Only planned or in-progress treatments can be put on hold.');
        }

        $treatment->putOnHold();

        return back()->with('success', 'Treatment put on hold.');
    }

    public function resume(Treatment $treatment)
    {
        if ($treatment->status != 'on_hold') {
            return back()->with('error', 'Only treatments on hold can be resumed.');
        }

        $treatment->resume();

        return back()->with('success', 'Treatment resumed.');
    }

    public function addSession(Treatment $treatment)
    {
        if (!$treatment->canAddSession()) {
            return back()->with('error', 'Cannot add more sessions.');
        }

        $treatment->addSession(); // This likely doesn't create a TreatmentSession record
        return back()->with('success', 'Session added successfully.');
    }

    // =========================
    // QUICK CREATE TREATMENT (minimal)
    // =========================
    public function quickCreate(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'diagnosis' => 'required|string',
        ]);

        $treatment = Treatment::create([
            'treatment_code' => Treatment::generateTreatmentCode(),
            'patient_id' => $request->patient_id,
            'doctor_id' => auth()->user()->doctor->id ?? 1,
            'treatment_date' => now(),
            'diagnosis' => $request->diagnosis,
            'status' => 'planned',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'treatment' => [
                'id' => $treatment->id,
                'code' => $treatment->treatment_code,
                'patient' => $treatment->patient->full_name,
            ]
        ]);
    }

    // =========================
    // LIST TREATMENTS FOR A SPECIFIC PATIENT
    // =========================
    public function patientTreatments($patientId)
    {
        $patient = Patient::with('treatments')->findOrFail($patientId);

        $treatments = Treatment::with(['doctor.user', 'appointment'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.treatments.patient-treatments', compact('patient', 'treatments'));
    }


    // =========================
    // QUICK METHOD TO UPDATE SESSION COST IN TREATMENT
    // =========================
    public function updateSessionCost(Request $request, Treatment $treatment)
    {
        $request->validate([
            'session_id' => 'required|exists:treatment_sessions,id',
            'cost' => 'required|numeric|min:0'
        ]);

        $session = $treatment->sessions()->findOrFail($request->session_id);
        $session->update(['cost_for_session' => $request->cost]);

        // Recalculate treatment actual cost
        $this->recalculateTreatmentCost($treatment);

        return back()->with('success', 'Session cost updated successfully.');
    }

    // =========================
    // RECALCULATE TREATMENT COST (PRIVATE METHOD)
    // =========================
    private function recalculateTreatmentCost(Treatment $treatment)
    {
        $totalSessionCost = $treatment->sessions()->sum('cost_for_session');
        $totalProcedureCost = $treatment->procedures()->sum('cost');

        // Use whichever is appropriate for your logic
        $totalActualCost = max($totalSessionCost, $totalProcedureCost);

        $treatment->update([
            'total_actual_cost' => $totalActualCost
        ]);

        return $totalActualCost;
    }

    // =========================
    // GET TREATMENT COST SUMMARY (AJAX)
    // =========================
    public function getCostSummary(Treatment $treatment)
    {
        $treatment->load(['sessions', 'procedures']);

        $summary = [
            'session_costs' => $treatment->sessions->sum('cost_for_session'),
            'procedure_costs' => $treatment->procedures->sum('cost'),
            'estimated_cost' => $treatment->total_estimated_cost,
            'discount' => $treatment->discount ?? 0,
            'final_estimated' => ($treatment->total_estimated_cost ?? 0) - ($treatment->discount ?? 0),
            'final_actual' => ($treatment->total_actual_cost ?? 0) - ($treatment->discount ?? 0)
        ];

        return response()->json($summary);
    }

    // Session Payments View
    public function sessionPayments(Treatment $treatment)
    {
        $treatment->load(['sessions.payments']);

        return view('backend.payments.session-payments', compact('treatment'));
    }

    // Procedure Payments View
    public function procedurePayments(Treatment $treatment)
    {
        $treatment->load(['procedures.payments']);

        return view('backend.payments.procedure-payments', compact('treatment'));
    }
}
