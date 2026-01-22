<?php

namespace App\Http\Controllers;

use App\Models\DiagnosisCode;
use Illuminate\Http\Request;

class DiagnosisCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = DiagnosisCode::query();

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        // Category filter
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $diagnosisCodes = $query->orderBy('category')->orderBy('code')->paginate(9);
        $categories = DiagnosisCode::categories();

        return view('backend.diagnosis_codes.index', compact('diagnosisCodes', 'categories'));
    }


    public function create()
    {
        $categories = DiagnosisCode::categories();
        return view('backend.diagnosis_codes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:diagnosis_codes',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'status' => 'required|in:active,inactive'
        ]);

        DiagnosisCode::create($request->all());

        return redirect()->route('backend.diagnosis-codes.index')
            ->with('success', 'Diagnosis code added successfully.');
    }

    public function show(DiagnosisCode $diagnosisCode)
    {
        // Check if Treatment class exists and relationship works
        if (class_exists(\App\Models\Treatment::class)) {
            $diagnosisCode->load('treatments');
        } else {
            // If not, add a dummy demo collection so your view won't break
            $diagnosisCode->treatments = collect([
                (object)[
                    'id' => 0,
                    'patient_name' => 'Demo Patient',
                    'procedure' => 'Demo Procedure',
                    'date' => now()->format('Y-m-d'),
                ]
            ]);
        }

        return view('backend.diagnosis_codes.show', compact('diagnosisCode'));
    }


    public function edit(DiagnosisCode $diagnosisCode)
    {
        $categories = DiagnosisCode::categories();
        return view('backend.diagnosis_codes.edit', compact('diagnosisCode', 'categories'));
    }

    public function update(Request $request, DiagnosisCode $diagnosisCode)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:diagnosis_codes,code,' . $diagnosisCode->id,
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'status' => 'required|in:active,inactive'
        ]);

        $diagnosisCode->update($request->all());

        return redirect()->route('backend.diagnosis-codes.index')
            ->with('success', 'Diagnosis code updated successfully.');
    }

    public function destroy(DiagnosisCode $diagnosisCode)
    {
        // Only check if the class exists
        if (class_exists(\App\Models\Treatment::class) && $diagnosisCode->treatments()->exists()) {
            return redirect()->route('backend.diagnosis-codes.index')
                ->with('error', 'Cannot delete diagnosis code. It is being used in treatments.');
        }

        $diagnosisCode->delete();

        return redirect()->route('backend.diagnosis-codes.index')
            ->with('success', 'Diagnosis code deleted successfully.');
    }


    // API endpoint for autocomplete
    public function autocomplete(Request $request)
    {
        $query = $request->get('query');

        $codes = DiagnosisCode::active()
            ->where(function ($q) use ($query) {
                $q->where('code', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($code) {
                return [
                    'id' => $code->id,
                    'value' => $code->code . ' - ' . $code->description,
                    'code' => $code->code,
                    'description' => $code->description,
                    'category' => $code->category
                ];
            });

        return response()->json($codes);
    }

    // Quick add modal endpoint
    public function quickAdd(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:diagnosis_codes',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:50'
        ]);

        $diagnosisCode = DiagnosisCode::create([
            'code' => $request->code,
            'description' => $request->description,
            'category' => $request->category,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'id' => $diagnosisCode->id,
            'code' => $diagnosisCode->code,
            'description' => $diagnosisCode->description
        ]);
    }

    // Export to CSV
    public function export(Request $request)
    {
        $diagnosisCodes = DiagnosisCode::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="diagnosis_codes_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($diagnosisCodes) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Code', 'Description', 'Category', 'Status', 'Created At']);

            // Data
            foreach ($diagnosisCodes as $code) {
                fputcsv($file, [
                    $code->code,
                    $code->description,
                    $code->category_name,
                    $code->status,
                    $code->created_at->format('Y-m-d')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
