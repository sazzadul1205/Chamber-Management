<?php

namespace App\Http\Controllers;

use App\Models\InventoryUsage;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\Treatment;
use App\Models\Prescription;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryUsageController extends Controller
{
    protected array $usageTypes = [
        'treatment'    => 'Treatment',
        'prescription' => 'Prescription',
        'wastage'      => 'Wastage',
        'other'        => 'Other',
    ];

    // ================================================
    // LIST / FILTER
    // ================================================
    public function index(Request $request)
    {
        $query = InventoryUsage::with(['item', 'treatment', 'prescription', 'patient', 'usedBy']);

        foreach (['item_id', 'patient_id', 'treatment_id', 'prescription_id', 'usage_type'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->$field);
            }
        }

        if ($request->filled('start_date')) $query->whereDate('usage_date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('usage_date', '<=', $request->end_date);

        // Full-text search across related models
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhereHas('item', fn($q2) => $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%"))
                    ->orWhereHas('patient', fn($q2) => $q2->where('full_name', 'like', "%{$search}%")
                        ->orWhere('patient_code', 'like', "%{$search}%"));
            });
        }

        $usages = $query->latest()->paginate(20);

        return view('inventory_usage.index', [
            'usages' => $usages,
            'items' => InventoryItem::active()->orderBy('name')->get(),
            'patients' => Patient::active()->orderBy('full_name')->get(),
            'treatments' => Treatment::notCancelled()->latest()->limit(100)->get(),
            'usageTypes' => $this->usageTypes,
        ]);
    }

    // ================================================
    // CREATE FORM
    // ================================================
    public function create(Request $request)
    {
        return view('inventory_usage.create', [
            'items' => InventoryItem::active()->withStock()->orderBy('name')->get(),
            'patients' => Patient::active()->orderBy('full_name')->get(),
            'treatments' => Treatment::notCancelled()->latest()->limit(100)->get(),
            'prescriptions' => Prescription::active()->latest()->limit(100)->get(),
            'preSelected' => $request->only('treatment_id', 'prescription_id', 'patient_id'),
            'usageTypes' => $this->usageTypes,
        ]);
    }

    // ================================================
    // STORE NEW USAGE
    // ================================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'used_quantity' => 'required|integer|min:1',
            'usage_type' => 'required|in:' . implode(',', array_keys($this->usageTypes)),
            'used_for_patient_id' => 'nullable|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'usage_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        $this->checkStockOrFail($data['item_id'], $data['used_quantity']);

        $usage = InventoryUsage::create(array_merge($data, ['used_by' => 1])); // default admin

        $usage->updateInventory();

        return redirect()->route('inventory_usage.show', $usage->id)
            ->with('success', 'Inventory usage recorded successfully.');
    }

    // ================================================
    // SHOW USAGE
    // ================================================
    public function show($id)
    {
        $usage = InventoryUsage::with(['item', 'treatment', 'prescription', 'patient', 'usedBy'])->findOrFail($id);

        return view('inventory_usage.show', [
            'usage' => $usage,
            'currentStock' => InventoryStock::where('item_id', $usage->item_id)->first(),
            'itemUsages' => InventoryUsage::where('item_id', $usage->item_id)->where('id', '!=', $id)->latest()->limit(10)->get(),
        ]);
    }

    // ================================================
    // EDIT FORM
    // ================================================
    public function edit($id)
    {
        return view('inventory_usage.edit', [
            'usage' => $usage = InventoryUsage::findOrFail($id),
            'items' => InventoryItem::active()->orderBy('name')->get(),
            'patients' => Patient::active()->orderBy('full_name')->get(),
            'treatments' => Treatment::notCancelled()->latest()->limit(100)->get(),
            'prescriptions' => Prescription::active()->latest()->limit(100)->get(),
            'usageTypes' => $this->usageTypes,
        ]);
    }

    // ================================================
    // UPDATE USAGE
    // ================================================
    public function update(Request $request, $id)
    {
        $usage = InventoryUsage::findOrFail($id);

        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'used_quantity' => 'required|integer|min:1',
            'usage_type' => 'required|in:' . implode(',', array_keys($this->usageTypes)),
            'used_for_patient_id' => 'nullable|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'usage_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        // If item changed or quantity increased, check stock
        $additionalNeeded = ($usage->item_id == $data['item_id'])
            ? max(0, $data['used_quantity'] - $usage->used_quantity)
            : $data['used_quantity'];
        $this->checkStockOrFail($data['item_id'], $additionalNeeded);

        $usage->revertInventory();
        $usage->update($data);
        $usage->updateInventory();

        return redirect()->route('inventory_usage.show', $usage->id)
            ->with('success', 'Inventory usage updated successfully.');
    }

    // ================================================
    // DELETE USAGE
    // ================================================
    public function destroy($id)
    {
        $usage = InventoryUsage::findOrFail($id);
        $usage->revertInventory();
        $usage->delete();

        return redirect()->route('inventory_usage.index')
            ->with('success', 'Inventory usage record deleted successfully.');
    }

    // ================================================
    // QUICK USAGE (AJAX)
    // ================================================
    public function quickUse(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:1',
            'patient_id' => 'required|exists:patients,id',
            'notes' => 'required|string|max:255'
        ]);

        $this->checkStockOrFail($data['item_id'], $data['quantity']);

        $usage = InventoryUsage::create([
            'item_id' => $data['item_id'],
            'used_quantity' => $data['quantity'],
            'usage_type' => 'treatment',
            'used_by' => 1,
            'used_for_patient_id' => $data['patient_id'],
            'usage_date' => now(),
            'notes' => $data['notes'],
        ]);

        $usage->updateInventory();

        return response()->json(['success' => true, 'usage_id' => $usage->id]);
    }

    // ================================================
    // HELPER: Stock check
    // ================================================
    protected function checkStockOrFail($itemId, $neededQty)
    {
        $stock = InventoryStock::where('item_id', $itemId)->first();
        if (!$stock || $stock->current_stock < $neededQty) {
            $itemName = InventoryItem::find($itemId)->name;
            $available = $stock?->current_stock ?? 0;
            abort(400, "Insufficient stock! {$itemName} has only {$available} items. Needed: {$neededQty}");
        }
    }
}
