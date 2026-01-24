<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'createdBy'])->latest();

        // Apply filters
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhere('reference_no', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('item', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('item_code', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $query->paginate(20);

        $items = InventoryItem::where('status', 'active')->orderBy('name')->get();
        $transactionTypes = [
            'purchase' => 'Purchase',
            'adjustment' => 'Adjustment',
            'consumption' => 'Consumption',
            'return' => 'Return',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out'
        ];

        return view('inventory_transactions.index', compact('transactions', 'items', 'transactionTypes'));
    }

    public function create()
    {
        $items = InventoryItem::where('status', 'active')->orderBy('name')->get();
        $transactionTypes = [
            'purchase' => 'Purchase',
            'adjustment' => 'Adjustment',
            'consumption' => 'Consumption',
            'return' => 'Return',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out'
        ];

        return view('inventory_transactions.create', compact('items', 'transactionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'transaction_type' => 'required|in:purchase,adjustment,consumption,return,transfer_in,transfer_out',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'transaction_date' => 'required|date'
        ]);

        // Calculate total amount
        $totalAmount = $request->unit_price ? $request->unit_price * $request->quantity : null;

        $transaction = InventoryTransaction::create([
            'transaction_code' => InventoryTransaction::generateTransactionCode(),
            'item_id' => $request->item_id,
            'transaction_type' => $request->transaction_type,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_amount' => $totalAmount,
            'reference_no' => $request->reference_no,
            'notes' => $request->notes,
            'transaction_date' => $request->transaction_date,
            'created_by' => 1 // Default admin user
        ]);

        // Update stock based on transaction type
        $transaction->updateStock();

        return redirect()->route('inventory_transactions.show', $transaction->id)
            ->with('success', 'Transaction recorded successfully.');
    }

    public function show($id)
    {
        $transaction = InventoryTransaction::with(['item', 'createdBy'])->findOrFail($id);

        // Get item's current stock
        $currentStock = InventoryStock::where('item_id', $transaction->item_id)->first();

        // Get recent transactions for this item
        $itemTransactions = InventoryTransaction::where('item_id', $transaction->item_id)
            ->where('id', '!=', $id)
            ->latest()
            ->limit(10)
            ->get();

        return view('inventory_transactions.show', compact('transaction', 'currentStock', 'itemTransactions'));
    }

    public function edit($id)
    {
        $transaction = InventoryTransaction::findOrFail($id);
        $items = InventoryItem::where('status', 'active')->orderBy('name')->get();
        $transactionTypes = [
            'purchase' => 'Purchase',
            'adjustment' => 'Adjustment',
            'consumption' => 'Consumption',
            'return' => 'Return',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out'
        ];

        return view('inventory_transactions.edit', compact('transaction', 'items', 'transactionTypes'));
    }

    public function update(Request $request, $id)
    {
        $transaction = InventoryTransaction::findOrFail($id);

        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'transaction_type' => 'required|in:purchase,adjustment,consumption,return,transfer_in,transfer_out',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'transaction_date' => 'required|date'
        ]);

        // Revert old stock impact
        $this->revertStockImpact($transaction);

        // Calculate total amount
        $totalAmount = $request->unit_price ? $request->unit_price * $request->quantity : null;

        $transaction->update([
            'item_id' => $request->item_id,
            'transaction_type' => $request->transaction_type,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_amount' => $totalAmount,
            'reference_no' => $request->reference_no,
            'notes' => $request->notes,
            'transaction_date' => $request->transaction_date
        ]);

        // Apply new stock impact
        $transaction->updateStock();

        return redirect()->route('inventory_transactions.show', $transaction->id)
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy($id)
    {
        $transaction = InventoryTransaction::findOrFail($id);

        // Revert stock impact before deleting
        $this->revertStockImpact($transaction);

        $transaction->delete();

        return redirect()->route('inventory_transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function purchaseReport(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'createdBy'])
            ->where('transaction_type', 'purchase')
            ->latest();

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $purchases = $query->paginate(20);

        // Summary
        $summary = [
            'total_purchases' => $purchases->total(),
            'total_quantity' => $purchases->sum('quantity'),
            'total_amount' => $purchases->sum('total_amount')
        ];

        $items = InventoryItem::where('status', 'active')->orderBy('name')->get();

        return view('inventory_transactions.reports.purchase', compact('purchases', 'summary', 'items'));
    }

    public function consumptionReport(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'createdBy'])
            ->where('transaction_type', 'consumption')
            ->latest();

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $consumptions = $query->paginate(20);

        // Summary
        $summary = [
            'total_consumptions' => $consumptions->total(),
            'total_quantity' => $consumptions->sum('quantity')
        ];

        $items = InventoryItem::where('status', 'active')->orderBy('name')->get();

        return view('inventory_transactions.reports.consumption', compact('consumptions', 'summary', 'items'));
    }

    public function stockMovement($itemId)
    {
        $item = InventoryItem::with('stock')->findOrFail($itemId);
        $transactions = InventoryTransaction::where('item_id', $itemId)
            ->with('createdBy')
            ->latest()
            ->paginate(20);

        return view('inventory_transactions.movement', compact('item', 'transactions'));
    }

    // Helper method to revert stock impact
    private function revertStockImpact($transaction)
    {
        $stock = InventoryStock::where('item_id', $transaction->item_id)->first();

        if ($stock) {
            switch ($transaction->transaction_type) {
                case 'purchase':
                case 'transfer_in':
                case 'return':
                    $stock->current_stock = max(0, $stock->current_stock - $transaction->quantity);
                    break;
                case 'consumption':
                case 'transfer_out':
                    $stock->current_stock += $transaction->quantity;
                    break;
            }

            $stock->last_updated = now();
            $stock->updated_by = 1; // Default admin
            $stock->save();
        }
    }
}
