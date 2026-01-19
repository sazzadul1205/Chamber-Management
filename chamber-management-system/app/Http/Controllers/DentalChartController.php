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

        $toothNumbers = [
            '18',
            '17',
            '16',
            '15',
            '14',
            '13',
            '12',
            '11',
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
            '28',
            '48',
            '47',
            '46',
            '45',
            '44',
            '43',
            '42',
            '41',
            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
            '37',
            '38'
        ];

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

        return view('backend.dental-charts.create', compact('patients', 'patient', 'toothNumbers', 'toothConditions'));
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
     * Accepts either Patient ID or Patient model.
     */
    public function show(Patient $patient)
    {
        // Load all charts for this patient
        $charts = $patient->dentalCharts()->get()->keyBy('tooth_number');

        $allTeeth = [
            '18',
            '17',
            '16',
            '15',
            '14',
            '13',
            '12',
            '11',
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
            '28',
            '48',
            '47',
            '46',
            '45',
            '44',
            '43',
            '42',
            '41',
            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
            '37',
            '38'
        ];

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

        return view('backend.dental-charts.show', compact('patient', 'charts', 'allTeeth', 'toothConditions'));
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
     * Delete a dental chart.
     */
    public function destroy(DentalChart $dentalChart)
    {
        $patientId = $dentalChart->patient_id;
        $dentalChart->delete();

        return redirect()->route('backend.dental-charts.index', ['patient' => $patientId])
            ->with('success', 'Dental chart record deleted successfully.');
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
