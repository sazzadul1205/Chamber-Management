<?php

namespace App\Http\Controllers;

use App\Models\DentalChart;
use App\Models\Patient;
use Illuminate\Http\Request;

class DentalChartController extends Controller
{
    /**
     * =========================================================================
     * LIST ALL DENTAL CHART RECORDS
     * =========================================================================
     * 
     * Display all dental chart records with filtering options.
     * Supports filtering by:
     * - Patient ID
     * - Tooth number
     * - Dental condition
     * - Date range (chart_date)
     * 
     * Provides active patient list for filter dropdowns.
     * 
     * @param Request $request HTTP request with filter parameters
     * @return \Illuminate\View\View Dental charts index page
     */
    public function index(Request $request)
    {
        // Build base query with relationships
        $query = DentalChart::with(['patient', 'updater']);

        // -------------------------------
        // APPLY FILTERS
        // -------------------------------
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('tooth_number', 'like', '%' . $search . '%')
                    ->orWhere('condition', 'like', '%' . $search . '%')
                    ->orWhere('procedure_done', 'like', '%' . $search . '%')
                    ->orWhereHas('patient', function ($patientQ) use ($search) {
                        $patientQ->where('full_name', 'like', '%' . $search . '%')
                            ->orWhere('patient_code', 'like', '%' . $search . '%');
                    });
            });
        }
        if ($request->filled('tooth_number')) {
            $query->where('tooth_number', $request->tooth_number);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        if ($request->filled('from_date')) {
            $query->where('chart_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('chart_date', '<=', $request->to_date);
        }

        // Execute query with pagination
        $charts = $query->orderBy('chart_date', 'desc')->paginate(20);
        
        // Get active patients for filter dropdown
        $patients = Patient::active()->get();

        return view('backend.dental-charts.index', compact('charts', 'patients'));
    }

    /**
     * =========================================================================
     * SHOW CREATE FORM
     * =========================================================================
     * 
     * Display form for creating a new dental chart record.
     * Provides dropdown of active patients for selection.
     * 
     * @return \Illuminate\View\View Dental chart creation form
     */
    public function create()
    {
        $patients = Patient::active()->get();
        return view('backend.dental-charts.create', compact('patients'));
    }

    /**
     * =========================================================================
     * STORE NEW DENTAL CHART RECORD
     * =========================================================================
     * 
     * Validate and store a new dental chart record.
     * Validates tooth number format and dental condition.
     * Automatically sets updated_by to current user.
     * 
     * @param Request $request HTTP request with dental chart data
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'chart_date'     => 'required|date',
            'tooth_number'   => 'required|string|max:5',
            'surface'        => 'nullable|string|max:20',
            'condition'      => 'required|string|max:100',
            'procedure_done' => 'nullable|string|max:100',
            'next_checkup'   => 'nullable|date',
            'remarks'        => 'nullable|string',
        ]);

        // Add user tracking information
        $validated['updated_by'] = auth()->id();

        DentalChart::create($validated);

        return redirect()
            ->route('backend.dental-charts.index')
            ->with('success', 'Dental chart record created successfully.');
    }

    /**
     * =========================================================================
     * VIEW SINGLE DENTAL CHART RECORD
     * =========================================================================
     * 
     * Display detailed view of a specific dental chart record.
     * Loads patient and updater information.
     * 
     * @param DentalChart $dentalChart Dental chart model instance
     * @return \Illuminate\View\View Dental chart details page
     */
    public function show(DentalChart $dentalChart)
    {
        $dentalChart->load(['patient', 'updater']);
        return view('backend.dental-charts.show', compact('dentalChart'));
    }

    /**
     * =========================================================================
     * SHOW EDIT FORM
     * =========================================================================
     * 
     * Display form for editing an existing dental chart record.
     * Pre-fills form with current record data.
     * 
     * @param DentalChart $dentalChart Dental chart model instance
     * @return \Illuminate\View\View Dental chart edit form
     */
    public function edit(DentalChart $dentalChart)
    {
        $patients = Patient::active()->get();
        return view('backend.dental-charts.edit', compact('dentalChart', 'patients'));
    }

    /**
     * =========================================================================
     * UPDATE EXISTING DENTAL CHART RECORD
     * =========================================================================
     * 
     * Validate and update an existing dental chart record.
     * Updates updated_by field to current user.
     * 
     * @param Request $request HTTP request with updated dental chart data
     * @param DentalChart $dentalChart Dental chart model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function update(Request $request, DentalChart $dentalChart)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'chart_date'     => 'required|date',
            'tooth_number'   => 'required|string|max:5',
            'surface'        => 'nullable|string|max:20',
            'condition'      => 'required|string|max:100',
            'procedure_done' => 'nullable|string|max:100',
            'next_checkup'   => 'nullable|date',
            'remarks'        => 'nullable|string',
        ]);

        // Update user tracking information
        $validated['updated_by'] = auth()->id();

        $dentalChart->update($validated);

        return redirect()
            ->route('backend.dental-charts.index')
            ->with('success', 'Dental chart record updated successfully.');
    }

    /**
     * =========================================================================
     * DELETE DENTAL CHART RECORD
     * =========================================================================
     * 
     * Soft delete a dental chart record.
     * Note: Uses soft deletes if enabled in model.
     * 
     * @param DentalChart $dentalChart Dental chart model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function destroy(DentalChart $dentalChart)
    {
        $dentalChart->delete();

        return redirect()
            ->route('backend.dental-charts.index')
            ->with('success', 'Dental chart record deleted successfully.');
    }

    /**
     * =========================================================================
     * VIEW ALL CHARTS FOR A PATIENT
     * =========================================================================
     * 
     * Display all dental chart records for a specific patient.
     * Shows comprehensive dental history organized by tooth.
     * 
     * @param int $patientId Patient ID
     * @return \Illuminate\View\View Patient dental chart history page
     */
    public function patientChart($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $charts = DentalChart::where('patient_id', $patientId)
            ->orderBy('chart_date', 'desc')
            ->get();

        return view('backend.dental-charts.patient-chart', compact('patient', 'charts'));
    }

    /**
     * =========================================================================
     * QUICK ADD RECORD (AJAX)
     * =========================================================================
     * 
     * AJAX endpoint for quickly adding dental chart records.
     * Automatically sets chart_date to current datetime.
     * Used for rapid data entry during examinations.
     * 
     * @param Request $request HTTP request with minimal dental chart data
     * @return \Illuminate\Http\JsonResponse JSON response with created record
     */
    public function quickAdd(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'tooth_number'   => 'required|string|max:5',
            'condition'      => 'required|string|max:100',
            'surface'        => 'nullable|string|max:20',
            'procedure_done' => 'nullable|string|max:100',
            'remarks'        => 'nullable|string',
        ]);

        // Set automatic fields
        $validated['chart_date'] = now();
        $validated['updated_by'] = auth()->id();

        $chart = DentalChart::create($validated);

        return response()->json([
            'success' => true,
            'chart'   => $chart,
        ]);
    }

    /**
     * =========================================================================
     * GET LATEST CHART DATA FOR EACH TOOTH (AJAX)
     * =========================================================================
     * 
     * AJAX endpoint to get the latest dental chart data for each tooth.
     * Groups records by tooth number and returns only the most recent entry.
     * Used for displaying current dental status in patient charts.
     * 
     * @param int $patientId Patient ID
     * @return \Illuminate\Http\JsonResponse JSON with latest data per tooth
     */
    public function getPatientChartData($patientId)
    {
        // Get all charts for patient, group by tooth, take latest per tooth
        $charts = DentalChart::where('patient_id', $patientId)
            ->orderBy('chart_date', 'desc')
            ->get()
            ->groupBy('tooth_number')
            ->map(fn($records) => $records->first()); // Latest record per tooth

        return response()->json($charts);
    }
}
