<?php

namespace App\Http\Controllers;

use App\Models\DiagnosisCode;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DiagnosisCodeController extends Controller
{
    /**
     * =========================================================================
     * INDEX / LIST DIAGNOSIS CODES
     * =========================================================================
     * 
     * Display all diagnosis codes with filtering and pagination.
     * Supports filtering by:
     * - Search term (code or description)
     * - Category
     * - Status (active/inactive)
     * 
     * Provides category list for filter dropdowns.
     * 
     * @param Request $request HTTP request with filter parameters
     * @return \Illuminate\View\View Diagnosis codes index page
     */
    public function index(Request $request)
    {
        // Build base query
        $query = DiagnosisCode::query();

        // -------------------------------
        // APPLY FILTERS
        // -------------------------------
        // Search filter (assuming search scope is defined in model)
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter (exclude 'all' option)
        if ($request->filled('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // Status filter (exclude 'all' option)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Execute query with pagination and preserve query string
        $diagnosisCodes = $query
            ->orderBy('category')
            ->orderBy('code')
            ->paginate(12)
            ->withQueryString();

        // Get categories for filter dropdown
        $categories = DiagnosisCode::categories();

        return view('backend.diagnosis_codes.index', compact('diagnosisCodes', 'categories'));
    }

    /**
     * =========================================================================
     * CREATE DIAGNOSIS CODE FORM
     * =========================================================================
     * 
     * Display form for creating a new diagnosis code.
     * Provides category dropdown for selection.
     * 
     * @return \Illuminate\View\View Diagnosis code creation form
     */
    public function create()
    {
        $categories = DiagnosisCode::categories();
        return view('backend.diagnosis_codes.create', compact('categories'));
    }

    /**
     * =========================================================================
     * STORE NEW DIAGNOSIS CODE
     * =========================================================================
     * 
     * Validate and store a new diagnosis code.
     * Ensures code uniqueness and validates category selection.
     * 
     * @param Request $request HTTP request with diagnosis code data
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:20|unique:diagnosis_codes,code',
            'description' => 'required|string|max:255',
            'category'    => 'required|string|max:50',
            'status'      => 'required|in:active,inactive',
        ]);

        DiagnosisCode::create($validated);

        return redirect()
            ->route('backend.diagnosis-codes.index')
            ->with('success', 'Diagnosis code added successfully.');
    }

    /**
     * =========================================================================
     * SHOW DIAGNOSIS CODE DETAILS
     * =========================================================================
     * 
     * Display detailed view of a specific diagnosis code.
     * Loads related treatments for reference.
     * 
     * @param DiagnosisCode $diagnosisCode Diagnosis code model instance
     * @return \Illuminate\View\View Diagnosis code details page
     */
    public function show(DiagnosisCode $diagnosisCode)
    {
        // Load related treatments to show usage
        $diagnosisCode->load('treatments');

        return view('backend.diagnosis_codes.show', compact('diagnosisCode'));
    }

    /**
     * =========================================================================
     * EDIT DIAGNOSIS CODE FORM
     * =========================================================================
     * 
     * Display form for editing an existing diagnosis code.
     * Pre-fills form with current code data.
     * 
     * @param DiagnosisCode $diagnosisCode Diagnosis code model instance
     * @return \Illuminate\View\View Diagnosis code edit form
     */
    public function edit(DiagnosisCode $diagnosisCode)
    {
        $categories = DiagnosisCode::categories();
        return view('backend.diagnosis_codes.edit', compact('diagnosisCode', 'categories'));
    }

    /**
     * =========================================================================
     * UPDATE DIAGNOSIS CODE
     * =========================================================================
     * 
     * Validate and update an existing diagnosis code.
     * Ensures code uniqueness (excluding current record).
     * 
     * @param Request $request HTTP request with updated diagnosis code data
     * @param DiagnosisCode $diagnosisCode Diagnosis code model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success message
     */
    public function update(Request $request, DiagnosisCode $diagnosisCode)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:20|unique:diagnosis_codes,code,' . $diagnosisCode->id,
            'description' => 'required|string|max:255',
            'category'    => 'required|string|max:50',
            'status'      => 'required|in:active,inactive',
        ]);

        $diagnosisCode->update($validated);

        return redirect()
            ->route('backend.diagnosis-codes.index')
            ->with('success', 'Diagnosis code updated successfully.');
    }

    /**
     * =========================================================================
     * DELETE DIAGNOSIS CODE
     * =========================================================================
     * 
     * Delete a diagnosis code record.
     * Prevents deletion if code is used in treatments.
     * 
     * @param DiagnosisCode $diagnosisCode Diagnosis code model instance
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function destroy(DiagnosisCode $diagnosisCode)
    {
        // Check if diagnosis code is used in any treatments
        if ($diagnosisCode->treatments()->exists()) {
            return redirect()
                ->route('backend.diagnosis-codes.index')
                ->with('error', 'Cannot delete diagnosis code. It is used in treatments.');
        }

        $diagnosisCode->delete();

        return redirect()
            ->route('backend.diagnosis-codes.index')
            ->with('success', 'Diagnosis code deleted successfully.');
    }

    /**
     * =========================================================================
     * API: AUTOCOMPLETE SEARCH
     * =========================================================================
     * 
     * AJAX endpoint for diagnosis code autocomplete search.
     * Searches by code or description.
     * Returns formatted results for dropdown selection.
     * 
     * @param Request $request HTTP request with search query
     * @return \Illuminate\Http\JsonResponse JSON array of matching diagnosis codes
     */
    public function autocomplete(Request $request)
    {
        $query = $request->get('query');

        // Search active diagnosis codes
        $codes = DiagnosisCode::active()
            ->where(
                fn($q) =>
                $q->where('code', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
            )
            ->limit(10)
            ->get()
            ->map(fn($code) => [
                'id'          => $code->id,
                'value'       => "{$code->code} - {$code->description}",
                'code'        => $code->code,
                'description' => $code->description,
                'category'    => $code->category,
            ]);

        return response()->json($codes);
    }

    /**
     * =========================================================================
     * QUICK ADD DIAGNOSIS CODE (AJAX MODAL)
     * =========================================================================
     * 
     * AJAX endpoint for quickly adding diagnosis codes from modal forms.
     * Automatically sets status to active.
     * Used for rapid data entry in treatment forms.
     * 
     * @param Request $request HTTP request with minimal diagnosis code data
     * @return \Illuminate\Http\JsonResponse JSON response with created record
     */
    public function quickAdd(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:20|unique:diagnosis_codes,code',
            'description' => 'required|string|max:255',
            'category'    => 'required|string|max:50',
        ]);

        $diagnosisCode = DiagnosisCode::create([
            ...$validated,
            'status' => 'active',
        ]);

        return response()->json([
            'success'     => true,
            'id'          => $diagnosisCode->id,
            'code'        => $diagnosisCode->code,
            'description' => $diagnosisCode->description,
        ]);
    }

    /**
     * =========================================================================
     * EXPORT DIAGNOSIS CODES TO CSV
     * =========================================================================
     * 
     * Export all diagnosis codes to CSV file.
     * Includes all fields with formatted dates.
     * Provides downloadable file with timestamp in filename.
     * 
     * @return \Symfony\Component\HttpFoundation\StreamedResponse Streamed file download
     */
    public function export(): StreamedResponse
    {
        $diagnosisCodes = DiagnosisCode::orderBy('category')
            ->orderBy('code')
            ->get();

        return response()->stream(function () use ($diagnosisCodes) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, ['Code', 'Description', 'Category', 'Status', 'Created At']);

            foreach ($diagnosisCodes as $code) {
                fputcsv($file, [
                    $code->code,
                    $code->description,
                    $code->category_name, // accessor
                    $code->status,
                    $code->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=diagnosis_codes_' . now()->toDateString() . '.csv',
        ]);
    }
}
