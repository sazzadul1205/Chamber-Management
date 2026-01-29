<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Treatment;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    // =========================
    // LIST PRESCRIPTIONS
    // =========================
    public function index(Request $request)
    {
        $query = Prescription::with(['treatment.patient', 'creator', 'items.medicine']);

        if ($request->filled('search')) $query->search($request->search);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('treatment_id')) $query->where('treatment_id', $request->treatment_id);
        if ($request->filled('date')) $query->whereDate('prescription_date', $request->date);

        $prescriptions = $query->orderByDesc('created_at')->paginate(20);
        $treatments = Treatment::active()->get();

        return view('backend.prescriptions.index', compact('prescriptions', 'treatments'));
    }

    // =========================
    // CREATE PRESCRIPTION
    // =========================
    public function create(Request $request)
    {
        $treatment = $request->filled('treatment_id')
            ? Treatment::with('patient')->findOrFail($request->treatment_id)
            : null;

        $treatments = Treatment::active()->with('patient')->get();
        $medicines = Medicine::active()->get();

        return view('backend.prescriptions.create', compact('treatment', 'treatments', 'medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'prescription_date' => 'required|date',
            'validity_days' => 'required|integer|min:1|max:30',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.dosage' => 'required|string|max:50',
            'items.*.frequency' => 'required|string|max:50',
            'items.*.duration' => 'required|string|max:50',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $prescription = Prescription::create([
                'prescription_code' => Prescription::generatePrescriptionCode(),
                'treatment_id' => $request->treatment_id,
                'prescription_date' => $request->prescription_date,
                'validity_days' => $request->validity_days,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $item['medicine_id'],
                    'dosage' => $item['dosage'],
                    'frequency' => $item['frequency'],
                    'duration' => $item['duration'],
                    'route' => $item['route'] ?? 'oral',
                    'instructions' => $item['instructions'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('backend.prescriptions.index')->with('success', 'Prescription created successfully.');
    }

    // =========================
    // SHOW PRESCRIPTION
    // =========================
    public function show(Prescription $prescription)
    {
        $prescription->load(['treatment.patient', 'treatment.doctor.user', 'creator', 'items.medicine']);
        return view('backend.prescriptions.show', compact('prescription'));
    }

    // =========================
    // EDIT / UPDATE PRESCRIPTION
    // =========================
    public function edit(Prescription $prescription)
    {
        $prescription->load(['treatment', 'items.medicine']);
        $medicines = Medicine::active()->get();

        // Prepare existing items data for JavaScript
        $existingItems = $prescription->items->map(function ($item) {
            return [
                'medicine_id' => $item->medicine_id,
                'dosage' => $item->dosage,
                'frequency' => $item->frequency,
                'duration' => $item->duration,
                'route' => $item->route,
                'quantity' => $item->quantity,
                'instructions' => $item->instructions
            ];
        })->toArray();

        return view('backend.prescriptions.edit', compact('prescription', 'medicines', 'existingItems'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $request->validate([
            'prescription_date' => 'required|date',
            'validity_days' => 'required|integer|min:1|max:30',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,expired,cancelled,filled',
        ]);

        $prescription->update($request->only(['prescription_date', 'validity_days', 'notes', 'status']));

        return redirect()->route('backend.prescriptions.show', $prescription)
            ->with('success', 'Prescription updated successfully.');
    }

    // =========================
    // DELETE PRESCRIPTION
    // =========================
    public function destroy(Prescription $prescription)
    {
        if ($prescription->items()->where('status', 'dispensed')->exists()) {
            return back()->with('error', 'Cannot delete prescription with dispensed items.');
        }

        $prescription->delete();
        return redirect()->route('backend.prescriptions.index')->with('success', 'Prescription deleted successfully.');
    }

    // =========================
    // PRESCRIPTION ITEMS ACTIONS
    // =========================
    public function addItem(Request $request, Prescription $prescription)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'dosage' => 'required|string|max:50',
            'frequency' => 'required|string|max:50',
            'duration' => 'required|string|max:50',
            'quantity' => 'required|integer|min:1',
            'instructions' => 'nullable|string|max:255',
        ]);

        $prescription->items()->create([
            'medicine_id' => $request->medicine_id,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'route' => $request->route ?? 'oral',
            'instructions' => $request->instructions,
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Medicine added to prescription.');
    }

    public function removeItem(PrescriptionItem $prescriptionItem)
    {
        if ($prescriptionItem->status === 'dispensed') {
            return back()->with('error', 'Cannot remove dispensed item.');
        }

        $prescriptionItem->delete();
        return back()->with('success', 'Medicine removed from prescription.');
    }

    public function dispenseItem(PrescriptionItem $prescriptionItem)
    {
        if ($prescriptionItem->dispense()) {
            return back()->with('success', 'Medicine dispensed.');
        }

        return back()->with('error', 'Cannot dispense this item.');
    }

    public function cancelItem(PrescriptionItem $prescriptionItem)
    {
        if ($prescriptionItem->cancel()) {
            return back()->with('success', 'Medicine cancelled.');
        }

        return back()->with('error', 'Cannot cancel this item.');
    }

    public function dispenseAll(Prescription $prescription)
    {
        DB::transaction(function () use ($prescription) {
            $prescription->items()->where('status', 'pending')->update(['status' => 'dispensed']);
            $prescription->update(['status' => 'filled']);
        });

        return back()->with('success', 'All items dispensed.');
    }

    // =========================
    // PRESCRIPTION STATUS ACTIONS
    // =========================
    public function expire(Prescription $prescription)
    {
        if ($prescription->expire()) {
            return back()->with('success', 'Prescription marked as expired.');
        }

        return back()->with('error', 'Cannot expire this prescription.');
    }

    public function cancel(Prescription $prescription)
    {
        if ($prescription->cancel()) {
            return back()->with('success', 'Prescription cancelled.');
        }

        return back()->with('error', 'Cannot cancel this prescription.');
    }

    public function markAsFilled(Prescription $prescription)
    {
        if ($prescription->markAsFilled()) {
            return back()->with('success', 'Prescription marked as filled.');
        }

        return back()->with('error', 'Cannot mark this prescription as filled.');
    }

    // =========================
    // PRINT PRESCRIPTION
    // =========================
    public function print(Prescription $prescription)
    {
        $prescription->load(['treatment.patient', 'treatment.doctor.user', 'items.medicine']);
        return view('backend.prescriptions.print', compact('prescription'));
    }

    // =========================
    // TREATMENT PRESCRIPTIONS
    // =========================
    public function treatmentPrescriptions(Treatment $treatment)
    {
        $prescriptions = $treatment->prescriptions()->orderByDesc('created_at')->get();
        return view('backend.prescriptions.treatment-prescriptions', compact('treatment', 'prescriptions'));
    }

    // =========================
    // AJAX: SEARCH MEDICINES
    // =========================
    public function getMedicines(Request $request)
    {
        $search = $request->get('search', '');

        $medicines = Medicine::active()
            ->where(function ($q) use ($search) {
                $q->where('brand_name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%")
                    ->orWhere('medicine_code', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(fn($medicine) => [
                'id' => $medicine->id,
                'text' => "{$medicine->medicine_code} - {$medicine->brand_name} ({$medicine->generic_name})",
                'brand_name' => $medicine->brand_name,
                'generic_name' => $medicine->generic_name,
                'strength' => $medicine->strength,
                'dosage_form' => $medicine->dosage_form,
                'unit' => $medicine->unit,
            ]);

        return response()->json($medicines);
    }

    // =========================
    // QUICK CREATE PRESCRIPTION (AJAX)
    // =========================
    public function quickCreate(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'medicine_id' => 'required|exists:medicines,id',
            'dosage' => 'required|string|max:50',
            'frequency' => 'required|string|max:50',
            'duration' => 'required|string|max:50',
        ]);

        $prescription = DB::transaction(function () use ($request) {
            $prescription = Prescription::create([
                'prescription_code' => Prescription::generatePrescriptionCode(),
                'treatment_id' => $request->treatment_id,
                'prescription_date' => now(),
                'validity_days' => 7,
                'created_by' => auth()->id(),
            ]);

            $prescription->items()->create([
                'medicine_id' => $request->medicine_id,
                'dosage' => $request->dosage,
                'frequency' => $request->frequency,
                'duration' => $request->duration,
                'route' => 'oral',
                'instructions' => $request->instructions,
                'quantity' => $request->quantity ?? 1,
            ]);

            return $prescription;
        });

        return response()->json([
            'success' => true,
            'prescription' => [
                'id' => $prescription->id,
                'code' => $prescription->prescription_code,
            ]
        ]);
    }
}
