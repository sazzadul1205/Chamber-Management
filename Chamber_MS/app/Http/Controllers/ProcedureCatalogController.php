<?php

namespace App\Http\Controllers;

use App\Models\ProcedureCatalog;
use Illuminate\Http\Request;

class ProcedureCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = ProcedureCatalog::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('procedure_code', 'like', "%{$search}%")
                    ->orWhere('procedure_name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->category && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $procedures = $query
            ->orderBy('category')
            ->orderBy('procedure_name')
            ->paginate(9)
            ->withQueryString();

        $categories = ProcedureCatalog::categories();

        return view('backend.procedure_catalog.index', compact('procedures', 'categories'));
    }


    public function create()
    {
        $categories = ProcedureCatalog::categories();
        return view('backend.procedure_catalog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'procedure_code' => 'required|string|max:20|unique:procedure_catalog',
            'procedure_name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'standard_duration' => 'required|integer|min:1',
            'standard_cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        ProcedureCatalog::create($request->all());

        return redirect()->route('backend.procedure-catalog.index')
            ->with('success', 'Dental procedure added successfully.');
    }

    public function show(ProcedureCatalog $procedureCatalog)
    {
        return view('backend.procedure_catalog.show', compact('procedureCatalog'));
    }

    public function edit(ProcedureCatalog $procedureCatalog)
    {
        $categories = ProcedureCatalog::categories();
        return view('backend.procedure_catalog.edit', compact('procedureCatalog', 'categories'));
    }

    public function update(Request $request, ProcedureCatalog $procedureCatalog)
    {
        $request->validate([
            'procedure_code' => 'required|string|max:20|unique:procedure_catalog,procedure_code,' . $procedureCatalog->id,
            'procedure_name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'standard_duration' => 'required|integer|min:1',
            'standard_cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $procedureCatalog->update($request->all());

        return redirect()->route('backend.procedure-catalog.index')
            ->with('success', 'Procedure updated successfully.');
    }

    public function destroy(ProcedureCatalog $procedureCatalog)
    {
        // Only check if the class exists
        if (
            class_exists(\App\Models\TreatmentProcedure::class) &&
            $procedureCatalog->treatmentProcedures()->exists()
        ) {
            return redirect()->route('backend.procedure-catalog.index')
                ->with('error', 'Cannot delete procedure. It is being used in treatments.');
        }

        $procedureCatalog->delete();
        return redirect()->route('backend.procedure-catalog.index')
            ->with('success', 'Procedure deleted successfully.');
    }


    // API endpoint for autocomplete
    public function autocomplete(Request $request)
    {
        $query = $request->get('query');

        $procedures = ProcedureCatalog::active()
            ->where(function ($q) use ($query) {
                $q->where('procedure_code', 'like', "%{$query}%")
                    ->orWhere('procedure_name', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($procedure) {
                return [
                    'id' => $procedure->id,
                    'value' => $procedure->procedure_code . ' - ' . $procedure->procedure_name,
                    'code' => $procedure->procedure_code,
                    'name' => $procedure->procedure_name,
                    'duration' => $procedure->standard_duration,
                    'cost' => $procedure->standard_cost,
                    'category' => $procedure->category
                ];
            });

        return response()->json($procedures);
    }

    // Bulk import from CSV
    public function import()
    {
        return view('backend.procedure_catalog.import');
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        // Process CSV import (simplified - in real app use Laravel Excel)
        $path = $request->file('csv_file')->getRealPath();
        $data = array_map('str_getcsv', file($path));

        $header = array_shift($data);
        $imported = 0;
        $errors = [];

        foreach ($data as $row) {
            if (count($row) != count($header)) continue;

            $rowData = array_combine($header, $row);

            try {
                ProcedureCatalog::updateOrCreate(
                    ['procedure_code' => $rowData['procedure_code']],
                    [
                        'procedure_name' => $rowData['procedure_name'],
                        'category' => $rowData['category'],
                        'standard_duration' => (int) $rowData['standard_duration'],
                        'standard_cost' => (float) $rowData['standard_cost'],
                        'description' => $rowData['description'] ?? null,
                        'status' => $rowData['status'] ?? 'active'
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row error: " . $e->getMessage();
            }
        }

        return redirect()->route('backend.procedure-catalog.index')
            ->with('success', "Imported {$imported} procedures successfully.")
            ->with('errors', $errors);
    }
}
