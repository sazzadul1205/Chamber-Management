<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with('user');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->specialization && $request->specialization !== 'all') {
            $query->where('specialization', $request->specialization);
        }

        $doctors = $query->orderBy('created_at', 'desc')->paginate(15);
        $specializations = Doctor::distinct()->pluck('specialization')->filter();

        return view('backend.doctors.index', compact('doctors', 'specializations'));
    }

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

        $doctor = Doctor::create($request->all());

        return redirect()
            ->route('backend.doctors.index')
            ->with('success', 'Doctor profile created successfully.');
    }

    public function show(Doctor $doctor)
    {
        // Ensure doctor exists (real model)
        $doctor = Doctor::with(['user'])->find($doctor->id);

        if (!$doctor) {
            abort(404, 'Doctor not found.');
        }

        // Safely load appointments and treatments
        try {
            $doctor->load([
                'appointments.patient' => function ($query) {
                    // optional: limit or order
                },
                'treatments.patient' => function ($query) {
                    // optional: limit or order
                },
            ]);
        } catch (\Exception $e) {
            // fallback empty collections if tables don't exist
            $doctor->appointments = collect();
            $doctor->treatments = collect();
        }

        return view('backend.doctors.show', compact('doctor'));
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

        return redirect()
            ->route('backend.doctors.index')
            ->with('success', 'Doctor profile updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        // Check appointments
        if (class_exists('App\Models\Appointment') && \Schema::hasTable('appointments')) {
            if ($doctor->appointments()->count() > 0) {
                return back()->with('error', 'Cannot delete doctor with appointments.');
            }
        }

        // Check treatments
        if (class_exists('App\Models\Treatment') && \Schema::hasTable('treatments')) {
            if ($doctor->treatments()->count() > 0) {
                return back()->with('error', 'Cannot delete doctor with treatments.');
            }
        }

        $doctor->delete();

        return redirect()
            ->route('backend.doctors.index')
            ->with('success', 'Doctor profile deleted.');
    }


    public function schedule(Request $request)
    {
        $doctors = Doctor::with(['appointments' => function ($query) use ($request) {
            if ($request->filled('date')) {
                $query->where('appointment_date', $request->date);
            }
        }])->get();

        $date = $request->get('date', now()->format('Y-m-d'));

        return view('backend.doctors.schedule', compact('doctors', 'date'));
    }


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
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->full_name,
                    'specialization' => $doctor->specialization,
                    'fee' => $doctor->consultation_fee,
                ];
            });

        return response()->json($doctors);
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
