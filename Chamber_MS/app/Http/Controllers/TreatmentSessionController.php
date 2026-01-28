<?php

namespace App\Http\Controllers;

use App\Models\TreatmentSession;
use App\Models\Treatment;
use App\Models\Appointment;
use App\Models\DentalChair;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TreatmentSessionController extends Controller
{
    // =========================
    // LIST SESSIONS WITH FILTERS
    // =========================
    public function index(Request $request)
    {
        $query = TreatmentSession::with(['treatment.patient', 'appointment', 'chair']);

        // Filters - remove the default future date filter
        if ($request->filled('treatment_id')) {
            $query->where('treatment_id', $request->treatment_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        }
        // REMOVED: else $query->whereDate('scheduled_date', '>=', today());
        if ($request->filled('chair_id')) {
            $query->where('chair_id', $request->chair_id);
        }

        $sessions = $query->orderBy('scheduled_date', 'desc')->orderBy('session_number')->paginate(20);

        $treatments = Treatment::active()->get();
        $chairs = DentalChair::active()->get();

        return view('backend.treatment-sessions.index', compact('sessions', 'treatments', 'chairs'));
    }

    // =========================
    // SHOW CREATE FORM
    // =========================
    public function create(Request $request)
    {
        $treatment = null;
        $sessionNumber = 1;

        // Check if treatment_id is provided in query string
        if ($request->filled('treatment_id')) {
            $treatment = Treatment::find($request->treatment_id);
            if ($treatment) {
                $sessionNumber = $treatment->sessions()->max('session_number') + 1;
            }
        }

        $treatments = Treatment::active()->with('patient')->get();
        $appointments = Appointment::where('status', 'scheduled')->get();
        $chairs = DentalChair::active()->available()->get();

        return view('backend.treatment-sessions.create', [
            'treatment' => $treatment,
            'sessionNumber' => $sessionNumber,
            'treatments' => $treatments,
            'appointments' => $appointments,
            'chairs' => $chairs
        ]);
    }

    // =========================
    // SHOW CREATE FORM FOR TREATMENT
    // =========================
    public function createForTreatment(Treatment $treatment)
    {
        return redirect()->route('backend.treatment-sessions.create', [
            'treatment_id' => $treatment->id
        ]);
    }

    // =========================
    // STORE NEW SESSION
    // =========================
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

        // Prevent duplicate session numbers
        if (TreatmentSession::where('treatment_id', $request->treatment_id)
            ->where('session_number', $request->session_number)->exists()
        ) {
            return back()->with('error', 'Session number already exists for this treatment.')->withInput();
        }

        $session = TreatmentSession::create(array_merge(
            $request->only([
                'treatment_id',
                'session_number',
                'session_title',
                'appointment_id',
                'scheduled_date',
                'chair_id',
                'status',
                'procedure_details',
                'materials_used',
                'doctor_notes',
                'assistant_notes',
                'duration_planned',
                'duration_actual',
                'cost_for_session',
                'next_session_date',
                'next_session_notes'
            ]),
            [
                'actual_date' => $request->status === 'completed' ? ($request->actual_date ?? now()) : null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        ));

        // Sync appointment status
        if ($request->appointment_id && in_array($request->status, ['in_progress', 'completed'])) {
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment) $appointment->update(['status' => $request->status === 'completed' ? 'completed' : 'in_progress']);
        }

        return redirect()->route('backend.treatment-sessions.show', $session)
            ->with('success', 'Treatment session created successfully.');
    }

    // =========================
    // SHOW SESSION DETAILS
    // =========================
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

        return view('backend.treatment-sessions.show', compact('treatmentSession'));
    }

    // =========================
    // EDIT FORM
    // =========================
    public function edit(TreatmentSession $treatmentSession)
    {
        $treatmentSession->load('treatment');
        $treatments = Treatment::active()->with('patient')->get();
        $appointments = Appointment::where('status', 'scheduled')
            ->orWhere('id', $treatmentSession->appointment_id)->get();
        $chairs = DentalChair::active()->get();

        return view('backend.treatment-sessions.edit', compact('treatmentSession', 'treatments', 'appointments', 'chairs'));
    }

    // =========================
    // UPDATE SESSION
    // =========================
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

        // Prevent duplicate session number if changed
        if (
            $treatmentSession->session_number != $request->session_number &&
            TreatmentSession::where('treatment_id', $treatmentSession->treatment_id)
            ->where('session_number', $request->session_number)
            ->where('id', '!=', $treatmentSession->id)
            ->exists()
        ) {
            return back()->with('error', 'Session number already exists for this treatment.')->withInput();
        }

        $oldStatus = $treatmentSession->status;
        $oldAppointmentId = $treatmentSession->appointment_id;

        $treatmentSession->update(array_merge(
            $request->only([
                'session_number',
                'session_title',
                'appointment_id',
                'scheduled_date',
                'actual_date',
                'chair_id',
                'status',
                'procedure_details',
                'materials_used',
                'doctor_notes',
                'assistant_notes',
                'duration_planned',
                'duration_actual',
                'cost_for_session',
                'next_session_date',
                'next_session_notes'
            ]),
            ['updated_by' => auth()->id()]
        ));

        // Sync appointment statuses
        if ($request->appointment_id) {
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment && in_array($request->status, ['in_progress', 'completed'])) {
                $appointment->update(['status' => $request->status === 'completed' ? 'completed' : 'in_progress']);
            }
        }
        if ($oldAppointmentId && $oldAppointmentId != $request->appointment_id) {
            $oldAppointment = Appointment::find($oldAppointmentId);
            if ($oldAppointment && $oldAppointment->status === 'in_progress') {
                $oldAppointment->update(['status' => 'scheduled']);
            }
        }

        // Update treatment if session completed
        if ($oldStatus !== 'completed' && $request->status === 'completed') {
            $treatmentSession->treatment->addSession();
        }

        return redirect()->route('backend.treatment-sessions.show', $treatmentSession)
            ->with('success', 'Treatment session updated successfully.');
    }

    // =========================
    // DELETE SESSION
    // =========================
    public function destroy(TreatmentSession $treatmentSession)
    {
        if ($treatmentSession->status === 'completed') {
            return back()->with('error', 'Cannot delete completed session.');
        }

        // Reset appointment if needed
        if ($treatmentSession->appointment_id && $treatmentSession->appointment->status === 'in_progress') {
            $treatmentSession->appointment->update(['status' => 'scheduled']);
        }

        $treatmentSession->delete();

        // FIXED: Use correct route name
        return redirect()->route('backend.treatments.show', $treatmentSession->treatment_id)
            ->with('success', 'Treatment session deleted successfully.');
    }

    // =========================
    // ACTION METHODS - FIXED ROUTE PARAMETER
    // =========================
    public function start(TreatmentSession $session)  // Changed parameter to match route definition
    {
        try {
            $session->start();
            return back()->with('success', 'Session started successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start session: ' . $e->getMessage());
        }
    }

    public function complete(TreatmentSession $session)  // Changed parameter to match route definition
    {
        $request = request();
        $session->update($request->only(['duration_actual', 'doctor_notes', 'materials_used']));
        $session->complete();
        return back()->with('success', 'Session completed successfully.');
    }

    public function cancel(TreatmentSession $session)  // Changed parameter to match route definition
    {
        $session->cancel();
        return back()->with('success', 'Session cancelled successfully.');
    }

    public function postpone(Request $request, TreatmentSession $session)  // Changed parameter to match route definition
    {
        $request->validate(['new_date' => 'required|date', 'notes' => 'nullable|string']);
        $session->postpone($request->new_date, $request->notes);
        return back()->with('success', 'Session postponed successfully.');
    }

    public function reschedule(Request $request, TreatmentSession $session)  // Changed parameter to match route definition
    {
        $request->validate(['new_date' => 'required|date']);
        $session->reschedule($request->new_date);
        return back()->with('success', 'Session rescheduled successfully.');
    }

    // =========================
    // GET SESSIONS FOR A TREATMENT
    // =========================
    public function treatmentSessions(Treatment $treatment)
    {
        $sessions = TreatmentSession::where('treatment_id', $treatment->id)
            ->orderBy('session_number')
            ->get();

        return view('backend.treatment-sessions.treatment-sessions', compact('treatment', 'sessions'));
    }

    // =========================
    // TODAY'S SESSIONS
    // =========================
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

        return view('backend.treatment-sessions.today', compact('todaySessions', 'stats'));
    }

    // =========================
    // QUICK CREATE VIA AJAX
    // =========================
    public function quickCreate(Request $request, Treatment $treatment)
    {
        $request->validate(['session_title' => 'required|string|max:100', 'scheduled_date' => 'required|date']);

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
                'date' => Carbon::parse($session->scheduled_date)->format('d/m/Y'),
            ]
        ]);
    }
}
