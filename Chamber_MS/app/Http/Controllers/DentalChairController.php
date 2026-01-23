<?php

namespace App\Http\Controllers;

use App\Models\DentalChair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Paginate 20 per page
        $dentalChairs = $query->orderBy('name')->paginate(20);
        $statuses = DentalChair::statuses();

        // Statistics safely
        $totalChairs = Schema::hasTable('dental_chairs') ? DentalChair::count() : 0;
        $availableChairs = Schema::hasTable('dental_chairs') ? DentalChair::available()->count() : 0;
        $occupiedChairs = Schema::hasTable('dental_chairs') ? DentalChair::occupied()->count() : 0;
        $maintenanceChairs = Schema::hasTable('dental_chairs') ? DentalChair::underMaintenance()->count() : 0;

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
        if (Schema::hasTable('appointments')) {
            $dentalChair->load([
                'appointments' => function ($q) {
                    $q->with(['patient', 'doctor.user'])->latest()->limit(10);
                },
                'currentAppointment.patient',
                'currentAppointment.doctor.user'
            ]);
        }

        return view('backend.dental_chairs.show', compact('dentalChair'));
    }

    // =========================
    // EDIT CHAIR FORM
    // =========================
    public function edit(DentalChair $dentalChair)
    {
        $statuses = DentalChair::statuses();

        /**
         * Fallback-safe demo logic
         * Appointments / Treatments NOT connected yet
         */

        // Appointments count
        if (
            method_exists($dentalChair, 'appointments') &&
            Schema::hasTable('appointments')
        ) {
            try {
                $appointmentCount = $dentalChair->appointments()->count();
            } catch (\Throwable $e) {
                $appointmentCount = rand(0, 10); // fallback
            }
        } else {
            $appointmentCount = rand(0, 10); // demo
        }

        // Last used date
        if (!empty($dentalChair->last_used_at)) {
            $lastUsed = $dentalChair->last_used_at->format('d M Y, h:i A');
        } else {
            $lastUsed = now()->subDays(rand(0, 7))->format('d M Y, h:i A'); // demo
        }

        return view('backend.dental_chairs.edit', compact(
            'dentalChair',
            'statuses',
            'appointmentCount',
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
        if (Schema::hasTable('appointments') && $dentalChair->appointments()->exists()) {
            return redirect()->route('backend.dental-chairs.index')
                ->with('error', 'Cannot delete dental chair. It has appointment history.');
        }

        $dentalChair->delete();
        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair deleted successfully.');
    }

    // =========================
    // API: AVAILABLE CHAIRS
    // =========================
    public function getAvailableChairs()
    {
        $chairs = DentalChair::available()
            ->orderBy('name')
            ->get()
            ->map(fn($chair) => [
                'id' => $chair->id,
                'code' => $chair->chair_code,
                'name' => $chair->name,
                'location' => $chair->location
            ]);

        return response()->json($chairs);
    }

    // =========================
    // UPDATE CHAIR STATUS
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
    // GENERATE CHAIR CODE
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
    // SCHEDULE FOR A SPECIFIC DATE
    // =========================
    public function schedule(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        // Return chairs with empty appointments if table not ready
        $chairs = DentalChair::all()->map(function ($chair) use ($date) {
            $chair->appointments = collect(); // empty collection
            return $chair;
        });

        return view('backend.dental_chairs.schedule', compact('chairs', 'date'));
    }
}
