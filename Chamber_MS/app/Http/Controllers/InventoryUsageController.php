<?php

namespace App\Http\Controllers;

use App\Models\InventoryUsage;
use App\Models\InventoryItem;
use App\Models\Treatment;
use App\Models\Prescription;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryUsageController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryUsage::with(['item', 'treatment', 'prescription', 'patient', 'usedBy']);

        // Apply filters
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('patient_id')) {
            $query->where('used_for_patient_id', $request->patient_id);
        }

        if ($request->filled('treatment_id')) {
            $query->where('treatment_id', $request->treatment_id);
        }

        if ($request->filled('prescription_id')) {
            $query->where('prescription_id', $request->prescription_id);
        }

        if ($request->filled('usage_type')) {
            $query->where('usage_type', $request->usage_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('usage_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('usage_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhereHas('item', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('item_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('patient', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                            ->orWhere('patient_code', 'like', "%{$search}%");
                    });
            });
        }

        $usages = $query->latest()->paginate(20);

        $items = InventoryItem::where('status', 'active')->orderBy('name')->get();
        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $treatments = Treatment::where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $usageTypes = [
            'treatment' => 'Treatment',
            'prescription' => 'Prescription',
            'wastage' => 'Wastage',
            'other' => 'Other'
        ];

        return view('inventory_usage.index', compact('usages', 'items', 'patients', 'treatments', 'usageTypes'));
    }

    public function create(Request $request)
    {
        $items = InventoryItem::where('status', 'active')
            ->whereHas('stock', function ($q) {
                $q->where('current_stock', '>', 0);
            })
            ->orderBy('name')
            ->get();

        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $treatments = Treatment::where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $prescriptions = Prescription::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        // Pre-select if coming from treatment or prescription
        $preSelected = [
            'treatment_id' => $request->treatment_id,
            'prescription_id' => $request->prescription_id,
            'patient_id' => $request->patient_id
        ];

        $usageTypes = [
            'treatment' => 'Treatment',
            'prescription' => 'Prescription',
            'wastage' => 'Wastage',
            'other' => 'Other'
        ];

        return view('inventory_usage.create', compact('items', 'patients', 'treatments', 'prescriptions', 'preSelected', 'usageTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'used_quantity' => 'required|integer|min:1',
            'usage_type' => 'required|in:treatment,prescription,wastage,other',
            'used_for_patient_id' => 'nullable|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'usage_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check stock availability
        $stock = \App\Models\InventoryStock::where('item_id', $request->item_id)->first();

        if (!$stock || $stock->current_stock < $request->used_quantity) {
            $itemName = InventoryItem::find($request->item_id)->name;
            $available = $stock ? $stock->current_stock : 0;

            return redirect()->back()
                ->withInput()
                ->with('error', "Insufficient stock! {$itemName} has only {$available} items available. Requested: {$request->used_quantity}");
        }

        $usage = InventoryUsage::create([
            'treatment_id' => $request->treatment_id,
            'prescription_id' => $request->prescription_id,
            'item_id' => $request->item_id,
            'used_quantity' => $request->used_quantity,
            'usage_type' => $request->usage_type,
            'used_by' => 1, // Default admin user
            'used_for_patient_id' => $request->used_for_patient_id,
            'usage_date' => $request->usage_date,
            'notes' => $request->notes
        ]);

        // Update inventory stock and create transaction
        $usage->updateInventory();

        return redirect()->route('inventory_usage.show', $usage->id)
            ->with('success', 'Inventory usage recorded successfully.');
    }

    public function show($id)
    {
        $usage = InventoryUsage::with(['item', 'treatment', 'prescription', 'patient', 'usedBy'])->findOrFail($id);

        // Get item's current stock
        $currentStock = \App\Models\InventoryStock::where('item_id', $usage->item_id)->first();

        // Get recent usages for this item
        $itemUsages = InventoryUsage::where('item_id', $usage->item_id)
            ->where('id', '!=', $id)
            ->latest()
            ->limit(10)
            ->get();

        return view('inventory_usage.show', compact('usage', 'currentStock', 'itemUsages'));
    }

    public function edit($id)
    {
        $usage = InventoryUsage::findOrFail($id);

        $items = InventoryItem::where('status', 'active')->orderBy('name')->get();
        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $treatments = Treatment::where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $prescriptions = Prescription::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $usageTypes = [
            'treatment' => 'Treatment',
            'prescription' => 'Prescription',
            'wastage' => 'Wastage',
            'other' => 'Other'
        ];

        return view('inventory_usage.edit', compact('usage', 'items', 'patients', 'treatments', 'prescriptions', 'usageTypes'));
    }

    public function update(Request $request, $id)
    {
        $usage = InventoryUsage::findOrFail($id);

        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'used_quantity' => 'required|integer|min:1',
            'usage_type' => 'required|in:treatment,prescription,wastage,other',
            'used_for_patient_id' => 'nullable|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'usage_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check stock availability if item changed or quantity increased
        if ($usage->item_id != $request->item_id || $request->used_quantity > $usage->used_quantity) {
            $stock = \App\Models\InventoryStock::where('item_id', $request->item_id)->first();
            $additionalNeeded = ($usage->item_id == $request->item_id)
                ? $request->used_quantity - $usage->used_quantity
                : $request->used_quantity;

            if (!$stock || $stock->current_stock < $additionalNeeded) {
                $itemName = InventoryItem::find($request->item_id)->name;
                $available = $stock ? $stock->current_stock : 0;

                return redirect()->back()
                    ->withInput()
                    ->with('error', "Insufficient stock! {$itemName} has only {$available} items available. Additional needed: {$additionalNeeded}");
            }
        }

        // Revert old inventory impact
        $usage->revertInventory();

        // Update usage record
        $usage->update([
            'treatment_id' => $request->treatment_id,
            'prescription_id' => $request->prescription_id,
            'item_id' => $request->item_id,
            'used_quantity' => $request->used_quantity,
            'usage_type' => $request->usage_type,
            'used_for_patient_id' => $request->used_for_patient_id,
            'usage_date' => $request->usage_date,
            'notes' => $request->notes
        ]);

        // Apply new inventory impact
        $usage->updateInventory();

        return redirect()->route('inventory_usage.show', $usage->id)
            ->with('success', 'Inventory usage updated successfully.');
    }

    public function destroy($id)
    {
        $usage = InventoryUsage::findOrFail($id);

        // Revert inventory impact before deleting
        $usage->revertInventory();

        $usage->delete();

        return redirect()->route('inventory_usage.index')
            ->with('success', 'Inventory usage record deleted successfully.');
    }

    public function treatmentUsage($treatmentId)
    {
        $treatment = Treatment::with('patient')->findOrFail($treatmentId);
        $usages = InventoryUsage::with(['item', 'usedBy'])
            ->where('treatment_id', $treatmentId)
            ->latest()
            ->get();

        $items = InventoryItem::where('status', 'active')
            ->whereHas('stock', function ($q) {
                $q->where('current_stock', '>', 0);
            })
            ->orderBy('name')
            ->get();

        $usageTypes = [
            'treatment' => 'Treatment',
            'wastage' => 'Wastage',
            'other' => 'Other'
        ];

        return view('inventory_usage.treatment', compact('treatment', 'usages', 'items', 'usageTypes'));
    }

    public function patientUsage($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $usages = InventoryUsage::with(['item', 'treatment', 'prescription', 'usedBy'])
            ->where('used_for_patient_id', $patientId)
            ->latest()
            ->paginate(20);

        return view('inventory_usage.patient', compact('patient', 'usages'));
    }

    public function report(Request $request)
    {
        $query = InventoryUsage::with(['item', 'patient'])
            ->select('item_id', DB::raw('SUM(used_quantity) as total_used'), DB::raw('COUNT(*) as usage_count'))
            ->groupBy('item_id');

        if ($request->filled('start_date')) {
            $query->whereDate('usage_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('usage_date', '<=', $request->end_date);
        }

        if ($request->filled('usage_type')) {
            $query->where('usage_type', $request->usage_type);
        }

        $report = $query->orderBy('total_used', 'desc')->paginate(20);

        $usageTypes = [
            'treatment' => 'Treatment',
            'prescription' => 'Prescription',
            'wastage' => 'Wastage',
            'other' => 'Other'
        ];

        return view('inventory_usage.report', compact('report', 'usageTypes'));
    }

    public function quickUse(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:1',
            'patient_id' => 'required|exists:patients,id',
            'notes' => 'required|string|max:255'
        ]);

        // Check stock availability
        $stock = \App\Models\InventoryStock::where('item_id', $request->item_id)->first();

        if (!$stock || $stock->current_stock < $request->quantity) {
            $itemName = InventoryItem::find($request->item_id)->name;
            $available = $stock ? $stock->current_stock : 0;

            return response()->json([
                'success' => false,
                'message' => "Insufficient stock! {$itemName} has only {$available} items available."
            ]);
        }

        $usage = InventoryUsage::create([
            'item_id' => $request->item_id,
            'used_quantity' => $request->quantity,
            'usage_type' => 'treatment',
            'used_by' => 1, // Default admin
            'used_for_patient_id' => $request->patient_id,
            'usage_date' => now(),
            'notes' => $request->notes
        ]);

        // Update inventory
        $usage->updateInventory();

        return response()->json([
            'success' => true,
            'message' => 'Item used successfully.',
            'usage_id' => $usage->id
        ]);
    }
}
