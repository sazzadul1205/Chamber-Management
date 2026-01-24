<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryStockController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryStock::with(['item', 'updatedBy']);

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'low_stock':
                    $query->whereHas('item', function ($q) {
                        $q->whereRaw('inventory_stock.current_stock <= inventory_items.reorder_level');
                    });
                    break;
                case 'out_of_stock':
                    $query->where('current_stock', '<=', 0);
                    break;
                case 'in_stock':
                    $query->where('current_stock', '>', 0);
                    break;
                case 'expiring_soon':
                    $query->whereNotNull('expiry_date')
                        ->where('expiry_date', '<=', now()->addDays(30))
                        ->where('expiry_date', '>', now());
                    break;
                case 'expired':
                    $query->whereNotNull('expiry_date')
                        ->where('expiry_date', '<', now());
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        $stocks = $query->orderBy('current_stock', 'asc')->paginate(20);

        // Summary statistics
        $summary = [
            'total_items' => InventoryStock::count(),
            'in_stock' => InventoryStock::where('current_stock', '>', 0)->count(),
            'low_stock' => DB::table('inventory_stock')
                ->join('inventory_items', 'inventory_stock.item_id', '=', 'inventory_items.id')
                ->whereRaw('inventory_stock.current_stock <= inventory_items.reorder_level')
                ->where('inventory_stock.current_stock', '>', 0)
                ->count(),
            'out_of_stock' => InventoryStock::where('current_stock', '<=', 0)->count(),
            'total_value' => InventoryStock::sum(DB::raw('current_stock * unit_cost'))
        ];

        // Get categories for filter
        $categories = InventoryItem::distinct()->pluck('category');

        return view('inventory_stock.index', compact('stocks', 'summary', 'categories'));
    }

    public function create()
    {
        $items = InventoryItem::where('status', 'active')
            ->whereDoesntHave('stock') // Items without stock record
            ->orderBy('name')
            ->get();

        return view('inventory_stock.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id|unique:inventory_stock,item_id',
            'opening_stock' => 'required|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'location' => 'nullable|string|max:50'
        ]);

        $stock = InventoryStock::create([
            'item_id' => $request->item_id,
            'opening_stock' => $request->opening_stock,
            'current_stock' => $request->opening_stock,
            'unit_cost' => $request->unit_cost,
            'selling_price' => $request->selling_price,
            'last_purchase_date' => now(),
            'expiry_date' => $request->expiry_date,
            'location' => $request->location,
            'last_updated' => now(),
            'updated_by' => 1 // Default admin user
        ]);

        return redirect()->route('inventory_stock.show', $stock->id)
            ->with('success', 'Stock record created successfully.');
    }

    public function show($id)
    {
        $stock = InventoryStock::with(['item', 'updatedBy'])->findOrFail($id);

        // Get stock movement history (you'll need to create inventory_transactions table later)
        // $transactions = $stock->item->transactions()->latest()->limit(10)->get();

        return view('inventory_stock.show', compact('stock'));
    }

    public function edit($id)
    {
        $stock = InventoryStock::with('item')->findOrFail($id);

        return view('inventory_stock.edit', compact('stock'));
    }

    public function update(Request $request, $id)
    {
        $stock = InventoryStock::findOrFail($id);

        $request->validate([
            'current_stock' => 'required|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'location' => 'nullable|string|max:50'
        ]);

        $stock->update([
            'current_stock' => $request->current_stock,
            'unit_cost' => $request->unit_cost,
            'selling_price' => $request->selling_price,
            'expiry_date' => $request->expiry_date,
            'location' => $request->location,
            'last_updated' => now(),
            'updated_by' => 1 // Default admin user
        ]);

        return redirect()->route('inventory_stock.show', $stock->id)
            ->with('success', 'Stock updated successfully.');
    }

    public function destroy($id)
    {
        $stock = InventoryStock::findOrFail($id);
        $stock->delete();

        return redirect()->route('inventory_stock.index')
            ->with('success', 'Stock record deleted successfully.');
    }

    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reference_no' => 'nullable|string|max:50'
        ]);

        $stock = InventoryStock::findOrFail($id);
        $oldStock = $stock->current_stock;

        switch ($request->adjustment_type) {
            case 'add':
                $newStock = $oldStock + $request->quantity;
                break;
            case 'remove':
                $newStock = max(0, $oldStock - $request->quantity);
                break;
            case 'set':
                $newStock = $request->quantity;
                break;
        }

        $stock->update([
            'current_stock' => $newStock,
            'last_updated' => now(),
            'updated_by' => 1
        ]);

        // Log the transaction (you'll create inventory_transactions table in next package)
        // InventoryTransaction::create([
        //     'item_id' => $stock->item_id,
        //     'transaction_type' => 'adjustment',
        //     'quantity' => $request->quantity,
        //     'reference_no' => $request->reference_no,
        //     'notes' => $request->reason,
        //     'transaction_date' => now(),
        //     'created_by' => 1
        // ]);

        return redirect()->route('inventory_stock.show', $stock->id)
            ->with('success', "Stock adjusted from {$oldStock} to {$newStock}.");
    }

    public function lowStockReport()
    {
        $lowStocks = InventoryStock::with('item')
            ->whereHas('item', function ($q) {
                $q->whereRaw('inventory_stock.current_stock <= inventory_items.reorder_level');
            })
            ->where('current_stock', '>', 0)
            ->orderBy('current_stock', 'asc')
            ->get();

        return view('inventory_stock.reports.low_stock', compact('lowStocks'));
    }

    public function expiryReport()
    {
        $expiringSoon = InventoryStock::with('item')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>', now())
            ->orderBy('expiry_date', 'asc')
            ->get();

        $expired = InventoryStock::with('item')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('inventory_stock.reports.expiry', compact('expiringSoon', 'expired'));
    }
}
