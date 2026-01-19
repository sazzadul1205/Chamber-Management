<?php

namespace App\Http\Controllers;

use App\Models\DentalChart;
use App\Models\Patient;
use Illuminate\Http\Request;

class DentalChartController extends Controller
{
    /**
     * Display dental charts for a specific patient or all charts.
     */
    public function index()
    {
        // Get patients who have at least one dental chart
        $patients = Patient::whereHas('dentalCharts')
            ->with(['dentalCharts' => function ($q) {
                $q->orderBy('created_at', 'desc'); // optional, latest first
            }])
            ->orderBy('full_name')
            ->paginate(20);

        return view('backend.dental-charts.index', compact('patients'));
    }



    /**
     * Show form to create dental chart records.
     */
    public function create(Request $request)
    {
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::findOrFail($request->patient_id);
        }

        $patients = Patient::orderBy('full_name')->get();

        return view('backend.dental-charts.create', compact('patients', 'patient'));
    }

    /**
     * Store new dental chart record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'charts' => 'required|array|min:1',
            'charts.*.tooth_number' => 'required|string|max:10',
            'charts.*.tooth_condition' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['charts'] as $chart) {
            DentalChart::updateOrCreate(
                [
                    'patient_id' => $validated['patient_id'],
                    'tooth_number' => $chart['tooth_number'],
                ],
                [
                    'tooth_condition' => $chart['tooth_condition'],
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('backend.dental-charts.index', ['patient' => $validated['patient_id']])
            ->with('success', 'Dental chart record saved successfully.');
    }


    /**
     * Show all dental charts for a specific patient.
     */
    public function show($id)
    {
        // Check if $id is a dental chart ID or patient ID
        $dentalChart = DentalChart::find($id);

        if ($dentalChart) {
            // If it's a dental chart, get its patient
            $patient = $dentalChart->patient;
        } else {
            // If it's not a dental chart, assume it's a patient ID
            $patient = Patient::findOrFail($id);
        }

        if (!$patient) {
            abort(404, 'Patient not found');
        }

        $charts = $patient->dentalCharts()->orderBy('tooth_number')->get();
        return view('backend.dental-charts.show', compact('patient', 'charts'));
    }

    /**
     * Show form to edit dental chart.
     */
    public function edit(DentalChart $dentalChart)
    {
        $patient = $dentalChart->patient;
        $charts = $patient->dentalCharts()->get();

        $toothConditions = [
            'Healthy',
            'Cavity',
            'Filled',
            'Crown',
            'Missing',
            'Implant',
            'Root Canal',
            'Decay',
            'Fractured',
            'Discolored',
            'Sensitive',
            'Other'
        ];

        return view('backend.dental-charts.edit', compact('patient', 'charts', 'toothConditions'));
    }


    /**
     * Update a dental chart.
     */
    public function update(Request $request, DentalChart $dentalChart)
    {
        $validated = $request->validate([
            'tooth_condition' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $dentalChart->update($validated);

        return redirect()->route('backend.dental-charts.index', ['patient' => $dentalChart->patient_id])
            ->with('success', 'Dental chart record updated successfully.');
    }

    /**
     * Delete all dental charts for the patient of the given chart.
     */
    public function destroy(DentalChart $dentalChart)
    {
        // Get patient ID
        $patientId = $dentalChart->patient_id;

        // Delete all charts for this patient
        DentalChart::where('patient_id', $patientId)->delete();

        return redirect()->route('backend.dental-charts.index')
            ->with('success', 'All dental charts for this patient have been deleted successfully.');
    }


    /**
     * Visualization for a patient.
     */
    public function visualization($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $charts = $patient->dentalCharts()->get()->keyBy('tooth_number');

        $legends = [
            'Healthy',
            'Cavity',
            'Filled',
            'Crown',
            'Missing',
            'Implant',
            'Root Canal',
            'Decay',
            'Fractured',
            'Discolored',
            'Sensitive',
            'Other'
        ];

        return view('backend.dental-charts.visualization', compact('patient', 'charts', 'legends'));
    }

    /**
     * Bulk update multiple teeth at once.
     */
    public function bulkUpdate(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);

        $validated = $request->validate([
            'charts' => 'required|array',
            'charts.*.tooth_number' => 'required|string|max:10',
            'charts.*.tooth_condition' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['charts'] as $chartData) {
            DentalChart::updateOrCreate(
                [
                    'patient_id' => $patientId,
                    'tooth_number' => $chartData['tooth_number'],
                ],
                [
                    'tooth_condition' => $chartData['tooth_condition'],
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('backend.dental-charts.index', $patientId)
            ->with('success', 'Dental chart updated successfully.');
    }


    /**
     * Show bulk edit form for a patient.
     */
    public function editPatientCharts($patientId)
    {
        $patient = Patient::with('dentalCharts')->findOrFail($patientId);
        $charts = $patient->dentalCharts;

        return view('backend.dental-charts.edit', compact('patient', 'charts'));
    }
}
