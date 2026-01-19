<?php

namespace App\Http\Controllers;

use App\Models\DentalChart;
use Illuminate\Http\Request;

class SingleToothChartController extends Controller
{
  /**
   * Show edit form for a single tooth
   */
  public function edit($id)
  {
    $chart = DentalChart::with('patient')->findOrFail($id);

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

    return view('backend.dental-charts.single-edit', compact('chart', 'toothConditions'));
  }

  /**
   * Update a single tooth
   */
  public function update(Request $request, $id)
  {
    $chart = DentalChart::findOrFail($id);

    $validated = $request->validate([
      'tooth_condition' => 'required|string|max:255',
      'remarks' => 'nullable|string|max:1000',
    ]);

    $chart->update($validated);

    return redirect()
      ->route('backend.dental-charts.show', $chart->patient_id)
      ->with('success', 'Tooth record updated successfully.');
  }

  /**
   * Delete a single tooth
   */
  public function destroy($id)
  {
    $chart = DentalChart::findOrFail($id);
    $patientId = $chart->patient_id;

    $chart->delete();

    return redirect()
      ->route('backend.dental-charts.show', $patientId)
      ->with('success', 'Tooth record deleted successfully.');
  }
}
