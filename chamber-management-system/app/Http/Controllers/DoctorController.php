<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $doctors = Doctor::with('user')
            ->when($search, function ($query, $search) {
                return $query->search($search);
            })
            ->latest()
            ->paginate(15);

        return view('backend.doctors.index', compact('doctors', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get users who are not already doctors
        $doctorUserIds = Doctor::pluck('user_id')->toArray();
        $availableUsers = User::where('role_id', 3) // Doctor role
            ->whereNotIn('id', $doctorUserIds)
            ->orderBy('full_name')
            ->get();

        return view('backend.doctors.create', compact('availableUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'             => 'required|exists:users,id|unique:doctors,user_id',
            'designation'         => 'nullable|string|max:255',
            'specialization'      => 'nullable|string|max:255',
            'qualification'       => 'nullable|string|max:255',
            'experience_years'    => 'nullable|integer|min:0|max:60',
            'bio'                 => 'nullable|string',
            'consultation_fee'    => 'required|numeric|min:0',
            'commission_percent'  => 'required|numeric|min:0|max:100',
            'is_available'        => 'boolean',
            'is_featured'         => 'boolean',
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')
                ->store('doctors', 'public');
        }

        Doctor::create($validated);

        return redirect()
            ->route('backend.doctors.index')
            ->with('success', 'Doctor profile created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        $doctor->load('user');
        return view('backend.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        $doctor->load('user');
        return view('backend.doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'designation'         => 'nullable|string|max:255',
            'specialization'      => 'nullable|string|max:255',
            'qualification'       => 'nullable|string|max:255',
            'experience_years'    => 'nullable|integer|min:0|max:60',
            'bio'                 => 'nullable|string',
            'consultation_fee'    => 'required|numeric|min:0',
            'commission_percent'  => 'required|numeric|min:0|max:100',
            'is_available'        => 'boolean',
            'is_featured'         => 'boolean',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($doctor->photo && Storage::disk('public')->exists($doctor->photo)) {
                Storage::disk('public')->delete($doctor->photo);
            }

            $validated['photo'] = $request->file('photo')
                ->store('doctors', 'public');
        }

        $doctor->update($validated);

        return redirect()
            ->route('backend.doctors.index')
            ->with('success', 'Doctor profile updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        if ($doctor->photo && Storage::disk('public')->exists($doctor->photo)) {
            Storage::disk('public')->delete($doctor->photo);
        }

        $doctor->delete();

        return redirect()
            ->route('backend.doctors.index')
            ->with('success', 'Doctor profile deleted successfully.');
    }


    /**
     * Create doctor from new user.
     */
    public function createWithUser()
    {
        return view('backend.doctors.create-with-user');
    }

    /**
     * Store doctor with new user.
     */
    public function storeWithUser(Request $request)
    {
        // Validate user data
        $userData = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
        ]);

        // Validate doctor data
        $doctorData = $request->validate([
            'specialization' => 'nullable|string|max:255',
            'consultation_fee' => 'required|numeric|min:0',
            'commission_percent' => 'required|numeric|min:0|max:100',
        ]);

        // Create user with doctor role
        $userData['role_id'] = 3; // Doctor role ID
        $userData['name'] = $userData['full_name'];
        $userData['password'] = bcrypt($userData['password']);

        $user = User::create($userData);

        // Create doctor profile
        $doctorData['user_id'] = $user->id;
        Doctor::create($doctorData);

        return redirect()->route('backend.doctors.index')
            ->with('success', 'Doctor and user account created successfully.');
    }
}
