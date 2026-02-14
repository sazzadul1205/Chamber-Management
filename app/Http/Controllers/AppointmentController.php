<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DentalChair;
use App\Models\OnlineBooking;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * =========================================================================
     * LIST APPOINTMENTS WITH FILTERS
     * =========================================================================
     * 
     * Display all appointments with optional filtering options for
     * - Search term
     * - Appointment status
     * - Doctor selection
     * - Date range (defaults to today and future)
     * - Appointment type
     * 
     * @param Request $request HTTP request containing filter parameters
     * @return \Illuminate\View\View Appointments index page with filtered results
     */
    public function index(Request $request)
    {
        // Build base query with relationships
        $query = Appointment::with(['patient', 'doctor.user', 'chair']);
        $onlineQuery = OnlineBooking::query();

        // Apply filters based on request parameters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Date filter (default to today and future appointments)
        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        } else {
            $query->whereDate('appointment_date', '>=', today());
        }

        if ($request->filled('type')) {
            $query->where('appointment_type', $request->type);
        }

        if ($request->filled('online_status')) {
            $onlineQuery->where('status', $request->online_status);
        }

        if ($request->filled('online_search')) {
            $search = $request->online_search;
            $onlineQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Execute query with pagination
        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time')
            ->paginate(20);
        $onlineBookings = $onlineQuery
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'online_page')
            ->withQueryString();

        // Get dropdown data for filters
        $doctors = Doctor::with('user')->active()->get();
        $patients = Patient::active()->get();

        return view('backend.appointments.index', compact('appointments', 'doctors', 'patients', 'onlineBookings'));
    }

    /**
     * =========================================================================
     * CREATE APPOINTMENT FORM
     * =========================================================================
     * 
     * Display the form for creating a new appointment.
     * Provides dropdowns for selecting active patients, doctors, and available chairs.
     * 
     * @return \Illuminate\View\View Appointment creation form
     */
    public function create()
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::active()->available()->get();

        return view('backend.appointments.create', compact('patients', 'doctors', 'chairs'));
    }

    /**
     * =========================================================================
     * STORE NEW APPOINTMENT
     * =========================================================================
     * 
     * Validate and store a new appointment with the following validations:
     * - Patient and doctor existence
     * - Appointment type validation
     * - Date and time format validation
     * - Duration limits (5-240 minutes)
     * - Priority validation
     * 
     * Also handles:
     * - Daily doctor appointment limit check
     * - Queue number assignment
     * - Appointment code generation
     * 
     * @param Request $request HTTP request with appointment data
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'        => 'required|exists:patients,id',
            'doctor_id'         => 'required|exists:doctors,id',
            'chair_id'          => 'nullable|exists:dental_chairs,id',
            'appointment_type'  => 'required|in:consultation,treatment,followup,emergency,checkup',
            'appointment_date'  => 'required|date',
            'appointment_time'  => 'required|date_format:H:i',
            'expected_duration' => 'required|integer|min:5|max:240',
            'priority'          => 'required|in:normal,urgent,high',
            'chief_complaint'   => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        // -------------------------------
        // CHECK DAILY LIMIT (FIFO-STYLE)
        // -------------------------------
        $dailyLimitReached = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->whereIn('status', ['scheduled', 'checked_in', 'in_progress'])
            ->count() >= 100; // adjust daily cap as needed

        if ($dailyLimitReached) {
            return back()->with('error', 'Doctor has reached daily patient limit.')->withInput();
        }

        // -------------------------------
        // CALCULATE QUEUE NUMBER
        // -------------------------------
        $queueNumber = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->whereIn('status', ['scheduled', 'checked_in', 'in_progress'])
            ->max('queue_no');

        $queueNumber = $queueNumber ? $queueNumber + 1 : 1;

        // -------------------------------
        // CREATE APPOINTMENT
        // -------------------------------
        Appointment::create([
            'appointment_code'   => Appointment::generateAppointmentCode(),
            'patient_id'         => $request->patient_id,
            'doctor_id'          => $request->doctor_id,
            'chair_id'           => $request->chair_id,
            'appointment_type'   => $request->appointment_type,
            'appointment_date'   => $request->appointment_date,
            'appointment_time'   => $request->appointment_time,
            'expected_duration'  => $request->expected_duration,
            'priority'           => $request->priority,
            'queue_no'           => $queueNumber,
            'status'             => 'scheduled',
            'chief_complaint'    => $request->chief_complaint,
            'notes'              => $request->notes,
            'created_by'         => auth()->id(),
            'updated_by'         => auth()->id(),
        ]);

        return redirect()->route('backend.appointments.index')
            ->with('success', "Appointment created successfully. Queue number: {$queueNumber}");
    }

    /**
     * =========================================================================
     * SHOW APPOINTMENT DETAILS
     * =========================================================================
     * 
     * Display detailed view of a specific appointment.
     * Loads all related data including patient, doctor, chair, and user information.
     * 
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\View\View Appointment details page
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor.user', 'chair', 'creator', 'updater']);
        return view('backend.appointments.show', compact('appointment'));
    }

    /**
     * =========================================================================
     * EDIT APPOINTMENT FORM
     * =========================================================================
     * 
     * Display the form for editing an existing appointment.
     * Pre-fills form with current appointment data.
     * 
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\View\View Appointment edit form
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::active()->get();

        return view('backend.appointments.edit', compact('appointment', 'patients', 'doctors', 'chairs'));
    }

    /**
     * =========================================================================
     * UPDATE APPOINTMENT
     * =========================================================================
     * 
     * Validate and update an existing appointment.
     * Handles status-based timestamps and cancellation reasons.
     * 
     * @param Request $request HTTP request with updated appointment data
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'chair_id'         => 'nullable|exists:dental_chairs,id',
            'appointment_type' => 'required|in:consultation,treatment,followup,emergency,checkup',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'expected_duration' => 'required|integer|min:5|max:240',
            'priority'         => 'required|in:normal,urgent,high',
            'status'           => 'required|in:scheduled,checked_in,in_progress,completed,cancelled,no_show',
            'chief_complaint'  => 'nullable|string',
            'notes'            => 'nullable|string',
            'reason_cancellation' => 'nullable|required_if:status,cancelled|string',
        ]);

        $updateData = $request->only([
            'patient_id',
            'doctor_id',
            'chair_id',
            'appointment_type',
            'appointment_date',
            'appointment_time',
            'expected_duration',
            'priority',
            'status',
            'chief_complaint',
            'notes'
        ]) + ['updated_by' => auth()->id()];

        // Update timestamps based on status changes
        if ($request->status == 'checked_in' && !$appointment->checked_in_time) {
            $updateData['checked_in_time'] = now();
        }

        if ($request->status == 'in_progress' && !$appointment->started_time) {
            $updateData['started_time'] = now();
        }

        if ($request->status == 'completed' && !$appointment->completed_time) {
            $updateData['completed_time'] = now();
        }

        if ($request->status == 'cancelled') {
            $updateData['reason_cancellation'] = $request->reason_cancellation;
        }

        $appointment->update($updateData);

        return redirect()->route('backend.appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * =========================================================================
     * DELETE APPOINTMENT
     * =========================================================================
     * 
     * Soft delete an appointment. Only allowed for scheduled or cancelled appointments.
     * Prevents deletion of appointments that have started or completed.
     * 
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function destroy(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['scheduled', 'cancelled'])) {
            return back()->with('error', 'Cannot delete appointment that has started or completed.');
        }

        $appointment->delete();

        return redirect()->route('backend.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * =========================================================================
     * APPOINTMENT STATUS MANAGEMENT METHODS
     * =========================================================================
     */

    /**
     * Check in a scheduled appointment
     * 
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function checkIn(Appointment $appointment)
    {
        if ($appointment->status !== 'scheduled') {
            return back()->with('error', 'Only scheduled appointments can be checked in.');
        }

        $appointment->checkIn();

        return back()->with('success', 'Patient added to queue');
    }

    /**
     * Start an appointment that has been checked in
     * 
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function start(Appointment $appointment)
    {
        if ($appointment->status != 'checked_in') {
            return back()->with('error', 'Only checked-in appointments can be started.');
        }

        $appointment->start();
        return back()->with('success', 'Appointment started successfully.');
    }

    /**
     * Complete an appointment that is checked-in or in progress
     * 
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function complete(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['checked_in', 'in_progress'])) {
            return back()->with('error', 'Only checked-in or in-progress appointments can be completed.');
        }

        $appointment->complete();
        return back()->with('success', 'Appointment completed successfully.');
    }

    /**
     * Cancel an appointment with a reason
     * 
     * @param Request $request HTTP request containing cancellation reason
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $request->validate(['reason' => 'required|string']);

        if (!in_array($appointment->status, ['scheduled', 'checked_in'])) {
            return back()->with('error', 'Only scheduled or checked-in appointments can be cancelled.');
        }

        $appointment->cancel($request->reason);
        return back()->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * =========================================================================
     * TODAY'S APPOINTMENTS & STATISTICS
     * =========================================================================
     * 
     * Display all appointments for today, grouped by status.
     * Provides statistics for each status category.
     * 
     * @return \Illuminate\View\View Today's appointments dashboard
     */
    public function today()
    {
        $todayAppointments = Appointment::with(['patient', 'doctor.user', 'chair'])
            ->today()
            ->orderBy('appointment_time')
            ->get()
            ->groupBy('status');

        $stats = [
            'scheduled'   => $todayAppointments->get('scheduled', collect())->count(),
            'checked_in'  => $todayAppointments->get('checked_in', collect())->count(),
            'in_progress' => $todayAppointments->get('in_progress', collect())->count(),
            'completed'   => $todayAppointments->get('completed', collect())->count(),
            'total'       => $todayAppointments->flatten()->count(),
        ];

        return view('backend.appointments.today', compact('todayAppointments', 'stats'));
    }

    /**
     * =========================================================================
     * CALENDAR / QUEUE VIEW
     * =========================================================================
     * 
     * Display appointments in a calendar/queue view for a specific date.
     * Supports filtering by doctor and organizes appointments by time slots.
     * 
     * @param Request $request HTTP request with date and doctor filters
     * @return \Illuminate\View\View Calendar view with appointments
     */
    public function calendar(Request $request)
    {
        $doctors = Doctor::with('user')->active()->get();

        $selectedDoctor = $request->doctor_id;
        $selectedDate   = $request->date ?? now()->toDateString();

        $appointmentsQuery = Appointment::with([
            'patient',
            'doctor.user',
            'chair'
        ])
            ->whereDate('appointment_date', $selectedDate)
            ->orderBy('queue_no')
            ->orderBy('created_at');

        if ($selectedDoctor) {
            $appointmentsQuery->where('doctor_id', $selectedDoctor);
        }

        $appointments = $appointmentsQuery->get();

        // Generate time slots from 9 AM to 5:30 PM (30-minute intervals)
        $timeSlots = [];
        for ($hour = 9; $hour <= 17; $hour++) {
            foreach ([0, 30] as $minute) {
                if ($hour === 17 && $minute === 30) break;
                $time = sprintf('%02d:%02d:00', $hour, $minute);
                $timeSlots[$time] = $appointments
                    ->where('appointment_time', $time)
                    ->values();
            }
        }

        return view('backend.appointments.calendar', compact(
            'appointments',
            'timeSlots',
            'doctors',
            'selectedDoctor',
            'selectedDate'
        ));
    }

    /**
     * =========================================================================
     * GET AVAILABLE TIME SLOTS
     * =========================================================================
     * 
     * AJAX endpoint to get available time slots for a specific doctor on a date.
     * Returns JSON response with available time slots.
     * 
     * @param Request $request HTTP request with doctor_id and date
     * @return \Illuminate\Http\JsonResponse JSON array of available time slots
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date'      => 'required|date',
        ]);

        $doctorId = $request->doctor_id;
        $date = $request->date;

        $existingAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'checked_in', 'in_progress'])
            ->pluck('appointment_time')
            ->toArray();

        // Generate working hours (9 AM to 5:30 PM, 30-minute intervals)
        $workingHours = [];
        for ($hour = 9; $hour <= 17; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                if ($hour == 17 && $minute == 30) break;
                $workingHours[] = sprintf('%02d:%02d:00', $hour, $minute);
            }
        }

        $availableSlots = array_values(array_diff($workingHours, $existingAppointments));

        return response()->json(['slots' => $availableSlots]);
    }

    /**
     * =========================================================================
     * WALK-IN APPOINTMENT MANAGEMENT
     * =========================================================================
     */

    /**
     * Display walk-in appointment creation form
     * 
     * @return \Illuminate\View\View Walk-in appointment form
     */
    public function walkInForm()
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::active()->available()->get();

        return view('backend.appointments.walk-in', compact('patients', 'doctors', 'chairs'));
    }

    /**
     * Store a walk-in appointment
     * 
     * Creates an appointment with current date/time and auto-check-in.
     * Uses 'walkin' as schedule_type and generates queue number.
     * 
     * @param Request $request HTTP request with walk-in appointment data
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function walkInStore(Request $request)
    {
        $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'chair_id'         => 'nullable|exists:dental_chairs,id',
            'appointment_type' => 'required|in:consultation,treatment,followup,emergency,checkup',
            'expected_duration' => 'required|integer|min:5|max:240',
            'priority'         => 'required|in:normal,urgent,high',
            'chief_complaint'  => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        Appointment::create([
            'appointment_code'  => Appointment::generateAppointmentCode(),
            'patient_id'        => $request->patient_id,
            'doctor_id'         => $request->doctor_id,
            'chair_id'          => $request->chair_id,
            'appointment_type'  => $request->appointment_type,
            'schedule_type'     => 'walkin',
            'appointment_date'  => today(),
            'appointment_time'  => now()->format('H:i:s'),
            'expected_duration' => $request->expected_duration,
            'priority'          => $request->priority,
            'status'            => 'checked_in',
            'checked_in_time'   => now(),
            'queue_no'          => Appointment::nextQueueNumber(),
            'chief_complaint'   => $request->chief_complaint,
            'notes'             => $request->notes,
            'created_by'        => auth()->id(),
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->route('backend.appointments.index')
            ->with('success', 'Walk-in appointment registered and checked-in successfully.');
    }

    /**
     * =========================================================================
     * QUEUE DISPLAY (PUBLIC/CLINIC TV VIEW)
     * =========================================================================
     * 
     * Display appointment queue for public viewing (clinic TV screens).
     * Groups appointments by status and sorts by priority and queue number.
     * 
     * @param Request $request HTTP request with optional date parameter
     * @return \Illuminate\View\View Queue display view
     */
    public function queue(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');

        $appointments = Appointment::with(['patient', 'doctor.user'])
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'checked_in', 'in_progress'])
            ->orderByRaw("
            CASE priority
                WHEN 'high' THEN 1
                WHEN 'urgent' THEN 2
                ELSE 3
            END
        ")
            ->orderBy('queue_no')
            ->get()
            ->groupBy('status');

        return view('backend.appointments.queue', compact('appointments', 'date'));
    }

    /**
     * =========================================================================
     * RESCHEDULE APPOINTMENT
     * =========================================================================
     * 
     * Reschedule an appointment to a new date and time.
     * Resets status to 'scheduled' and checks daily doctor limit.
     * 
     * @param Request $request HTTP request with new date and time
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function reschedule(Request $request, Appointment $appointment)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // Check daily limit for the new date
        $dailyLimitReached = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->whereIn('status', ['scheduled', 'checked_in', 'in_progress'])
            ->count() >= 100;

        if ($dailyLimitReached) {
            return back()->with('error', 'Doctor has reached daily patient limit on this day.');
        }

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'scheduled',
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('backend.appointments.index')
            ->with('success', 'Appointment rescheduled successfully and status reset to Scheduled.');
    }

    /**
     * =========================================================================
     * MARK APPOINTMENT AS NO SHOW
     * =========================================================================
     * 
     * Mark a scheduled or checked-in appointment as 'no show'.
     * Prevents marking appointments that are already in progress or completed.
     * 
     * @param Appointment $appointment Appointment model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function noShow(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['scheduled', 'checked_in'])) {
            return redirect()->back()->with('error', 'Cannot mark this appointment as No Show.');
        }

        $appointment->status = 'no_show';
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment marked as No Show.');
    }
}
