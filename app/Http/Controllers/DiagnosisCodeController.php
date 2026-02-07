<?php

namespace App\Http\Controllers;

use App\Models\DiagnosisCode;
use Illuminate\Http\Request;

class DiagnosisCodeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index / Listing
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = DiagnosisCode::query();

        // Search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $diagnosisCodes = $query
            ->orderBy('category')
            ->orderBy('code')
            ->paginate(12)
            ->withQueryString();

        $categories = DiagnosisCode::categories();

        return view('backend.diagnosis_codes.index', compact('diagnosisCodes', 'categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $categories = DiagnosisCode::categories();
        return view('backend.diagnosis_codes.create', compact('categories'));
    }

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

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */
    public function show(DiagnosisCode $diagnosisCode)
    {
        // Load related treatments (must exist or fail loudly)
        $diagnosisCode->load('treatments');

        return view('backend.diagnosis_codes.show', compact('diagnosisCode'));
    }

    /*
    |--------------------------------------------------------------------------
    | Edit / Update
    |--------------------------------------------------------------------------
    */
    public function edit(DiagnosisCode $diagnosisCode)
    {
        $categories = DiagnosisCode::categories();
        return view('backend.diagnosis_codes.edit', compact('diagnosisCode', 'categories'));
    }

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

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */
    public function destroy(DiagnosisCode $diagnosisCode)
    {
        // Do not allow deletion if already used
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

    /*
    |--------------------------------------------------------------------------
    | API: Autocomplete
    |--------------------------------------------------------------------------
    */
    public function autocomplete(Request $request)
    {
        $query = $request->get('query');

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

    /*
    |--------------------------------------------------------------------------
    | Quick Add (AJAX Modal)
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | Export CSV
    |--------------------------------------------------------------------------
    */
    public function export()
    {
        $diagnosisCodes = DiagnosisCode::orderBy('category')->orderBy('code')->get();

        return response()->stream(function () use ($diagnosisCodes) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Code', 'Description', 'Category', 'Status', 'Created At']);

            foreach ($diagnosisCodes as $code) {
                fputcsv($file, [
                    $code->code,
                    $code->description,
                    $code->category_name,
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
