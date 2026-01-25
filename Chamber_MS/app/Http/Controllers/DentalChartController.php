<?php

namespace App\Http\Controllers;

use App\Models\DentalChart;
use App\Models\Patient;
use Illuminate\Http\Request;

class DentalChartController extends Controller
{
    // =========================
    // LIST ALL DENTAL CHART RECORDS
    // =========================
    public function index(Request $request)
    {
        $query = DentalChart::with(['patient', 'updater']);

        // Filters
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
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

        $charts = $query->orderBy('chart_date', 'desc')->paginate(20);
        $patients = Patient::active()->get(); // For filter dropdown

        return view('dental-charts.index', compact('charts', 'patients'));
    }

    // =========================
    // SHOW CREATE FORM
    // =========================
    public function create()
    {
        $patients = Patient::active()->get();
        return view('dental-charts.create', compact('patients'));
    }

    // =========================
    // STORE NEW RECORD
    // =========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'chart_date' => 'required|date',
            'tooth_number' => 'required|string|max:5',
            'surface' => 'nullable|string|max:20',
            'condition' => 'required|string|max:100',
            'procedure_done' => 'nullable|string|max:100',
            'next_checkup' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        DentalChart::create($validated);

        return redirect()
            ->route('dental-charts.index')
            ->with('success', 'Dental chart record created successfully.');
    }

    // =========================
    // VIEW SINGLE RECORD
    // =========================
    public function show(DentalChart $dentalChart)
    {
        $dentalChart->load(['patient', 'updater']);
        return view('dental-charts.show', compact('dentalChart'));
    }

    // =========================
    // SHOW EDIT FORM
    // =========================
    public function edit(DentalChart $dentalChart)
    {
        $patients = Patient::active()->get();
        return view('dental-charts.edit', compact('dentalChart', 'patients'));
    }

    // =========================
    // UPDATE EXISTING RECORD
    // =========================
    public function update(Request $request, DentalChart $dentalChart)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'chart_date' => 'required|date',
            'tooth_number' => 'required|string|max:5',
            'surface' => 'nullable|string|max:20',
            'condition' => 'required|string|max:100',
            'procedure_done' => 'nullable|string|max:100',
            'next_checkup' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $dentalChart->update($validated);

        return redirect()
            ->route('dental-charts.index')
            ->with('success', 'Dental chart record updated successfully.');
    }

    // =========================
    // DELETE RECORD
    // =========================
    public function destroy(DentalChart $dentalChart)
    {
        $dentalChart->delete();

        return redirect()
            ->route('dental-charts.index')
            ->with('success', 'Dental chart record deleted successfully.');
    }

    // =========================
    // VIEW ALL CHARTS FOR A PATIENT
    // =========================
    public function patientChart($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $charts = DentalChart::where('patient_id', $patientId)
            ->orderBy('chart_date', 'desc')
            ->get();

        return view('dental-charts.patient-chart', compact('patient', 'charts'));
    }

    // =========================
    // QUICK ADD RECORD (AJAX)
    // =========================
    public function quickAdd(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tooth_number' => 'required|string|max:5',
            'condition' => 'required|string|max:100',
            'surface' => 'nullable|string|max:20',
            'procedure_done' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $validated['chart_date'] = now();
        $validated['updated_by'] = auth()->id();

        $chart = DentalChart::create($validated);

        return response()->json([
            'success' => true,
            'chart' => $chart,
        ]);
    }

    // =========================
    // GET LATEST CHART DATA FOR EACH TOOTH (AJAX)
    // =========================
    public function getPatientChartData($patientId)
    {
        $charts = DentalChart::where('patient_id', $patientId)
            ->orderBy('chart_date', 'desc')
            ->get()
            ->groupBy('tooth_number')
            ->map(fn($records) => $records->first()); // Latest record per tooth

        return response()->json($charts);
    }
}
