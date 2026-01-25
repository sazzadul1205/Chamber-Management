<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    /* =====================================================
     |  LISTING & FILTERING
     ===================================================== */

    /**
     * Display all inventory transactions with filters
     */
    public function index(Request $request)
    {
        $transactions = InventoryTransaction::with(['item', 'createdBy'])
            ->latest()
            ->when(
                $request->item_id,
                fn($q) =>
                $q->where('item_id', $request->item_id)
            )
            ->when(
                $request->transaction_type,
                fn($q) =>
                $q->where('transaction_type', $request->transaction_type)
            )
            ->when(
                $request->start_date,
                fn($q) =>
                $q->whereDate('transaction_date', '>=', $request->start_date)
            )
            ->when(
                $request->end_date,
                fn($q) =>
                $q->whereDate('transaction_date', '<=', $request->end_date)
            )
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('transaction_code', 'like', "%{$request->search}%")
                        ->orWhere('reference_no', 'like', "%{$request->search}%")
                        ->orWhere('notes', 'like', "%{$request->search}%")
                        ->orWhereHas(
                            'item',
                            fn($i) =>
                            $i->where('name', 'like', "%{$request->search}%")
                                ->orWhere('item_code', 'like', "%{$request->search}%")
                        );
                });
            })
            ->paginate(20);

        $items = InventoryItem::active()->orderBy('name')->get();

        return view('inventory_transactions.index', [
            'transactions' => $transactions,
            'items'        => $items,
            'types'        => $this->transactionTypes(),
        ]);
    }

    /* =====================================================
     |  CREATE & STORE
     ===================================================== */

    /**
     * Show create form
     */
    public function create()
    {
        return view('inventory_transactions.create', [
            'items' => InventoryItem::active()->orderBy('name')->get(),
            'types' => $this->transactionTypes(),
        ]);
    }

    /**
     * Persist a new transaction and apply stock movement
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data, &$transaction) {

            // Create transaction record
            $transaction = InventoryTransaction::create([
                ...$data,
                'transaction_code' => InventoryTransaction::generateTransactionCode(),
                'total_amount'     => $data['unit_price']
                    ? $data['unit_price'] * $data['quantity']
                    : null,
                'created_by'       => auth()->id(),
            ]);

            // Apply stock movement (NO SAFETY NETS)
            $transaction->applyToStock();
        });

        return redirect()
            ->route('inventory_transactions.show', $transaction)
            ->with('success', 'Transaction recorded successfully.');
    }

    /* =====================================================
     |  VIEW DETAILS
     ===================================================== */

    /**
     * Show single transaction details
     */
    public function show(InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->load(['item', 'createdBy']);

        return view('inventory_transactions.show', [
            'transaction'       => $inventoryTransaction,
            'currentStock'      => InventoryStock::where('item_id', $inventoryTransaction->item_id)->firstOrFail(),
            'itemTransactions'  => InventoryTransaction::where('item_id', $inventoryTransaction->item_id)
                ->whereKeyNot($inventoryTransaction->id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    /* =====================================================
     |  EDIT & UPDATE
     ===================================================== */

    /**
     * Show edit form
     */
    public function edit(InventoryTransaction $inventoryTransaction)
    {
        return view('inventory_transactions.edit', [
            'transaction' => $inventoryTransaction,
            'items'       => InventoryItem::active()->orderBy('name')->get(),
            'types'       => $this->transactionTypes(),
        ]);
    }

    /**
     * Update transaction and re-apply stock movement
     */
    public function update(Request $request, InventoryTransaction $inventoryTransaction)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($inventoryTransaction, $data) {

            // Revert previous stock effect
            $this->revertStockImpact($inventoryTransaction);

            // Update transaction
            $inventoryTransaction->update([
                ...$data,
                'total_amount' => $data['unit_price']
                    ? $data['unit_price'] * $data['quantity']
                    : null,
            ]);

            // Apply new stock effect
            $inventoryTransaction->applyToStock();
        });

        return redirect()
            ->route('inventory_transactions.show', $inventoryTransaction)
            ->with('success', 'Transaction updated successfully.');
    }

    /* =====================================================
     |  DELETE
     ===================================================== */

    /**
     * Delete transaction and rollback stock
     */
    public function destroy(InventoryTransaction $inventoryTransaction)
    {
        DB::transaction(function () use ($inventoryTransaction) {
            $this->revertStockImpact($inventoryTransaction);
            $inventoryTransaction->delete();
        });

        return redirect()
            ->route('inventory_transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    /* =====================================================
     |  REPORTS
     ===================================================== */

    public function purchaseReport(Request $request)
    {
        return $this->transactionReport('purchase', $request);
    }

    public function consumptionReport(Request $request)
    {
        return $this->transactionReport('consumption', $request);
    }

    /* =====================================================
     |  STOCK MOVEMENT
     ===================================================== */

    public function stockMovement(InventoryItem $item)
    {
        return view('inventory_transactions.movement', [
            'item'         => $item->load('stock'),
            'transactions' => InventoryTransaction::where('item_id', $item->id)
                ->with('createdBy')
                ->latest()
                ->paginate(20),
        ]);
    }

    /* =====================================================
     |  INTERNAL HELPERS
     ===================================================== */

    /**
     * Centralized validation rules
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'item_id'           => 'required|exists:inventory_items,id',
            'transaction_type'  => 'required|in:purchase,adjustment,consumption,return,transfer_in,transfer_out',
            'quantity'          => 'required|integer|min:1',
            'unit_price'        => 'nullable|numeric|min:0',
            'reference_no'      => 'nullable|string|max:100',
            'notes'             => 'nullable|string|max:500',
            'transaction_date'  => 'required|date',
        ]);
    }

    /**
     * Reverse stock effect of a transaction
     * NO guards, NO max(), NO silent failure
     */
    private function revertStockImpact(InventoryTransaction $transaction): void
    {
        $stock = InventoryStock::where('item_id', $transaction->item_id)->firstOrFail();

        match ($transaction->transaction_type) {
            'purchase', 'transfer_in', 'return'
            => $stock->decrement('current_stock', $transaction->quantity),

            'consumption', 'transfer_out'
            => $stock->increment('current_stock', $transaction->quantity),

            default => null,
        };

        $stock->update([
            'last_updated' => now(),
            'updated_by'   => auth()->id(),
        ]);
    }

    /**
     * Central transaction types list
     */
    private function transactionTypes(): array
    {
        return [
            'purchase'     => 'Purchase',
            'adjustment'   => 'Adjustment',
            'consumption'  => 'Consumption',
            'return'       => 'Return',
            'transfer_in'  => 'Transfer In',
            'transfer_out' => 'Transfer Out',
        ];
    }

    /**
     * Shared logic for reports
     */
    private function transactionReport(string $type, Request $request)
    {
        $query = InventoryTransaction::with(['item', 'createdBy'])
            ->where('transaction_type', $type)
            ->latest()
            ->when($request->start_date, fn($q) => $q->whereDate('transaction_date', '>=', $request->start_date))
            ->when($request->end_date, fn($q) => $q->whereDate('transaction_date', '<=', $request->end_date))
            ->when($request->item_id, fn($q) => $q->where('item_id', $request->item_id));

        // Calculate summary
        $summary = [
            'total_records' => $query->count(),
            'total_quantity' => $query->sum('quantity'),
            'total_amount' => $query->sum('total_amount'),
        ];

        // Then paginate
        $transactions = $query->paginate(20);

        return view("inventory_transactions.reports.$type", [
            'transactions' => $transactions,
            'summary' => $summary,
            'items' => InventoryItem::active()->orderBy('name')->get(),
        ]);
    }
}
