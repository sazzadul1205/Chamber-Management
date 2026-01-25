<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    // =========================
    // LIST DOCTORS
    // =========================
    public function index(Request $request)
    {
        $query = Doctor::with('user');

        // Search by doctor code, name, or specialization
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('doctor_code', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('full_name', 'like', "%{$request->search}%"))
                    ->orWhere('specialization', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by specialization
        if ($request->filled('specialization') && $request->specialization !== 'all') {
            $query->where('specialization', $request->specialization);
        }

        $doctors = $query->orderByDesc('created_at')->paginate(15);
        $specializations = Doctor::distinct()->pluck('specialization')->filter();

        return view('backend.doctors.index', compact('doctors', 'specializations'));
    }

    // =========================
    // CREATE DOCTOR FORM
    // =========================
    public function create()
    {
        // Users without a doctor profile and role_id = 3 (assumed doctors)
        $users = User::whereDoesntHave('doctor')
            ->where('role_id', 3)
            ->active()
            ->get();

        return view('backend.doctors.create', compact('users'));
    }

    // =========================
    // STORE DOCTOR
    // =========================
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

        Doctor::create($request->all());

        return redirect()->route('backend.doctors.index')
            ->with('success', 'Doctor profile created successfully.');
    }

    // =========================
    // SHOW DOCTOR DETAILS
    // =========================
    public function show(Doctor $doctor)
    {
        $doctor->load([
            'user',
            'appointments.patient',
            'treatments.patient',
        ]);

        return view('backend.doctors.show', compact('doctor'));
    }

    // =========================
    // EDIT DOCTOR FORM
    // =========================
    public function edit(Doctor $doctor)
    {
        return view('backend.doctors.edit', compact('doctor'));
    }

    // =========================
    // UPDATE DOCTOR
    // =========================
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

    // =========================
    // DELETE DOCTOR
    // =========================
    public function destroy(Doctor $doctor)
    {
        // Prevent deletion if appointments or treatments exist
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

    // =========================
    // DOCTOR SCHEDULE
    // =========================
    public function schedule(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $doctors = Doctor::with(['appointments' => function ($query) use ($date) {
            $query->where('appointment_date', $date);
        }])->get();

        return view('backend.doctors.schedule', compact('doctors', 'date'));
    }

    // =========================
    // AVAILABLE DOCTORS API
    // =========================
    public function getAvailable(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $time = $request->get('time', now()->format('H:i'));

        $doctors = Doctor::with('user')
            ->active()
            ->whereDoesntHave('appointments', function ($query) use ($date, $time) {
                $query->where('appointment_date', $date)
                    ->where('appointment_time', $time)
                    ->whereIn('status', ['scheduled', 'checked_in']);
            })
            ->get()
            ->map(fn($doctor) => [
                'id' => $doctor->id,
                'name' => $doctor->user->full_name,
                'specialization' => $doctor->specialization,
                'fee' => $doctor->consultation_fee,
            ]);

        return response()->json($doctors);
    }

    // =========================
    // GENERATE UNIQUE DOCTOR CODE
    // =========================
    public function generateCode()
    {
        $last = Doctor::orderByDesc('doctor_code')->first();
        $next = $last ? ((int) substr($last->doctor_code, 3)) + 1 : 1;

        return response()->json([
            'code' => 'DOC' . str_pad($next, 3, '0', STR_PAD_LEFT)
        ]);
    }
}
