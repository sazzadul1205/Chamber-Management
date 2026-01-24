<?php

namespace App\Http\Controllers;

use App\Models\DentalChart;
use App\Models\Patient;
use Illuminate\Http\Request;

class DentalChartController extends Controller
{
    public function index(Request $request)
    {
        $query = DentalChart::with(['patient', 'updater']);

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
        $patients = Patient::active()->get();

        return view('dental-charts.index', compact('charts', 'patients'));
    }

    public function create()
    {
        $patients = Patient::active()->get();
        return view('dental-charts.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'chart_date' => 'required|date',
            'tooth_number' => 'required|string|max:5',
            'surface' => 'nullable|string|max:20',
            'condition' => 'required|string|max:100',
            'procedure_done' => 'nullable|string|max:100',
            'next_checkup' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        DentalChart::create([
            'patient_id' => $request->patient_id,
            'chart_date' => $request->chart_date,
            'tooth_number' => $request->tooth_number,
            'surface' => $request->surface,
            'condition' => $request->condition,
            'procedure_done' => $request->procedure_done,
            'next_checkup' => $request->next_checkup,
            'remarks' => $request->remarks,
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('dental-charts.index')
            ->with('success', 'Dental chart record created successfully.');
    }

    public function show(DentalChart $dentalChart)
    {
        $dentalChart->load(['patient', 'updater']);
        return view('dental-charts.show', compact('dentalChart'));
    }

    public function edit(DentalChart $dentalChart)
    {
        $patients = Patient::active()->get();
        return view('dental-charts.edit', compact('dentalChart', 'patients'));
    }

    public function update(Request $request, DentalChart $dentalChart)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'chart_date' => 'required|date',
            'tooth_number' => 'required|string|max:5',
            'surface' => 'nullable|string|max:20',
            'condition' => 'required|string|max:100',
            'procedure_done' => 'nullable|string|max:100',
            'next_checkup' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $dentalChart->update([
            'patient_id' => $request->patient_id,
            'chart_date' => $request->chart_date,
            'tooth_number' => $request->tooth_number,
            'surface' => $request->surface,
            'condition' => $request->condition,
            'procedure_done' => $request->procedure_done,
            'next_checkup' => $request->next_checkup,
            'remarks' => $request->remarks,
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('dental-charts.index')
            ->with('success', 'Dental chart record updated successfully.');
    }

    public function destroy(DentalChart $dentalChart)
    {
        $dentalChart->delete();

        return redirect()
            ->route('dental-charts.index')
            ->with('success', 'Dental chart record deleted successfully.');
    }

    public function patientChart($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $charts = DentalChart::where('patient_id', $patientId)
            ->orderBy('chart_date', 'desc')
            ->get();

        return view('dental-charts.patient-chart', compact('patient', 'charts'));
    }

    public function quickAdd(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tooth_number' => 'required|string|max:5',
            'condition' => 'required|string|max:100',
        ]);

        $chart = DentalChart::create([
            'patient_id' => $request->patient_id,
            'chart_date' => now(),
            'tooth_number' => $request->tooth_number,
            'condition' => $request->condition,
            'surface' => $request->surface,
            'procedure_done' => $request->procedure_done,
            'remarks' => $request->remarks,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'chart' => $chart,
        ]);
    }

    public function getPatientChartData($patientId)
    {
        $charts = DentalChart::where('patient_id', $patientId)
            ->orderBy('chart_date', 'desc')
            ->get()
            ->groupBy('tooth_number')
            ->map(function ($toothRecords) {
                return $toothRecords->first(); // Get latest record for each tooth
            });

        return response()->json($charts);
    }
}
