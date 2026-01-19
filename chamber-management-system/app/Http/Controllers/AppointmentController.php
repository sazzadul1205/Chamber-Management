<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DentalChair;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $date = request('date', today()->format('Y-m-d'));
        $status = request('status');

        $appointments = Appointment::with(['patient', 'doctor.user', 'chair'])
            ->when($search, function ($query, $search) {
                return $query->search($search);
            })
            ->when($date, function ($query, $date) {
                return $query->whereDate('appointment_date', $date);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time')
            ->paginate(20);

        return view('backend.appointments.index', compact('appointments', 'search', 'date', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::available()->get();

        // Default values
        $defaultDate = $request->get('date', today()->format('Y-m-d'));
        $defaultPatientId = $request->get('patient_id');

        // Time slots (30-minute intervals)
        $timeSlots = [];
        $startTime = Carbon::parse('09:00');
        $endTime = Carbon::parse('17:00');

        while ($startTime <= $endTime) {
            $timeSlots[] = $startTime->format('H:i');
            $startTime->addMinutes(30);
        }

        return view('backend.appointments.create', compact(
            'patients',
            'doctors',
            'chairs',
            'timeSlots',
            'defaultDate',
            'defaultPatientId'
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
            'chair_id' => 'required|exists:dental_chairs,id',
            'appointment_type' => 'required|in:slot,fifo',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'nullable|required_if:appointment_type,slot|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check chair availability for slot appointments
        if ($validated['appointment_type'] === 'slot') {
            $isChairAvailable = $this->checkChairAvailability(
                $validated['chair_id'],
                $validated['appointment_date'],
                $validated['appointment_time']
            );

            if (!$isChairAvailable) {
                return back()->withInput()
                    ->with('error', 'Selected chair is not available at the chosen time. Please select another time or chair.');
            }

            // Check doctor availability
            $isDoctorAvailable = $this->checkDoctorAvailability(
                $validated['doctor_id'],
                $validated['appointment_date'],
                $validated['appointment_time']
            );

            if (!$isDoctorAvailable) {
                return back()->withInput()
                    ->with('error', 'Doctor is not available at the chosen time. Please select another time.');
            }
        }

        Appointment::create($validated);

        return redirect()->route('backend.appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor.user', 'chair', 'creator', 'updater']);
        return view('backend.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::available()->get();

        // Time slots
        $timeSlots = [];
        $startTime = Carbon::parse('09:00');
        $endTime = Carbon::parse('17:00');

        while ($startTime <= $endTime) {
            $timeSlots[] = $startTime->format('H:i');
            $startTime->addMinutes(30);
        }

        return view('backend.appointments.edit', compact(
            'appointment',
            'patients',
            'doctors',
            'chairs',
            'timeSlots'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'chair_id' => 'required|exists:dental_chairs,id',
            'appointment_type' => 'required|in:slot,fifo',
            'appointment_date' => 'required|date',
            'appointment_time' => 'nullable|required_if:appointment_type,slot|date_format:H:i',
            'status' => 'required|in:scheduled,checked_in,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string|max:1000',
        ]);

        $appointment->update($validated);

        return redirect()->route('backend.appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('backend.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:checked_in,in_progress,completed,cancelled,no_show',
        ]);

        $appointment->update($validated);

        return redirect()->route('backend.appointments.show', $appointment)
            ->with('success', 'Appointment status updated successfully.');
    }

    /**
     * Check chair availability.
     */
    private function checkChairAvailability($chairId, $date, $time)
    {
        $appointmentTime = Carbon::parse($time);
        $appointmentEnd = $appointmentTime->copy()->addMinutes(30);

        $conflictingAppointments = Appointment::where('chair_id', $chairId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_type', 'slot')
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($appointmentTime, $appointmentEnd) {
                $query->where(function ($q) use ($appointmentTime, $appointmentEnd) {
                    $q->where('appointment_time', '>=', $appointmentTime->format('H:i:s'))
                        ->where('appointment_time', '<', $appointmentEnd->format('H:i:s'));
                })->orWhere(function ($q) use ($appointmentTime) {
                    $q->where('appointment_time', '<=', $appointmentTime->format('H:i:s'))
                        ->whereRaw("ADDTIME(appointment_time, '00:30:00') > ?", [$appointmentTime->format('H:i:s')]);
                });
            })
            ->exists();

        return !$conflictingAppointments;
    }

    /**
     * Check doctor availability.
     */
    private function checkDoctorAvailability($doctorId, $date, $time)
    {
        $appointmentTime = Carbon::parse($time);
        $appointmentEnd = $appointmentTime->copy()->addMinutes(30);

        $conflictingAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_type', 'slot')
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($appointmentTime, $appointmentEnd) {
                $query->where(function ($q) use ($appointmentTime, $appointmentEnd) {
                    $q->where('appointment_time', '>=', $appointmentTime->format('H:i:s'))
                        ->where('appointment_time', '<', $appointmentEnd->format('H:i:s'));
                })->orWhere(function ($q) use ($appointmentTime) {
                    $q->where('appointment_time', '<=', $appointmentTime->format('H:i:s'))
                        ->whereRaw("ADDTIME(appointment_time, '00:30:00') > ?", [$appointmentTime->format('H:i:s')]);
                });
            })
            ->exists();

        return !$conflictingAppointments;
    }

    /**
     * Get appointments calendar view.
     */
    public function calendar(Request $request)
    {


        $date = $request->get('date', today()->format('Y-m-d'));
        $doctorId = $request->get('doctor_id');

        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $appointments = Appointment::with(['patient', 'doctor.user', 'chair'])
            ->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])
            ->when($doctorId, function ($query, $doctorId) {
                return $query->where('doctor_id', $doctorId);
            })
            ->where('appointment_type', 'slot')
            ->orderBy('appointment_time')
            ->get()
            ->groupBy([
                function ($item) {
                    return \Carbon\Carbon::parse($item->appointment_date)->format('Y-m-d');
                },
                'chair_id'
            ]);

        $doctors = Doctor::with('user')->active()->get();
        $chairs = DentalChair::orderBy('name')->get();

        // Generate week days
        $weekDays = [];
        $currentDay = $startOfWeek->copy();

        while ($currentDay <= $endOfWeek) {
            $weekDays[] = [
                'date' => $currentDay->copy(),
                'formatted' => $currentDay->format('D, M j'),
                'is_today' => $currentDay->isToday(),
            ];
            $currentDay->addDay();
        }

        // Time slots
        $timeSlots = [];
        $startTime = Carbon::parse('09:00');
        $endTime = Carbon::parse('17:00');

        while ($startTime < $endTime) {
            $timeSlots[] = $startTime->copy();
            $startTime->addMinutes(30);
        }

        return view('backend.appointments.calendar', compact(
            'appointments',
            'weekDays',
            'timeSlots',
            'chairs',
            'doctors',
            'startOfWeek',
            'endOfWeek',
            'doctorId'
        ));
    }

    /**
     * Get today's appointments dashboard.
     */
    public function dashboard()
    {
        $today = today()->format('Y-m-d');

        // Today's appointments grouped by status
        $appointments = Appointment::with(['patient', 'doctor.user', 'chair'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get()
            ->groupBy('status');

        // Appointment counts
        $counts = [
            'scheduled' => $appointments->get('scheduled', collect())->count(),
            'checked_in' => $appointments->get('checked_in', collect())->count(),
            'in_progress' => $appointments->get('in_progress', collect())->count(),
            'completed' => $appointments->get('completed', collect())->count(),
            'total' => Appointment::whereDate('appointment_date', $today)->count(),
        ];


        // FIFO queue
        $fifoQueue = Appointment::with('patient')
            ->whereDate('appointment_date', $today)
            ->where('appointment_type', 'fifo')
            ->where('status', 'scheduled')
            ->orderBy('queue_no')
            ->get();

        // Available chairs
        $availableChairs = DentalChair::available()->count();
        $occupiedChairs = DentalChair::occupied()->count();

        return view('backend.appointments.dashboard', compact(
            'appointments',
            'counts',
            'fifoQueue',
            'availableChairs',
            'occupiedChairs',
            'today'
        ));
    }

    /**
     * Get available time slots for a given date and chair.
     */
    public function getAvailableSlots(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'chair_id' => 'required|exists:dental_chairs,id',
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $date = $validated['date'];
        $chairId = $validated['chair_id'];
        $doctorId = $validated['doctor_id'];

        // Get booked slots
        $bookedSlots = Appointment::whereDate('appointment_date', $date)
            ->where('appointment_type', 'slot')
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($chairId, $doctorId) {
                $query->where('chair_id', $chairId)
                    ->orWhere('doctor_id', $doctorId);
            })
            ->pluck('appointment_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        // Generate all time slots
        $allSlots = [];
        $startTime = Carbon::parse('09:00');
        $endTime = Carbon::parse('17:00');

        while ($startTime < $endTime) {
            $slot = $startTime->format('H:i');
            $allSlots[] = [
                'time' => $slot,
                'formatted' => $startTime->format('h:i A'),
                'available' => !in_array($slot, $bookedSlots),
            ];
            $startTime->addMinutes(30);
        }

        return response()->json($allSlots);
    }
}
