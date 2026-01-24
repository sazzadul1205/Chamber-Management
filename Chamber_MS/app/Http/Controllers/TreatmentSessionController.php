<?php

namespace App\Http\Controllers;

use App\Models\TreatmentSession;
use App\Models\Treatment;
use App\Models\Appointment;
use App\Models\DentalChair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreatmentSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = TreatmentSession::with(['treatment.patient', 'appointment', 'chair']);

        if ($request->filled('treatment_id')) {
            $query->where('treatment_id', $request->treatment_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        } else {
            $query->whereDate('scheduled_date', '>=', today());
        }

        if ($request->filled('chair_id')) {
            $query->where('chair_id', $request->chair_id);
        }

        $sessions = $query->orderBy('scheduled_date', 'desc')
            ->orderBy('session_number')
            ->paginate(20);

        $treatments = Treatment::active()->get();
        $chairs = DentalChair::active()->get();

        return view('treatment-sessions.index', compact('sessions', 'treatments', 'chairs'));
    }

    public function create(Request $request)
    {
        $treatment = null;
        $sessionNumber = 1;

        if ($request->filled('treatment_id')) {
            $treatment = Treatment::findOrFail($request->treatment_id);
            $sessionNumber = $treatment->sessions()->max('session_number') + 1;
        }

        $treatments = Treatment::active()->with('patient')->get();
        $appointments = Appointment::where('status', 'scheduled')->get();
        $chairs = DentalChair::active()->available()->get();

        return view('treatment-sessions.create', compact('treatment', 'sessionNumber', 'treatments', 'appointments', 'chairs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'session_number' => 'required|integer|min:1',
            'session_title' => 'required|string|max:100',
            'appointment_id' => 'nullable|exists:appointments,id',
            'scheduled_date' => 'required|date',
            'chair_id' => 'nullable|exists:dental_chairs,id',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'procedure_details' => 'nullable|string',
            'duration_planned' => 'required|integer|min:1|max:480',
            'cost_for_session' => 'nullable|numeric|min:0',
            'next_session_date' => 'nullable|date|after_or_equal:scheduled_date',
        ]);

        // Check for duplicate session number
        $exists = TreatmentSession::where('treatment_id', $request->treatment_id)
            ->where('session_number', $request->session_number)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Session number already exists for this treatment.')->withInput();
        }

        $session = TreatmentSession::create([
            'treatment_id' => $request->treatment_id,
            'session_number' => $request->session_number,
            'session_title' => $request->session_title,
            'appointment_id' => $request->appointment_id,
            'scheduled_date' => $request->scheduled_date,
            'actual_date' => $request->status === 'completed' ? ($request->actual_date ?? now()) : null,
            'chair_id' => $request->chair_id,
            'status' => $request->status,
            'procedure_details' => $request->procedure_details,
            'materials_used' => $request->materials_used,
            'doctor_notes' => $request->doctor_notes,
            'assistant_notes' => $request->assistant_notes,
            'duration_planned' => $request->duration_planned,
            'duration_actual' => $request->duration_actual,
            'cost_for_session' => $request->cost_for_session,
            'next_session_date' => $request->next_session_date,
            'next_session_notes' => $request->next_session_notes,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Update appointment status if linked
        if ($request->appointment_id && in_array($request->status, ['in_progress', 'completed'])) {
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment) {
                $appointment->update(['status' => $request->status === 'completed' ? 'completed' : 'in_progress']);
            }
        }

        return redirect()
            ->route('treatment-sessions.show', $session)
            ->with('success', 'Treatment session created successfully.');
    }

    public function show(TreatmentSession $treatmentSession)
    {
        $treatmentSession->load([
            'treatment.patient',
            'treatment.doctor.user',
            'appointment',
            'chair',
            'creator',
            'updater'
        ]);

        return view('treatment-sessions.show', compact('treatmentSession'));
    }

    public function edit(TreatmentSession $treatmentSession)
    {
        $treatmentSession->load('treatment');
        $treatments = Treatment::active()->with('patient')->get();
        $appointments = Appointment::where('status', 'scheduled')
            ->orWhere('id', $treatmentSession->appointment_id)
            ->get();
        $chairs = DentalChair::active()->get();

        return view('treatment-sessions.edit', compact('treatmentSession', 'treatments', 'appointments', 'chairs'));
    }

    public function update(Request $request, TreatmentSession $treatmentSession)
    {
        $request->validate([
            'session_number' => 'required|integer|min:1',
            'session_title' => 'required|string|max:100',
            'appointment_id' => 'nullable|exists:appointments,id',
            'scheduled_date' => 'required|date',
            'actual_date' => 'nullable|date',
            'chair_id' => 'nullable|exists:dental_chairs,id',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'procedure_details' => 'nullable|string',
            'duration_planned' => 'required|integer|min:1|max:480',
            'duration_actual' => 'nullable|integer|min:1|max:480',
            'cost_for_session' => 'nullable|numeric|min:0',
            'next_session_date' => 'nullable|date|after_or_equal:scheduled_date',
        ]);

        // Check for duplicate session number if changed
        if ($treatmentSession->session_number != $request->session_number) {
            $exists = TreatmentSession::where('treatment_id', $treatmentSession->treatment_id)
                ->where('session_number', $request->session_number)
                ->where('id', '!=', $treatmentSession->id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Session number already exists for this treatment.')->withInput();
            }
        }

        $oldStatus = $treatmentSession->status;
        $oldAppointmentId = $treatmentSession->appointment_id;

        $treatmentSession->update([
            'session_number' => $request->session_number,
            'session_title' => $request->session_title,
            'appointment_id' => $request->appointment_id,
            'scheduled_date' => $request->scheduled_date,
            'actual_date' => $request->actual_date,
            'chair_id' => $request->chair_id,
            'status' => $request->status,
            'procedure_details' => $request->procedure_details,
            'materials_used' => $request->materials_used,
            'doctor_notes' => $request->doctor_notes,
            'assistant_notes' => $request->assistant_notes,
            'duration_planned' => $request->duration_planned,
            'duration_actual' => $request->duration_actual,
            'cost_for_session' => $request->cost_for_session,
            'next_session_date' => $request->next_session_date,
            'next_session_notes' => $request->next_session_notes,
            'updated_by' => auth()->id(),
        ]);

        // Handle appointment status changes
        if ($request->appointment_id) {
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment && in_array($request->status, ['in_progress', 'completed'])) {
                $appointment->update(['status' => $request->status === 'completed' ? 'completed' : 'in_progress']);
            }
        }

        // Update old appointment if changed
        if ($oldAppointmentId && $oldAppointmentId != $request->appointment_id) {
            $oldAppointment = Appointment::find($oldAppointmentId);
            if ($oldAppointment && $oldAppointment->status === 'in_progress') {
                $oldAppointment->update(['status' => 'scheduled']);
            }
        }

        // Update treatment completed sessions if session completed
        if ($oldStatus !== 'completed' && $request->status === 'completed') {
            $treatmentSession->treatment->addSession();
        }

        return redirect()
            ->route('treatment-sessions.show', $treatmentSession)
            ->with('success', 'Treatment session updated successfully.');
    }

    public function destroy(TreatmentSession $treatmentSession)
    {
        if ($treatmentSession->status === 'completed') {
            return back()->with('error', 'Cannot delete completed session.');
        }

        // Update appointment if linked
        if ($treatmentSession->appointment_id && $treatmentSession->appointment->status === 'in_progress') {
            $treatmentSession->appointment->update(['status' => 'scheduled']);
        }

        $treatmentSession->delete();

        return redirect()
            ->route('treatments.show', $treatmentSession->treatment_id)
            ->with('success', 'Treatment session deleted successfully.');
    }

    public function start(TreatmentSession $treatmentSession)
    {
        if ($treatmentSession->start()) {
            return back()->with('success', 'Session started successfully.');
        }

        return back()->with('error', 'Cannot start session. It may already be started or completed.');
    }

    public function complete(TreatmentSession $treatmentSession)
    {
        $request = request();
        $request->validate([
            'duration_actual' => 'nullable|integer|min:1',
            'doctor_notes' => 'nullable|string',
            'materials_used' => 'nullable|string',
        ]);

        if ($request->filled('duration_actual')) {
            $treatmentSession->update(['duration_actual' => $request->duration_actual]);
        }

        if ($request->filled('doctor_notes')) {
            $treatmentSession->update(['doctor_notes' => $request->doctor_notes]);
        }

        if ($request->filled('materials_used')) {
            $treatmentSession->update(['materials_used' => $request->materials_used]);
        }

        if ($treatmentSession->complete()) {
            return back()->with('success', 'Session completed successfully.');
        }

        return back()->with('error', 'Cannot complete session. It may already be completed or cancelled.');
    }

    public function cancel(TreatmentSession $treatmentSession)
    {
        if ($treatmentSession->cancel()) {
            return back()->with('success', 'Session cancelled successfully.');
        }

        return back()->with('error', 'Cannot cancel session. It may already be completed.');
    }

    public function postpone(Request $request, TreatmentSession $treatmentSession)
    {
        $request->validate([
            'new_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($treatmentSession->postpone($request->new_date, $request->notes)) {
            return back()->with('success', 'Session postponed successfully.');
        }

        return back()->with('error', 'Cannot postpone session. It may not be scheduled.');
    }

    public function reschedule(Request $request, TreatmentSession $treatmentSession)
    {
        $request->validate([
            'new_date' => 'required|date',
        ]);

        if ($treatmentSession->reschedule($request->new_date)) {
            return back()->with('success', 'Session rescheduled successfully.');
        }

        return back()->with('error', 'Cannot reschedule session.');
    }

    public function treatmentSessions(Treatment $treatment)
    {
        $sessions = TreatmentSession::where('treatment_id', $treatment->id)
            ->orderBy('session_number')
            ->get();

        return view('treatment-sessions.treatment-sessions', compact('treatment', 'sessions'));
    }

    public function today()
    {
        $todaySessions = TreatmentSession::with(['treatment.patient', 'chair', 'appointment'])
            ->today()
            ->orderBy('scheduled_date')
            ->get()
            ->groupBy('status');

        $stats = [
            'scheduled' => $todaySessions->get('scheduled', collect())->count(),
            'in_progress' => $todaySessions->get('in_progress', collect())->count(),
            'completed' => $todaySessions->get('completed', collect())->count(),
            'total' => $todaySessions->flatten()->count(),
        ];

        return view('treatment-sessions.today', compact('todaySessions', 'stats'));
    }

    public function quickCreate(Request $request, Treatment $treatment)
    {
        $request->validate([
            'session_title' => 'required|string|max:100',
            'scheduled_date' => 'required|date',
        ]);

        $sessionNumber = $treatment->sessions()->max('session_number') + 1;

        $session = TreatmentSession::create([
            'treatment_id' => $treatment->id,
            'session_number' => $sessionNumber,
            'session_title' => $request->session_title,
            'scheduled_date' => $request->scheduled_date,
            'duration_planned' => $request->duration_planned ?? 30,
            'status' => 'scheduled',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'session' => [
                'id' => $session->id,
                'number' => $session->session_number,
                'title' => $session->session_title,
                'date' => $session->scheduled_date->format('d/m/Y'),
            ]
        ]);
    }
}
