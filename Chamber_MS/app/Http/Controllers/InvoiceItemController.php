<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    public function index($invoiceId)
    {
        $invoice = Invoice::with('items')->findOrFail($invoiceId);
        return view('invoice_items.index', compact('invoice'));
    }

    public function create($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot add items to a non-draft invoice.');
        }

        return view('invoice_items.create', compact('invoice'));
    }

    public function store(Request $request, $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot add items to a non-draft invoice.');
        }

        $request->validate([
            'item_type' => 'required|in:procedure,medicine,inventory,other',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100'
        ]);

        $total = ($request->quantity * $request->unit_price) - ($request->discount ?? 0);
        $tax = ($total * ($request->tax_percent ?? 0)) / 100;
        $totalWithTax = $total + $tax;

        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => $request->item_type,
            'item_id' => $request->item_id,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'discount' => $request->discount ?? 0,
            'tax_percent' => $request->tax_percent ?? 0,
            'total_amount' => $totalWithTax
        ]);

        // Update invoice totals
        $invoice->calculateTotals();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Item added to invoice successfully.');
    }

    public function edit($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = InvoiceItem::where('invoice_id', $invoiceId)->findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot edit items in a non-draft invoice.');
        }

        return view('invoice_items.edit', compact('invoice', 'item'));
    }

    public function update(Request $request, $invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = InvoiceItem::where('invoice_id', $invoiceId)->findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot edit items in a non-draft invoice.');
        }

        $request->validate([
            'item_type' => 'required|in:procedure,medicine,inventory,other',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100'
        ]);

        $total = ($request->quantity * $request->unit_price) - ($request->discount ?? 0);
        $tax = ($total * ($request->tax_percent ?? 0)) / 100;
        $totalWithTax = $total + $tax;

        $item->update([
            'item_type' => $request->item_type,
            'item_id' => $request->item_id,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'discount' => $request->discount ?? 0,
            'tax_percent' => $request->tax_percent ?? 0,
            'total_amount' => $totalWithTax
        ]);

        // Update invoice totals
        $invoice->calculateTotals();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Item updated successfully.');
    }

    public function destroy($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = InvoiceItem::where('invoice_id', $invoiceId)->findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot remove items from a non-draft invoice.');
        }

        $item->delete();

        // Update invoice totals
        $invoice->calculateTotals();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Item removed from invoice successfully.');
    }
}
