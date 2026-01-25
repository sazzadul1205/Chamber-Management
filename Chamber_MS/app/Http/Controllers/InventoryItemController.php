<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    // =========================
    // LIST INVENTORY ITEMS
    // =========================
    public function index(Request $request)
    {
        $query = InventoryItem::with('stock'); // eager load stock

        // --- Search filter ---
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // --- Category filter ---
        if ($request->category && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // --- Status filter ---
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // --- Stock status filter ---
        if ($request->stock_status === 'low_stock') {
            $query->lowStock();
        }

        // --- Pagination ---
        $inventoryItems = $query->orderBy('category')
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        // --- Categories & units for filters ---
        $categories = InventoryItem::categories();
        $units = InventoryItem::units();

        // --- Stats ---
        $totalItems = InventoryItem::count();
        $activeItems = InventoryItem::active()->count();
        $lowStockItems = InventoryItem::active()->lowStock()->count();

        return view('backend.inventory_items.index', compact(
            'inventoryItems',
            'categories',
            'units',
            'totalItems',
            'activeItems',
            'lowStockItems'
        ));
    }

    // =========================
    // CREATE INVENTORY ITEM FORM
    // =========================
    public function create()
    {
        return view('backend.inventory_items.create', [
            'categories' => InventoryItem::categories(),
            'units' => InventoryItem::units(),
            'subcategories' => $this->getSubcategories(),
        ]);
    }

    // =========================
    // STORE NEW ITEM
    // =========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:30|unique:inventory_items,item_code',
            'name' => 'required|string|max:100',
            'category' => 'required',
            'subcategory' => 'nullable|string|max:50',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:100',
            'reorder_level' => 'required|integer|min:0',
            'optimum_level' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        $item = InventoryItem::create($validated);

        // Initialize stock
        $item->stock()->create([
            'current_stock' => 0,
            'opening_stock' => 0,
        ]);

        return redirect()
            ->route('backend.inventory-items.index')
            ->with('success', 'Inventory item created successfully.');
    }

    // =========================
    // SHOW ITEM DETAILS
    // =========================
    public function show(InventoryItem $inventoryItem)
    {
        $inventoryItem->load([
            'stock',
            'transactions' => fn($q) => $q->latest()->limit(20),
            'usages' => fn($q) => $q->latest()->limit(20),
        ]);

        return view('backend.inventory_items.show', compact('inventoryItem'));
    }

    // =========================
    // EDIT ITEM FORM
    // =========================
    public function edit(InventoryItem $inventoryItem)
    {
        return view('backend.inventory_items.edit', [
            'inventoryItem' => $inventoryItem,
            'categories' => InventoryItem::categories(),
            'units' => InventoryItem::units(),
            'subcategories' => $this->getSubcategories(),
        ]);
    }

    // =========================
    // UPDATE ITEM
    // =========================
    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:30|unique:inventory_items,item_code,' . $inventoryItem->id,
            'name' => 'required|string|max:100',
            'category' => 'required',
            'subcategory' => 'nullable|string|max:50',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:100',
            'reorder_level' => 'required|integer|min:0',
            'optimum_level' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        $inventoryItem->update($validated);

        return redirect()
            ->route('backend.inventory-items.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    // =========================
    // DELETE ITEM
    // =========================
    public function destroy(InventoryItem $inventoryItem)
    {
        if ($inventoryItem->stock && $inventoryItem->stock->current_stock > 0) {
            return back()->with('error', 'Cannot delete item with stock.');
        }

        if ($inventoryItem->transactions()->exists()) {
            return back()->with('error', 'Cannot delete item with transaction history.');
        }

        $inventoryItem->delete();

        return redirect()
            ->route('backend.inventory-items.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    // =========================
    // AUTOCOMPLETE FOR SEARCH
    // =========================
    public function autocomplete(Request $request)
    {
        $query = $request->get('query', '');

        $items = InventoryItem::active()
            ->where(function ($q) use ($query) {
                $q->where('item_code', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'value' => "{$item->item_code} - {$item->name}",
                'code' => $item->item_code,
                'name' => $item->name,
                'unit' => $item->unit,
                'category' => $item->category,
                'current_stock' => $item->current_stock,
            ]);

        return response()->json($items);
    }

    // =========================
    // GENERATE ITEM CODE
    // =========================
    public function generateCode(Request $request)
    {
        $category = $request->get('category', 'other');
        $prefix = strtoupper(substr($category, 0, 3));

        $last = InventoryItem::where('item_code', 'like', "{$prefix}-%")
            ->orderByDesc('item_code')
            ->first();

        $next = $last ? ((int) substr($last->item_code, -3)) + 1 : 1;

        return response()->json([
            'code' => "{$prefix}-" . str_pad($next, 3, '0', STR_PAD_LEFT)
        ]);
    }

    // =========================
    // SUBCATEGORIES HELPER
    // =========================
    private function getSubcategories()
    {
        return [
            'consumable' => ['gloves', 'masks', 'syringes', 'needles'],
            'instrument' => ['surgical', 'examination'],
            'medicine_related' => ['antiseptics', 'analgesics'],
            'office_supplies' => ['stationery', 'printing'],
        ];
    }

    // =========================
    // EXPORT INVENTORY ITEMS TO CSV
    // =========================
    public function export()
    {
        $items = InventoryItem::all();

        return response()->streamDownload(function () use ($items) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'Item Code',
                'Name',
                'Category',
                'Unit',
                'Stock',
                'Reorder Level',
                'Status'
            ]);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->item_code,
                    $item->name,
                    $item->category,
                    $item->unit,
                    $item->current_stock,
                    $item->reorder_level,
                    ucfirst($item->status),
                ]);
            }

            fclose($file);
        }, 'inventory_items_' . date('Y-m-d') . '.csv');
    }
}
