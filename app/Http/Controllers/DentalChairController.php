<?php

namespace App\Http\Controllers;

use App\Models\DentalChair;
use Illuminate\Http\Request;

class DentalChairController extends Controller
{
    // =========================
    // LIST ALL CHAIRS WITH FILTERS
    // =========================
    public function index(Request $request)
    {
        $query = DentalChair::query();

        // Search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Paginate 20 per page
        $dentalChairs = $query->orderBy('name')->paginate(20);
        $statuses = DentalChair::statuses();

        // Statistics
        $totalChairs = DentalChair::count();
        $availableChairs = DentalChair::available()->count();
        $occupiedChairs = DentalChair::occupied()->count();
        $maintenanceChairs = DentalChair::underMaintenance()->count();

        return view('backend.dental_chairs.index', compact(
            'dentalChairs',
            'statuses',
            'totalChairs',
            'availableChairs',
            'occupiedChairs',
            'maintenanceChairs'
        ));
    }

    // =========================
    // CREATE NEW CHAIR FORM
    // =========================
    public function create()
    {
        $statuses = DentalChair::statuses();
        return view('backend.dental_chairs.create', compact('statuses'));
    }

    // =========================
    // STORE NEW CHAIR
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'chair_code' => 'required|string|max:20|unique:dental_chairs',
            'name' => 'required|string|max:50',
            'location' => 'nullable|string|max:100',
            'status' => 'required|in:' . implode(',', array_keys(DentalChair::statuses())),
            'notes' => 'nullable|string'
        ]);

        DentalChair::create($request->all());

        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair added successfully.');
    }

    // =========================
    // SHOW SINGLE CHAIR
    // =========================
    public function show(DentalChair $dentalChair)
    {
        // Load related appointments & current appointment details
        $dentalChair->load([
            'appointments.patient',
            'appointments.doctor.user',
            'currentAppointment.patient',
            'currentAppointment.doctor.user'
        ]);

        return view('backend.dental_chairs.show', compact('dentalChair'));
    }

    // =========================
    // EDIT CHAIR FORM
    // =========================
    public function edit(DentalChair $dentalChair)
    {
        $statuses = DentalChair::statuses();

        // Last used date display
        $lastUsed = $dentalChair->last_used
            ? $dentalChair->last_used->format('d M Y, h:i A')
            : 'Never';

        return view('backend.dental_chairs.edit', compact(
            'dentalChair',
            'statuses',
            'lastUsed'
        ));
    }

    // =========================
    // UPDATE CHAIR
    // =========================
    public function update(Request $request, DentalChair $dentalChair)
    {
        $request->validate([
            'chair_code' => 'required|string|max:20|unique:dental_chairs,chair_code,' . $dentalChair->id,
            'name' => 'required|string|max:50',
            'location' => 'nullable|string|max:100',
            'status' => 'required|in:' . implode(',', array_keys(DentalChair::statuses())),
            'notes' => 'nullable|string'
        ]);

        $dentalChair->update($request->all());

        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair updated successfully.');
    }

    // =========================
    // DELETE CHAIR
    // =========================
    public function destroy(DentalChair $dentalChair)
    {
        if ($dentalChair->appointments()->exists()) {
            return redirect()->route('backend.dental-chairs.index')
                ->with('error', 'Cannot delete dental chair. It has appointment history.');
        }

        $dentalChair->delete();
        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair deleted successfully.');
    }

    // =========================
    // API: GET AVAILABLE CHAIRS
    // =========================
    public function getAvailableChairs()
    {
        $chairs = DentalChair::available()
            ->orderBy('name')
            ->get(['id', 'chair_code', 'name', 'location']);

        return response()->json($chairs);
    }

    // =========================
    // UPDATE CHAIR STATUS (API)
    // =========================
    public function updateStatus(Request $request, DentalChair $dentalChair)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(DentalChair::statuses())),
            'notes' => 'nullable|string'
        ]);

        $dentalChair->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $dentalChair->notes,
            'last_used' => $request->status === 'occupied' ? now() : $dentalChair->last_used
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chair status updated successfully.'
        ]);
    }

    // =========================
    // QUICK STATUS CHANGE (UI)
    // =========================
    public function quickStatusChange(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,maintenance,cleaning'
        ]);

        $chair = DentalChair::findOrFail($id);
        $chair->update([
            'status' => $request->status,
            'last_used' => in_array($request->status, ['occupied', 'available']) ? now() : $chair->last_used
        ]);

        return redirect()->back()
            ->with('success', 'Chair status updated to ' . $request->status . '.');
    }

    // =========================
    // GENERATE NEXT CHAIR CODE
    // =========================
    public function generateCode()
    {
        $lastChair = DentalChair::orderBy('chair_code', 'desc')->first();
        $nextNumber = 1;

        if ($lastChair && preg_match('/CHAIR-(\d+)/', $lastChair->chair_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        }

        $newCode = 'CHAIR-' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        return response()->json(['code' => $newCode]);
    }

    // =========================
    // DASHBOARD VIEW
    // =========================
    public function dashboard()
    {
        $chairs = DentalChair::with('currentAppointment.patient', 'currentAppointment.doctor.user')
            ->orderBy('location')
            ->get();

        return view('backend.dental_chairs.dashboard', compact('chairs'));
    }

    // =========================
    // SCHEDULE VIEW FOR SPECIFIC DATE
    // =========================
    public function schedule(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $chairs = DentalChair::all();

        return view('backend.dental_chairs.schedule', compact('chairs', 'date'));
    }
}
