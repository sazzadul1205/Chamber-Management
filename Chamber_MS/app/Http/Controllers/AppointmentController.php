<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DentalChair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    // =========================
    // LIST APPOINTMENTS
    // =========================
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.user', 'chair']);

        // Filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        } else {
            $query->whereDate('appointment_date', '>=', today());
        }

        if ($request->filled('type')) {
            $query->where('appointment_type', $request->type);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time')
            ->paginate(20);

        $doctors = Doctor::with('user')->active()->get();
        $patients = Patient::active()->get();

        return view('backend.appointments.index', compact('appointments', 'doctors', 'patients'));
    }

    // =========================
    // CREATE APPOINTMENT FORM
    // =========================
    public function create()
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::active()->available()->get();

        return view('backend.appointments.create', compact('patients', 'doctors', 'chairs'));
    }

    // =========================
    // STORE NEW APPOINTMENT
    // =========================
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
            ->count() >= 100; // adjust daily cap

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
            'chair_id'           => $request->chair_id, // optional
            'appointment_type'   => $request->appointment_type,
            'appointment_date'   => $request->appointment_date,
            'appointment_time'   => $request->appointment_time, // approximate arrival
            'expected_duration'  => $request->expected_duration,
            'priority'           => $request->priority,
            'queue_no'       => $queueNumber,
            'status'             => 'scheduled',
            'chief_complaint'    => $request->chief_complaint,
            'notes'              => $request->notes,
            'created_by'         => auth()->id(),
            'updated_by'         => auth()->id(),
        ]);

        return redirect()->route('backend.appointments.index')
            ->with('success', "Appointment created successfully. Queue number: {$queueNumber}");
    }

    // =========================
    // SHOW APPOINTMENT DETAILS
    // =========================
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor.user', 'chair', 'creator', 'updater']);
        return view('backend.appointments.show', compact('appointment'));
    }

    // =========================
    // EDIT APPOINTMENT
    // =========================
    public function edit(Appointment $appointment)
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::active()->get();

        return view('backend.appointments.edit', compact('appointment', 'patients', 'doctors', 'chairs'));
    }

    // =========================
    // UPDATE APPOINTMENT
    // =========================
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

        // Update timestamps based on status
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

    // =========================
    // DELETE APPOINTMENT
    // =========================
    public function destroy(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['scheduled', 'cancelled'])) {
            return back()->with('error', 'Cannot delete appointment that has started or completed.');
        }

        $appointment->delete();

        return redirect()->route('backend.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    // =========================
    // CHECK-IN / START / COMPLETE / CANCEL
    // =========================
    public function checkIn(Appointment $appointment)
    {
        if ($appointment->status !== 'scheduled') {
            return back()->with('error', 'Only scheduled appointments can be checked in.');
        }

        $appointment->checkIn();

        return back()->with('success', 'Patient added to queue');
    }


    public function start(Appointment $appointment)
    {
        if ($appointment->status != 'checked_in') {
            return back()->with('error', 'Only checked-in appointments can be started.');
        }

        $appointment->start();
        return back()->with('success', 'Appointment started successfully.');
    }

    public function complete(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['checked_in', 'in_progress'])) {
            return back()->with('error', 'Only checked-in or in-progress appointments can be completed.');
        }

        $appointment->complete();
        return back()->with('success', 'Appointment completed successfully.');
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $request->validate(['reason' => 'required|string']);

        if (!in_array($appointment->status, ['scheduled', 'checked_in'])) {
            return back()->with('error', 'Only scheduled or checked-in appointments can be cancelled.');
        }

        $appointment->cancel($request->reason);
        return back()->with('success', 'Appointment cancelled successfully.');
    }

    // =========================
    // TODAY'S APPOINTMENTS & STATS
    // =========================
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

    // =========================
    // CALENDAR / QUEUE VIEW
    // =========================
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
            ->orderBy('queue_no')        // FIFO first
            ->orderBy('created_at');     // safety fallback

        if ($selectedDoctor) {
            $appointmentsQuery->where('doctor_id', $selectedDoctor);
        }

        $appointments = $appointmentsQuery->get();

        // Optional reference slots (NOT strict scheduling)
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


    // =========================
    // AVAILABLE TIME SLOTS
    // =========================
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

    // =========================
    // WALK-IN FORM
    // =========================
    public function walkInForm()
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::active()->available()->get();

        return view('backend.appointments.walk-in', compact('patients', 'doctors', 'chairs'));
    }

    // =========================
    // STORE WALK-IN APPOINTMENT
    // =========================
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

        $now = now();

        // Auto-assign appointment for current date/time
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

    // =========================
    // QUEUE DISPLAY (TV)
    // =========================
    public function queue(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d'); // use requested date or today

        // Fetch appointments for the given date
        $appointments = Appointment::with(['patient', 'doctor.user'])
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'checked_in', 'in_progress']) // include scheduled
            ->orderByRaw("
            CASE priority
                WHEN 'high' THEN 1
                WHEN 'urgent' THEN 2
                ELSE 3
            END
        ")
            ->orderBy('queue_no')
            ->get()
            ->groupBy('status'); // group by status for Blade columns

        return view('backend.appointments.queue', compact('appointments', 'date'));
    }



    // =========================
    // RESCHEDULE APPOINTMENT
    // =========================
    public function reschedule(Request $request, Appointment $appointment)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // Optionally: Check if doctor has daily limit
        $dailyLimitReached = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->whereIn('status', ['scheduled', 'checked_in', 'in_progress'])
            ->count() >= 100;

        if ($dailyLimitReached) {
            return back()->with('error', 'Doctor has reached daily patient limit on this day.');
        }

        // Update appointment and reset status to scheduled
        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'scheduled', // reset status
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('backend.appointments.index')
            ->with('success', 'Appointment rescheduled successfully and status reset to Scheduled.');
    }


    // =========================
    // MARK APPOINTMENT AS NO SHOW
    // =========================
    public function noShow(Appointment $appointment)
    {
        // Only allow marking scheduled or checked-in appointments as no-show
        if (!in_array($appointment->status, ['scheduled', 'checked_in'])) {
            return redirect()->back()->with('error', 'Cannot mark this appointment as No Show.');
        }

        $appointment->status = 'no_show';
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment marked as No Show.');
    }
}
