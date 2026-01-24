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
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.user', 'chair']);

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

        return view('appointments.index', compact('appointments', 'doctors', 'patients'));
    }

    public function create()
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::active()->available()->get();

        return view('appointments.create', compact('patients', 'doctors', 'chairs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'chair_id' => 'nullable|exists:dental_chairs,id',
            'appointment_type' => 'required|in:consultation,treatment,followup,emergency,checkup',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'expected_duration' => 'required|integer|min:5|max:240',
            'priority' => 'required|in:normal,urgent,high',
            'chief_complaint' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Check for scheduling conflicts
        $conflict = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['scheduled', 'checked_in', 'in_progress'])
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Doctor is not available at this time. Please choose another time.')->withInput();
        }

        // Check chair availability if chair is selected
        if ($request->chair_id) {
            $chairConflict = Appointment::where('chair_id', $request->chair_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->whereIn('status', ['scheduled', 'checked_in', 'in_progress'])
                ->exists();

            if ($chairConflict) {
                return back()->with('error', 'Dental chair is not available at this time. Please choose another chair or time.')->withInput();
            }
        }

        $appointment = Appointment::create([
            'appointment_code' => Appointment::generateAppointmentCode(),
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'chair_id' => $request->chair_id,
            'appointment_type' => $request->appointment_type,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'expected_duration' => $request->expected_duration,
            'priority' => $request->priority,
            'chief_complaint' => $request->chief_complaint,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor.user', 'chair', 'creator', 'updater']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::active()->get();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors', 'chairs'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'chair_id' => 'nullable|exists:dental_chairs,id',
            'appointment_type' => 'required|in:consultation,treatment,followup,emergency,checkup',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'expected_duration' => 'required|integer|min:5|max:240',
            'priority' => 'required|in:normal,urgent,high',
            'status' => 'required|in:scheduled,checked_in,in_progress,completed,cancelled,no_show',
            'chief_complaint' => 'nullable|string',
            'notes' => 'nullable|string',
            'reason_cancellation' => 'nullable|required_if:status,cancelled|string',
        ]);

        // Update timestamps based on status changes
        $updateData = [
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'chair_id' => $request->chair_id,
            'appointment_type' => $request->appointment_type,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'expected_duration' => $request->expected_duration,
            'priority' => $request->priority,
            'status' => $request->status,
            'chief_complaint' => $request->chief_complaint,
            'notes' => $request->notes,
            'updated_by' => auth()->id(),
        ];

        // Handle status-specific timestamps
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

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['scheduled', 'cancelled'])) {
            return back()->with('error', 'Cannot delete appointment that has started or completed.');
        }

        $appointment->delete();

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    public function calendar(Request $request)
    {
        $doctors = Doctor::with('user')->active()->get();
        $selectedDoctor = $request->get('doctor_id');
        $selectedDate = $request->get('date', date('Y-m-d'));

        $query = Appointment::with(['patient', 'doctor.user', 'chair'])
            ->whereDate('appointment_date', $selectedDate);

        if ($selectedDoctor) {
            $query->where('doctor_id', $selectedDoctor);
        }

        $appointments = $query->orderBy('appointment_time')->get();

        // Group appointments by time slots
        $timeSlots = [];
        for ($hour = 9; $hour <= 17; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $time = sprintf('%02d:%02d:00', $hour, $minute);
                $timeSlots[$time] = $appointments->where('appointment_time', $time)->values();
            }
        }

        return view('appointments.calendar', compact('timeSlots', 'doctors', 'selectedDoctor', 'selectedDate'));
    }

    public function checkIn(Appointment $appointment)
    {
        if ($appointment->status != 'scheduled') {
            return back()->with('error', 'Only scheduled appointments can be checked in.');
        }

        $appointment->checkIn();

        return back()->with('success', 'Patient checked in successfully.');
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
        $request->validate([
            'reason' => 'required|string',
        ]);

        if (!in_array($appointment->status, ['scheduled', 'checked_in'])) {
            return back()->with('error', 'Only scheduled or checked-in appointments can be cancelled.');
        }

        $appointment->cancel($request->reason);

        return back()->with('success', 'Appointment cancelled successfully.');
    }

    public function today()
    {
        $todayAppointments = Appointment::with(['patient', 'doctor.user', 'chair'])
            ->today()
            ->orderBy('appointment_time')
            ->get()
            ->groupBy('status');

        $stats = [
            'scheduled' => $todayAppointments->get('scheduled', collect())->count(),
            'checked_in' => $todayAppointments->get('checked_in', collect())->count(),
            'in_progress' => $todayAppointments->get('in_progress', collect())->count(),
            'completed' => $todayAppointments->get('completed', collect())->count(),
            'total' => $todayAppointments->flatten()->count(),
        ];

        return view('appointments.today', compact('todayAppointments', 'stats'));
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
        ]);

        $date = $request->date;
        $doctorId = $request->doctor_id;

        // Get existing appointments for the doctor on the given date
        $existingAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'checked_in', 'in_progress'])
            ->pluck('appointment_time')
            ->toArray();

        // Define working hours (9 AM to 5 PM, 30-minute slots)
        $workingHours = [];
        for ($hour = 9; $hour <= 17; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $time = sprintf('%02d:%02d:00', $hour, $minute);
                if ($hour == 17 && $minute == 30) break; // Skip 5:30 PM

                $workingHours[] = $time;
            }
        }

        // Remove booked slots
        $availableSlots = array_diff($workingHours, $existingAppointments);

        return response()->json([
            'slots' => array_values($availableSlots)
        ]);
    }
}
