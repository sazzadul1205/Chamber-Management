<?php

namespace App\Http\Controllers;

use App\Models\TreatmentProcedure;
use App\Models\Treatment;
use App\Models\ProcedureCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreatmentProcedureController extends Controller
{
    // =========================
    // LIST PROCEDURES
    // =========================
    public function index(Request $request)
    {
        $query = TreatmentProcedure::with(['treatment.patient', 'completer']);

        // Filters
        if ($request->filled('treatment_id')) {
            $query->where('treatment_id', $request->treatment_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tooth_number')) {
            $query->where('tooth_number', $request->tooth_number);
        }

        $procedures = $query->orderBy('created_at', 'desc')->paginate(20);
        $treatments = Treatment::active()->get();

        return view('backend.treatment-procedures.index', compact('procedures', 'treatments'));
    }

    // =========================
    // CREATE PROCEDURE FORM
    // =========================
    public function create($treatmentId = null)
    {
        $treatment = $treatmentId ? Treatment::with('patient')->findOrFail($treatmentId) : null;
        $commonProcedures = TreatmentProcedure::getCommonProcedures();
        $treatments = Treatment::active()->with('patient')->get();
        $proceduresCatalog = ProcedureCatalog::active()->get();

        return view(
            'backend.treatment-procedures.create',
            compact('treatment', 'treatments', 'commonProcedures', 'proceduresCatalog')
        );
    }

    // =========================
    // STORE NEW PROCEDURE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'treatment_id'    => 'required|exists:treatments,id',
            'procedure_code'  => 'required|string|max:20',
            'procedure_name'  => 'required|string|max:100',
            'tooth_number'    => 'nullable|string|max:5',
            'surface'         => 'nullable|string|max:20',
            'cost'            => 'required|numeric|min:0',
            'duration'        => 'required|integer|min:1|max:480',
            'status'          => 'required|in:planned,in_progress,completed,cancelled',
            'notes'           => 'nullable|string',
        ]);

        $procedure = TreatmentProcedure::create($request->all());

        // Update treatment cost after adding procedure
        $procedure->treatment->updateActualCost();

        return redirect()
            ->route('backend.treatment-procedures.show', $procedure)
            ->with('success', 'Procedure added successfully.');
    }

    // =========================
    // SHOW PROCEDURE
    // =========================
    public function show(TreatmentProcedure $treatmentProcedure)
    {
        $treatmentProcedure->load(['treatment.patient', 'completer']);
        return view('backend.treatment-procedures.show', compact('treatmentProcedure'));
    }

    // =========================
    // EDIT PROCEDURE FORM
    // =========================
    public function edit(TreatmentProcedure $treatmentProcedure)
    {
        $treatmentProcedure->load('treatment');
        $commonProcedures = TreatmentProcedure::getCommonProcedures();
        $proceduresCatalog = ProcedureCatalog::active()->get();

        return view(
            'backend.treatment-procedures.edit',
            compact('treatmentProcedure', 'commonProcedures', 'proceduresCatalog')
        );
    }

    // =========================
    // UPDATE PROCEDURE
    // =========================
    public function update(Request $request, TreatmentProcedure $treatmentProcedure)
    {
        $request->validate([
            'procedure_code'  => 'required|string|max:20',
            'procedure_name'  => 'required|string|max:100',
            'tooth_number'    => 'nullable|string|max:5',
            'surface'         => 'nullable|string|max:20',
            'cost'            => 'required|numeric|min:0',
            'duration'        => 'required|integer|min:1|max:480',
            'status'          => 'required|in:planned,in_progress,completed,cancelled',
            'notes'           => 'nullable|string',
        ]);

        $oldCost = $treatmentProcedure->cost;

        $treatmentProcedure->update($request->all());

        // Update treatment cost if needed
        if ($oldCost != $request->cost || in_array($request->status, ['completed', 'cancelled'])) {
            $treatmentProcedure->treatment->updateActualCost();
        }

        return redirect()
            ->route('backend.treatment-procedures.show', $treatmentProcedure)
            ->with('success', 'Procedure updated successfully.');
    }

    // =========================
    // DELETE PROCEDURE
    // =========================
    public function destroy(TreatmentProcedure $treatmentProcedure)
    {
        $treatment = $treatmentProcedure->treatment;
        $treatmentProcedure->delete();

        // Update treatment cost after deletion
        $treatment->updateActualCost();

        return redirect()
            ->route('treatments.show', $treatment->id)
            ->with('success', 'Procedure deleted successfully.');
    }

    // =========================
    // STATUS ACTIONS
    // =========================
    public function start(TreatmentProcedure $treatmentProcedure)
    {
        $treatmentProcedure->start();

        return back()->with('success', 'Procedure started successfully.');
    }

    public function complete(TreatmentProcedure $treatmentProcedure)
    {
        $treatmentProcedure->complete();

        return back()->with('success', 'Procedure completed successfully.');
    }

    public function cancel(TreatmentProcedure $treatmentProcedure)
    {
        $treatmentProcedure->cancel();

        return back()->with('success', 'Procedure cancelled successfully.');
    }

    // =========================
    // BULK ADD PROCEDURES
    // =========================
    public function bulkAdd(Request $request, Treatment $treatment)
    {
        $request->validate([
            'procedures' => 'required|array|min:1',
            'procedures.*.code'     => 'required|string|max:20',
            'procedures.*.name'     => 'required|string|max:100',
            'procedures.*.cost'     => 'required|numeric|min:0',
            'procedures.*.duration' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->procedures as $proc) {
                TreatmentProcedure::create([
                    'treatment_id'    => $treatment->id,
                    'procedure_code'  => $proc['code'],
                    'procedure_name'  => $proc['name'],
                    'cost'            => $proc['cost'],
                    'duration'        => $proc['duration'],
                    'tooth_number'    => $proc['tooth_number'] ?? null,
                    'surface'         => $proc['surface'] ?? null,
                    'status'          => 'planned',
                ]);
            }
            DB::commit();

            // Update treatment cost after bulk add
            $treatment->updateActualCost();

            return redirect()
                ->route('treatments.show', $treatment->id)
                ->with('success', 'Procedures added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add procedures: ' . $e->getMessage());
        }
    }

    // =========================
    // LIST PROCEDURES FOR A SPECIFIC TREATMENT
    // =========================
    public function treatmentProcedures(Treatment $treatment)
    {
        $procedures = TreatmentProcedure::where('treatment_id', $treatment->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.treatment-procedures.treatment-procedures', compact('treatment', 'procedures'));
    }

    // =========================
    // SEARCH CATALOG PROCEDURES (AJAX)
    // =========================
    public function getCatalogProcedures(Request $request)
    {
        $search = $request->get('search', '');

        $procedures = ProcedureCatalog::where('status', 'active')
            ->where(function ($query) use ($search) {
                $query->where('procedure_code', 'like', "%{$search}%")
                    ->orWhere('procedure_name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($proc) {
                return [
                    'code'     => $proc->procedure_code,
                    'name'     => $proc->procedure_name,
                    'cost'     => $proc->standard_cost,
                    'duration' => $proc->standard_duration,
                    'category' => $proc->category,
                ];
            });

        return response()->json($procedures);
    }

    // TreatmentProcedureController.php
    public function catalogSearch()
    {
        $search = request('search', '');
        if (strlen($search) < 2) return response()->json([]);

        $procedures = ProcedureCatalog::searchCatalog($search);
        return response()->json($procedures);
    }
}
