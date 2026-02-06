<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\DoctorSchedule;
use App\Models\DoctorLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DoctorController extends Controller
{
    // =========================
    // LIST DOCTORS
    // =========================
    public function index(Request $request)
    {
        $query = Doctor::with(['user', 'activeSchedules']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('doctor_code', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('full_name', 'like', "%{$request->search}%"))
                    ->orWhere('specialization', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('specialization') && $request->specialization !== 'all') {
            $query->where('specialization', $request->specialization);
        }

        $doctors = $query->orderByDesc('created_at')->paginate(15);
        $specializations = Doctor::distinct()->pluck('specialization')->filter();

        return view('backend.doctors.index', compact('doctors', 'specializations'));
    }

    // =========================
    // DOCTOR SCHEDULE MANAGEMENT
    // =========================

    /** Show schedule management page */
    public function scheduleManagement(Doctor $doctor)
    {
        $doctor->load('schedules');
        $daysOfWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        $schedules = [];
        foreach ($daysOfWeek as $day) {
            $schedule = $doctor->schedules->where('day_of_week', $day)->first();
            $schedules[$day] = $schedule ?: new DoctorSchedule([
                'day_of_week' => $day,
                'is_active' => false,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'max_appointments' => 20,
                'slot_duration' => 30,
            ]);
        }

        return view('backend.doctors.schedule-management', compact('doctor', 'schedules', 'daysOfWeek'));
    }

    /** Update doctor schedule */
    public function updateSchedule(Request $request, Doctor $doctor)
    {
        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'schedules.*.is_active' => 'boolean',
            'schedules.*.start_time' => 'required_if:schedules.*.is_active,1|date_format:H:i',
            'schedules.*.end_time' => 'required_if:schedules.*.is_active,1|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.max_appointments' => 'required_if:schedules.*.is_active,1|integer|min:1|max:50',
            'schedules.*.slot_duration' => 'required_if:schedules.*.is_active,1|integer|min:15|max:120',
        ]);

        DB::transaction(function () use ($request, $doctor) {
            foreach ($request->schedules as $scheduleData) {
                $schedule = $doctor->schedules()
                    ->where('day_of_week', $scheduleData['day_of_week'])
                    ->first();

                if ($scheduleData['is_active'] ?? false) {
                    if ($schedule) {
                        $schedule->update([
                            'start_time' => $scheduleData['start_time'],
                            'end_time' => $scheduleData['end_time'],
                            'max_appointments' => $scheduleData['max_appointments'],
                            'slot_duration' => $scheduleData['slot_duration'],
                            'is_active' => true,
                        ]);
                    } else {
                        DoctorSchedule::create([
                            'doctor_id' => $doctor->id,
                            'day_of_week' => $scheduleData['day_of_week'],
                            'start_time' => $scheduleData['start_time'],
                            'end_time' => $scheduleData['end_time'],
                            'max_appointments' => $scheduleData['max_appointments'],
                            'slot_duration' => $scheduleData['slot_duration'],
                            'is_active' => true,
                        ]);
                    }
                } elseif ($schedule) {
                    $schedule->update(['is_active' => false]);
                }
            }
        });

        return redirect()->route('backend.doctors.schedule-management', $doctor)
            ->with('success', 'Schedule updated successfully.');
    }

    // =========================
    // DOCTOR LEAVE MANAGEMENT
    // =========================

    /** Show leave requests (for admin) */
    public function leaveRequests(Request $request)
    {
        $query = DoctorLeave::with(['doctor.user', 'approver'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('leave_date', $request->date);
        }

        $leaves = $query->paginate(20);
        $doctors = Doctor::with('user')->active()->get();

        return view('backend.doctors.leave-requests', compact('leaves', 'doctors'));
    }

    /** Show doctor's own leave requests */
    public function myLeaves(Request $request)
    {
        $doctor = Doctor::where('user_id', auth()->id())->firstOrFail();

        $leaves = $doctor->leaves()
            ->orderByDesc('leave_date')
            ->paginate(15);

        return view('backend.doctors.my-leaves', compact('doctor', 'leaves'));
    }

    /** Apply for leave */
    public function applyLeave(Request $request)
    {
        $doctor = Doctor::where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'leave_date' => 'required|date|after:yesterday',
            'type' => 'required|in:full_day,half_day,emergency',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Check if leave already exists for this date
        $existingLeave = $doctor->leaves()
            ->whereDate('leave_date', $request->leave_date)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->exists();

        if ($existingLeave) {
            return back()->with('error', 'You already have a leave request for this date.');
        }

        // Check if doctor is scheduled to work that day
        $dayOfWeek = strtolower(Carbon::parse($request->leave_date)->format('l'));
        $isScheduled = $doctor->activeSchedules()->where('day_of_week', $dayOfWeek)->exists();

        if (!$isScheduled && $request->type != 'emergency') {
            return back()->with('error', 'You are not scheduled to work on this day.');
        }

        DoctorLeave::create([
            'doctor_id' => $doctor->id,
            'leave_date' => $request->leave_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('backend.doctors.my-leaves')
            ->with('success', 'Leave application submitted successfully.');
    }

    /** Approve/Reject leave */
    public function processLeave(Request $request, DoctorLeave $leave)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string|max:255',
        ]);

        DB::transaction(function () use ($request, $leave) {
            if ($request->action === 'approve') {
                $leave->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);

                // Cancel appointments for this day
                $leave->doctor->appointments()
                    ->whereDate('appointment_date', $leave->leave_date)
                    ->where('status', 'scheduled')
                    ->update([
                        'status' => 'cancelled',
                        'cancellation_reason' => 'Doctor on approved leave',
                    ]);
            } else {
                $leave->update([
                    'status' => 'rejected',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'rejection_reason' => $request->rejection_reason,
                ]);
            }
        });

        return back()->with('success', 'Leave request ' . $request->action . 'd successfully.');
    }

    /** Cancel leave request */
    public function cancelLeave(DoctorLeave $leave)
    {
        if ($leave->doctor->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($leave->status, ['pending', 'approved'])) {
            return back()->with('error', 'Only pending or approved leaves can be cancelled.');
        }

        $leave->update(['status' => 'cancelled']);

        // If approved leave was cancelled, reactivate appointments
        if ($leave->status == 'approved') {
            $leave->doctor->appointments()
                ->whereDate('appointment_date', $leave->leave_date)
                ->where('status', 'cancelled')
                ->where('cancellation_reason', 'Doctor on approved leave')
                ->update([
                    'status' => 'scheduled',
                    'cancellation_reason' => null,
                ]);
        }

        return back()->with('success', 'Leave request cancelled successfully.');
    }

    // =========================
    // AVAILABILITY CHECK
    // =========================

    /** Check doctor availability */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after:today',
            'time' => 'nullable|date_format:H:i',
        ]);

        $doctor = Doctor::findOrFail($request->doctor_id);

        if ($request->time) {
            $isAvailable = $doctor->isAvailable($request->date, $request->time);
            $message = $isAvailable
                ? 'Doctor is available at this time.'
                : 'Doctor is not available at this time.';

            return response()->json([
                'available' => $isAvailable,
                'message' => $message,
            ]);
        } else {
            $slots = $doctor->getAvailableSlots($request->date);
            $dayOfWeek = strtolower(Carbon::parse($request->date)->format('l'));
            $schedule = $doctor->schedules()->where('day_of_week', $dayOfWeek)->first();

            return response()->json([
                'has_schedule' => (bool) $schedule,
                'is_on_leave' => $doctor->leaves()
                    ->whereDate('leave_date', $request->date)
                    ->where('status', 'approved')
                    ->exists(),
                'working_hours' => $schedule ? [
                    'start' => $schedule->start_time,
                    'end' => $schedule->end_time,
                ] : null,
                'available_slots' => $slots,
                'total_slots' => count($slots),
            ]);
        }
    }

    // =========================
    // CALENDAR VIEW
    // =========================

    /** Show doctor calendar */
    public function calendar(Request $request, Doctor $doctor = null)
    {
        $doctor = $doctor ?: Doctor::where('user_id', auth()->id())->first();

        if (!$doctor) {
            abort(404, 'Doctor not found.');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get appointments
        $appointments = $doctor->appointments()
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->get()
            ->groupBy('appointment_date');

        // Get leaves
        $leaves = $doctor->leaves()
            ->whereBetween('leave_date', [$startDate, $endDate])
            ->get()
            ->keyBy('leave_date');

        // Generate calendar data
        $calendar = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = strtolower($currentDate->format('l'));

            $isWorkingDay = $doctor->activeSchedules()
                ->where('day_of_week', $dayOfWeek)
                ->exists();

            $isOnLeave = $leaves->has($dateStr) &&
                $leaves[$dateStr]->status == 'approved';

            $calendar[$dateStr] = [
                'date' => $currentDate->copy(),
                'is_working_day' => $isWorkingDay,
                'is_on_leave' => $isOnLeave,
                'appointments' => $appointments[$dateStr] ?? collect(),
                'leave' => $leaves[$dateStr] ?? null,
                'is_today' => $currentDate->isToday(),
            ];

            $currentDate->addDay();
        }

        return view('backend.doctors.calendar', compact('doctor', 'calendar', 'startDate'));
    }

    // =========================
    // REST OF THE METHODS (keep existing)
    // =========================

    public function create()
    {
        $users = User::whereDoesntHave('doctor')
            ->where('role_id', 3)
            ->active()
            ->get();

        return view('backend.doctors.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:doctors,user_id',
            'doctor_code' => 'required|string|max:20|unique:doctors,doctor_code',
            'specialization' => 'nullable|string|max:100',
            'qualification' => 'nullable|string',
            'consultation_fee' => 'required|numeric|min:0',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        DB::transaction(function () use ($request) {
            $doctor = Doctor::create($request->all());

            // Create default schedule (Mon-Fri, 9-5)
            $defaultDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

            foreach ($defaultDays as $day) {
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'is_active' => true,
                    'max_appointments' => 20,
                    'slot_duration' => 30,
                ]);
            }
        });

        return redirect()->route('backend.doctors.index')
            ->with('success', 'Doctor profile created successfully with default schedule.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load([
            'user',
            'appointments.patient',
            'treatments.patient',
            'schedules',
            'leaves' => function ($query) {
                $query->whereDate('leave_date', '>=', now())
                    ->orderBy('leave_date');
            },
        ]);

        // Get statistics
        $monthlyStats = $doctor->appointments()
            ->selectRaw('MONTH(appointment_date) as month, COUNT(*) as count')
            ->whereYear('appointment_date', now()->year)
            ->groupBy('month')
            ->pluck('count', 'month');

        return view('backend.doctors.show', compact('doctor', 'monthlyStats'));
    }

    public function edit(Doctor $doctor)
    {
        return view('backend.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'doctor_code' => 'required|string|max:20|unique:doctors,doctor_code,' . $doctor->id,
            'specialization' => 'nullable|string|max:100',
            'qualification' => 'nullable|string',
            'consultation_fee' => 'required|numeric|min:0',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        $doctor->update($request->all());

        return redirect()->route('backend.doctors.index')
            ->with('success', 'Doctor profile updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        if ($doctor->appointments()->exists()) {
            return back()->with('error', 'Cannot delete doctor with appointments.');
        }

        if ($doctor->treatments()->exists()) {
            return back()->with('error', 'Cannot delete doctor with treatments.');
        }

        $doctor->delete();

        return redirect()->route('backend.doctors.index')
            ->with('success', 'Doctor profile deleted.');
    }

    public function generateCode()
    {
        $last = Doctor::orderByDesc('doctor_code')->first();
        $next = $last ? ((int) substr($last->doctor_code, 3)) + 1 : 1;

        return response()->json([
            'code' => 'DOC' . str_pad($next, 3, '0', STR_PAD_LEFT)
        ]);
    }
}
