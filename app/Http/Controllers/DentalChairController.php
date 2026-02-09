<?php

namespace App\Http\Controllers;

use App\Models\DentalChair;
use Illuminate\Http\Request;

class DentalChairController extends Controller
{
    /**
     * =========================================================================
     * LIST ALL CHAIRS WITH FILTERS
     * =========================================================================
     * 
     * Display all dental chairs with search and status filtering.
     * Provides statistics for each status category.
     * 
     * Filters:
     * - Search term (searches chair_code, name, location)
     * - Status filter (available, occupied, maintenance, cleaning)
     * 
     * Statistics shown:
     * - Total chairs
     * - Available chairs
     * - Occupied chairs
     * - Chairs under maintenance
     * 
     * @param Request $request HTTP request with filter parameters
     * @return \Illuminate\View\View Dental chairs index page
     */
    public function index(Request $request)
    {
        // Build base query
        $query = DentalChair::query();

        // -------------------------------
        // APPLY FILTERS
        // -------------------------------
        // Search filter (assuming search scope is defined in model)
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter (exclude 'all' option)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Execute query with pagination
        $dentalChairs = $query->orderBy('name')->paginate(20);
        $statuses = DentalChair::statuses();

        // -------------------------------
        // CALCULATE STATISTICS
        // -------------------------------
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

    /**
     * =========================================================================
     * CREATE NEW CHAIR FORM
     * =========================================================================
     * 
     * Display form for creating a new dental chair.
     * Provides status options for initial chair configuration.
     * 
     * @return \Illuminate\View\View Dental chair creation form
     */
    public function create()
    {
        $statuses = DentalChair::statuses();
        return view('backend.dental_chairs.create', compact('statuses'));
    }

    /**
     * =========================================================================
     * STORE NEW CHAIR
     * =========================================================================
     * 
     * Validate and store a new dental chair record.
     * Ensures chair_code is unique.
     * 
     * @param Request $request HTTP request with chair data
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function store(Request $request)
    {
        $request->validate([
            'chair_code' => 'required|string|max:20|unique:dental_chairs',
            'name'       => 'required|string|max:50',
            'location'   => 'nullable|string|max:100',
            'status'     => 'required|in:' . implode(',', array_keys(DentalChair::statuses())),
            'notes'      => 'nullable|string'
        ]);

        DentalChair::create($request->all());

        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair added successfully.');
    }

    /**
     * =========================================================================
     * SHOW SINGLE CHAIR DETAILS
     * =========================================================================
     * 
     * Display detailed view of a specific dental chair.
     * Loads related appointments and current appointment if any.
     * 
     * @param DentalChair $dentalChair Dental chair model instance
     * @return \Illuminate\View\View Dental chair details page
     */
    public function show(DentalChair $dentalChair)
    {
        // Load relationships for detailed view
        $dentalChair->load([
            'appointments.patient',
            'appointments.doctor.user',
            'currentAppointment.patient',
            'currentAppointment.doctor.user'
        ]);

        return view('backend.dental_chairs.show', compact('dentalChair'));
    }

    /**
     * =========================================================================
     * EDIT CHAIR FORM
     * =========================================================================
     * 
     * Display form for editing an existing dental chair.
     * Shows last used date for reference.
     * 
     * @param DentalChair $dentalChair Dental chair model instance
     * @return \Illuminate\View\View Dental chair edit form
     */
    public function edit(DentalChair $dentalChair)
    {
        $statuses = DentalChair::statuses();

        // Format last used date for display
        $lastUsed = $dentalChair->last_used
            ? $dentalChair->last_used->format('d M Y, h:i A')
            : 'Never';

        return view('backend.dental_chairs.edit', compact(
            'dentalChair',
            'statuses',
            'lastUsed'
        ));
    }

    /**
     * =========================================================================
     * UPDATE CHAIR
     * =========================================================================
     * 
     * Validate and update an existing dental chair.
     * Ensures chair_code uniqueness (excluding current record).
     * 
     * @param Request $request HTTP request with updated chair data
     * @param DentalChair $dentalChair Dental chair model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function update(Request $request, DentalChair $dentalChair)
    {
        $request->validate([
            'chair_code' => 'required|string|max:20|unique:dental_chairs,chair_code,' . $dentalChair->id,
            'name'       => 'required|string|max:50',
            'location'   => 'nullable|string|max:100',
            'status'     => 'required|in:' . implode(',', array_keys(DentalChair::statuses())),
            'notes'      => 'nullable|string'
        ]);

        $dentalChair->update($request->all());

        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair updated successfully.');
    }

    /**
     * =========================================================================
     * DELETE CHAIR
     * =========================================================================
     * 
     * Delete a dental chair record.
     * Prevents deletion if chair has appointment history.
     * 
     * @param DentalChair $dentalChair Dental chair model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function destroy(DentalChair $dentalChair)
    {
        // Check if chair has appointment history
        if ($dentalChair->appointments()->exists()) {
            return redirect()->route('backend.dental-chairs.index')
                ->with('error', 'Cannot delete dental chair. It has appointment history.');
        }

        $dentalChair->delete();
        return redirect()->route('backend.dental-chairs.index')
            ->with('success', 'Dental chair deleted successfully.');
    }

    /**
     * =========================================================================
     * API: GET AVAILABLE CHAIRS
     * =========================================================================
     * 
     * AJAX endpoint to get all available dental chairs.
     * Used for dropdowns when assigning chairs to appointments.
     * 
     * @return \Illuminate\Http\JsonResponse JSON array of available chairs
     */
    public function getAvailableChairs()
    {
        $chairs = DentalChair::available()
            ->orderBy('name')
            ->get(['id', 'chair_code', 'name', 'location']);

        return response()->json($chairs);
    }

    /**
     * =========================================================================
     * UPDATE CHAIR STATUS (API)
     * =========================================================================
     * 
     * AJAX endpoint to update chair status.
     * Updates last_used timestamp when chair becomes occupied.
     * 
     * @param Request $request HTTP request with status update
     * @param DentalChair $dentalChair Dental chair model instance
     * @return \Illuminate\Http\JsonResponse JSON response with success/error
     */
    public function updateStatus(Request $request, DentalChair $dentalChair)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(DentalChair::statuses())),
            'notes'  => 'nullable|string'
        ]);

        // Prepare update data
        $updateData = [
            'status' => $request->status,
            'notes'  => $request->notes ?? $dentalChair->notes,
        ];

        // Update last_used timestamp when chair becomes occupied
        if ($request->status === 'occupied') {
            $updateData['last_used'] = now();
        }

        $dentalChair->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Chair status updated successfully.'
        ]);
    }

    /**
     * =========================================================================
     * QUICK STATUS CHANGE (UI)
     * =========================================================================
     * 
     * Quick status update from UI (e.g., buttons on dashboard).
     * Updates last_used for occupied/available status changes.
     * 
     * @param Request $request HTTP request with new status
     * @param int $id Dental chair ID
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function quickStatusChange(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,maintenance,cleaning'
        ]);

        $chair = DentalChair::findOrFail($id);
        
        // Prepare update data
        $updateData = ['status' => $request->status];
        
        // Update last_used for occupied or available status
        if (in_array($request->status, ['occupied', 'available'])) {
            $updateData['last_used'] = now();
        }

        $chair->update($updateData);

        return redirect()->back()
            ->with('success', 'Chair status updated to ' . $request->status . '.');
    }

    /**
     * =========================================================================
     * GENERATE NEXT CHAIR CODE
     * =========================================================================
     * 
     * AJAX endpoint to generate the next available chair code.
     * Format: CHAIR-01, CHAIR-02, etc.
     * 
     * @return \Illuminate\Http\JsonResponse JSON with generated code
     */
    public function generateCode()
    {
        // Find the last chair to determine next number
        $lastChair = DentalChair::orderBy('chair_code', 'desc')->first();
        $nextNumber = 1;

        // Extract number from last chair code if pattern matches
        if ($lastChair && preg_match('/CHAIR-(\d+)/', $lastChair->chair_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        }

        // Format with leading zeros (2 digits)
        $newCode = 'CHAIR-' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        return response()->json(['code' => $newCode]);
    }

    /**
     * =========================================================================
     * DASHBOARD VIEW
     * =========================================================================
     * 
     * Display real-time dental chair dashboard.
     * Shows current status and appointments for all chairs.
     * 
     * @return \Illuminate\View\View Dental chair dashboard
     */
    public function dashboard()
    {
        $chairs = DentalChair::with('currentAppointment.patient', 'currentAppointment.doctor.user')
            ->orderBy('location')
            ->get();

        return view('backend.dental_chairs.dashboard', compact('chairs'));
    }

    /**
     * =========================================================================
     * SCHEDULE VIEW FOR SPECIFIC DATE
     * =========================================================================
     * 
     * Display chair schedule for a specific date.
     * Shows appointments scheduled for each chair on the given date.
     * 
     * @param Request $request HTTP request with optional date parameter
     * @return \Illuminate\View\View Chair schedule view
     */
    public function schedule(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $chairs = DentalChair::all();

        return view('backend.dental_chairs.schedule', compact('chairs', 'date'));
    }
}