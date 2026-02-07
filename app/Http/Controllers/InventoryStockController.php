<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryStockController extends Controller
{
    /**
     * Stock listing with filters & summary
     */
    public function index(Request $request)
    {
        $query = InventoryStock::with(['item', 'updatedBy']);

        // --------------------------------------------------
        // FILTERS
        // --------------------------------------------------

        // Filter by item category
        if ($request->filled('category')) {
            $query->whereHas(
                'item',
                fn($q) =>
                $q->where('category', $request->category)
            );
        }

        // Filter by stock / expiry status
        if ($request->filled('status')) {
            match ($request->status) {
                'low_stock'     => $query->lowStock()->where('current_stock', '>', 0),
                'out_of_stock'  => $query->outOfStock(),
                'in_stock'      => $query->inStock(),
                'expiring_soon' => $query->expiringSoon(30),
                'expired'       => $query->expired(),
                default         => null,
            };
        }

        // Search by item name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas(
                'item',
                fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%")
            );
        }

        // --------------------------------------------------
        // DATA
        // --------------------------------------------------

        $stocks = $query
            ->orderBy('current_stock', 'asc')
            ->paginate(20);

        // Dashboard summary
        $summary = [
            'total_items'  => InventoryStock::count(),
            'in_stock'     => InventoryStock::inStock()->count(),
            'low_stock'    => InventoryStock::lowStock()->where('current_stock', '>', 0)->count(),
            'out_of_stock' => InventoryStock::outOfStock()->count(),
            'total_value'  => InventoryStock::sum(DB::raw('current_stock * unit_cost')),
        ];

        // Categories for filter dropdown
        $categories = InventoryItem::distinct()->pluck('category');

        return view('inventory_stock.index', compact(
            'stocks',
            'summary',
            'categories'
        ));
    }

    /**
     * Create stock record
     */
    public function create()
    {
        // Only items without existing stock
        $items = InventoryItem::where('status', 'active')
            ->whereDoesntHave('stock')
            ->orderBy('name')
            ->get();

        return view('inventory_stock.create', compact('items'));
    }

    /**
     * Store initial stock
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id'        => 'required|exists:inventory_items,id|unique:inventory_stock,item_id',
            'opening_stock'  => 'required|integer|min:0',
            'unit_cost'      => 'nullable|numeric|min:0',
            'selling_price'  => 'nullable|numeric|min:0',
            'expiry_date'    => 'nullable|date|after:today',
            'location'       => 'nullable|string|max:50',
        ]);

        $stock = InventoryStock::create([
            'item_id'           => $request->item_id,
            'opening_stock'     => $request->opening_stock,
            'current_stock'     => $request->opening_stock,
            'unit_cost'         => $request->unit_cost,
            'selling_price'     => $request->selling_price,
            'last_purchase_date' => now(),
            'expiry_date'       => $request->expiry_date,
            'location'          => $request->location,
            'last_updated'      => now(),
            'updated_by'        => 1, // static admin for now
        ]);

        return redirect()
            ->route('inventory_stock.show', $stock)
            ->with('success', 'Stock record created successfully.');
    }

    /**
     * View stock details
     */
    public function show($id)
    {
        $stock = InventoryStock::with(['item', 'updatedBy'])->findOrFail($id);

        return view('inventory_stock.show', compact('stock'));
    }

    /**
     * Edit stock
     */
    public function edit($id)
    {
        $stock = InventoryStock::with('item')->findOrFail($id);

        return view('inventory_stock.edit', compact('stock'));
    }

    /**
     * Update stock details
     */
    public function update(Request $request, $id)
    {
        $stock = InventoryStock::findOrFail($id);

        $request->validate([
            'current_stock' => 'required|integer|min:0',
            'unit_cost'     => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'expiry_date'   => 'nullable|date',
            'location'      => 'nullable|string|max:50',
        ]);

        $stock->update([
            'current_stock' => $request->current_stock,
            'unit_cost'     => $request->unit_cost,
            'selling_price' => $request->selling_price,
            'expiry_date'   => $request->expiry_date,
            'location'      => $request->location,
            'last_updated'  => now(),
            'updated_by'    => 1,
        ]);

        return redirect()
            ->route('inventory_stock.show', $stock)
            ->with('success', 'Stock updated successfully.');
    }

    /**
     * Delete stock record
     */
    public function destroy($id)
    {
        InventoryStock::findOrFail($id)->delete();

        return redirect()
            ->route('inventory_stock.index')
            ->with('success', 'Stock record deleted successfully.');
    }

    /**
     * Adjust stock quantity manually
     */
    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity'        => 'required|integer|min:1',
            'reason'          => 'required|string|max:255',
            'reference_no'    => 'nullable|string|max:50',
        ]);

        $stock    = InventoryStock::findOrFail($id);
        $previous = $stock->current_stock;

        $newStock = match ($request->adjustment_type) {
            'add'    => $previous + $request->quantity,
            'remove' => max(0, $previous - $request->quantity),
            'set'    => $request->quantity,
        };

        $stock->update([
            'current_stock' => $newStock,
            'last_updated'  => now(),
            'updated_by'    => 1,
        ]);

        return redirect()
            ->route('inventory_stock.show', $stock)
            ->with('success', "Stock adjusted from {$previous} to {$newStock}.");
    }

    /**
     * Low stock report
     */
    public function lowStockReport()
    {
        $lowStocks = InventoryStock::with('item')
            ->lowStock()
            ->where('current_stock', '>', 0)
            ->orderBy('current_stock')
            ->get();

        return view('inventory_stock.reports.low_stock', compact('lowStocks'));
    }

    /**
     * Expiry report
     */
    public function expiryReport()
    {
        $expiringSoon = InventoryStock::with('item')
            ->expiringSoon(30)
            ->orderBy('expiry_date')
            ->get();

        $expired = InventoryStock::with('item')
            ->expired()
            ->orderBy('expiry_date')
            ->get();

        return view('inventory_stock.reports.expiry', compact(
            'expiringSoon',
            'expired'
        ));
    }
}
