<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InventoryItemController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        // Only eager load stock if table exists
        if (Schema::hasTable('inventory_stocks')) {
            $query->with('stock');
        }

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        // Category filter
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Stock status filter
        if ($request->has('stock_status') && $request->stock_status != 'all') {
            if ($request->stock_status == 'low_stock' && Schema::hasTable('inventory_stocks')) {
                $query->lowStock();
            }
        }

        $inventoryItems = $query->orderBy('category')
            ->orderBy('name')
            ->paginate(25);

        $categories = InventoryItem::categories();
        $units = InventoryItem::units();

        // Stock statistics
        $totalItems = InventoryItem::count();
        $activeItems = InventoryItem::active()->count();

        // Only count low stock items if table exists
        $lowStockItems = Schema::hasTable('inventory_stocks')
            ? InventoryItem::active()->lowStock()->count()
            : 0;

        return view('backend.inventory_items.index', compact(
            'inventoryItems',
            'categories',
            'units',
            'totalItems',
            'activeItems',
            'lowStockItems'
        ));
    }

    public function create()
    {
        $categories = InventoryItem::categories();
        $units = InventoryItem::units();
        $subcategories = $this->getSubcategories();

        return view('backend.inventory_items.create', compact('categories', 'units', 'subcategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required|string|max:30|unique:inventory_items',
            'name' => 'required|string|max:100',
            'category' => 'required|in:' . implode(',', array_keys(InventoryItem::categories())),
            'subcategory' => 'nullable|string|max:50',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:100',
            'reorder_level' => 'required|integer|min:0',
            'optimum_level' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,discontinued'
        ]);

        $item = InventoryItem::create($request->all());

        // Create initial stock record
        $item->stock()->create([
            'current_stock' => 0,
            'opening_stock' => 0
        ]);

        return redirect()->route('inventory-items.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function show(InventoryItem $inventoryItem)
    {
        $inventoryItem->load(['stock', 'transactions' => function ($q) {
            $q->latest()->limit(20);
        }, 'usages' => function ($q) {
            $q->latest()->limit(20);
        }]);

        return view('backend.inventory_items.show', compact('inventoryItem'));
    }

    public function edit(InventoryItem $inventoryItem)
    {
        $categories = InventoryItem::categories();
        $units = InventoryItem::units();
        $subcategories = $this->getSubcategories();

        return view('backend.inventory_items.edit', compact('inventoryItem', 'categories', 'units', 'subcategories'));
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $request->validate([
            'item_code' => 'required|string|max:30|unique:inventory_items,item_code,' . $inventoryItem->id,
            'name' => 'required|string|max:100',
            'category' => 'required|in:' . implode(',', array_keys(InventoryItem::categories())),
            'subcategory' => 'nullable|string|max:50',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:100',
            'reorder_level' => 'required|integer|min:0',
            'optimum_level' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,discontinued'
        ]);

        $inventoryItem->update($request->all());

        return redirect()->route('inventory-items.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        // Check if item has stock or transactions
        if ($inventoryItem->stock && $inventoryItem->stock->current_stock > 0) {
            return redirect()->route('inventory-items.index')
                ->with('error', 'Cannot delete item. It has stock remaining.');
        }

        if ($inventoryItem->transactions()->exists()) {
            return redirect()->route('inventory-items.index')
                ->with('error', 'Cannot delete item. It has transaction history.');
        }

        $inventoryItem->delete();
        return redirect()->route('inventory-items.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    // API endpoint for autocomplete
    public function autocomplete(Request $request)
    {
        $query = $request->get('query');

        $items = InventoryItem::active()
            ->where(function ($q) use ($query) {
                $q->where('item_code', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->with('stock')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'value' => $item->item_code . ' - ' . $item->name,
                    'code' => $item->item_code,
                    'name' => $item->name,
                    'current_stock' => $item->current_stock,
                    'unit' => $item->unit,
                    'category' => $item->category
                ];
            });

        return response()->json($items);
    }

    // Generate item code
    public function generateCode(Request $request)
    {
        $category = $request->get('category', 'other');
        $prefix = strtoupper(substr($category, 0, 3));

        $lastItem = InventoryItem::where('item_code', 'like', $prefix . '-%')
            ->orderBy('item_code', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastItem) {
            $lastCode = $lastItem->item_code;
            $lastNumber = intval(substr($lastCode, strrpos($lastCode, '-') + 1));
            $nextNumber = $lastNumber + 1;
        }

        $newCode = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return response()->json(['code' => $newCode]);
    }

    // Get subcategories based on category
    private function getSubcategories()
    {
        return [
            'consumable' => ['gloves', 'masks', 'syringes', 'needles', 'wipes', 'cotton', 'disposables'],
            'instrument' => ['hand_instruments', 'examination', 'surgical', 'periodontal', 'prosthodontic', 'endodontic'],
            'dental_material' => ['filling_materials', 'cements', 'impression_materials', 'temporary_materials', 'bonding_agents'],
            'protective_gear' => ['gloves', 'gowns', 'face_protection', 'eye_protection', 'head_covers'],
            'medicine_related' => ['local_anesthetics', 'dressings', 'antiseptics', 'analgesics', 'antibiotics'],
            'office_supplies' => ['stationery', 'forms', 'printing', 'cleaning', 'miscellaneous']
        ];
    }

    // Export to CSV
    public function export(Request $request)
    {
        $items = InventoryItem::with('stock')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_items_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Item Code',
                'Name',
                'Category',
                'Subcategory',
                'Unit',
                'Current Stock',
                'Reorder Level',
                'Manufacturer',
                'Supplier',
                'Status'
            ]);

            // Data
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->item_code,
                    $item->name,
                    $item->category_name,
                    $item->subcategory,
                    $item->unit_name,
                    $item->current_stock,
                    $item->reorder_level,
                    $item->manufacturer,
                    $item->supplier,
                    ucfirst($item->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
