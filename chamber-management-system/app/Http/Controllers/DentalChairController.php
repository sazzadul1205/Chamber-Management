<?php

namespace App\Http\Controllers;

use App\Models\DentalChair;
use Illuminate\Http\Request;

class DentalChairController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Simply get all chairs without appointments relationship
        $chairs = DentalChair::latest()->get();

        return view('backend.dental-chairs.index', compact('chairs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dental-chairs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:dental_chairs,name',
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        DentalChair::create($validated);

        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DentalChair $dentalChair)
    {
        // Show chair without appointments (for now)
        return view('backend.dental-chairs.show', compact('dentalChair'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DentalChair $dentalChair)
    {
        return view('backend.dental-chairs.edit', compact('dentalChair'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DentalChair $dentalChair)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:dental_chairs,name,' . $dentalChair->id,
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $dentalChair->update($validated);

        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DentalChair $dentalChair)
    {
        // We'll add appointment check later
        $dentalChair->delete();

        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair deleted successfully.');
    }

    /**
     * Update chair status only.
     */
    public function updateStatus(Request $request, DentalChair $dentalChair)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $dentalChair->update($validated);

        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Chair status updated successfully.');
    }

    /**
     * Show chair dashboard (real-time status).
     */
    public function dashboard()
    {
        $chairs = DentalChair::orderBy('name')->get();

        $availableChairs = $chairs->where('status', 'available')->count();
        $occupiedChairs = $chairs->where('status', 'occupied')->count();
        $maintenanceChairs = $chairs->where('status', 'maintenance')->count();

        return view('backend.dental-chairs.dashboard', compact(
            'chairs',
            'availableChairs',
            'occupiedChairs',
            'maintenanceChairs'
        ));
    }
}
