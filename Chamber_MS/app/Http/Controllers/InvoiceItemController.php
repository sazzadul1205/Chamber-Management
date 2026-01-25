<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    /*==========================================
     | Display all items for an invoice
     *==========================================*/
    public function index($invoiceId)
    {
        $invoice = Invoice::with('items')->findOrFail($invoiceId);
        return view('invoice_items.index', compact('invoice'));
    }

    /*==========================================
     | Show form to add a new item
     *==========================================*/
    public function create($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        // Only allow adding to draft invoices
        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot add items to a non-draft invoice.');
        }

        return view('invoice_items.create', compact('invoice'));
    }

    /*==========================================
     | Store new item in invoice
     *==========================================*/
    public function store(Request $request, $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        // Validation
        $data = $request->validate([
            'item_type' => 'required|in:procedure,medicine,inventory,other',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'item_id' => 'nullable|integer'
        ]);

        // Calculate total with discount and tax
        $total = ($data['quantity'] * $data['unit_price']) - ($data['discount'] ?? 0);
        $tax = ($total * ($data['tax_percent'] ?? 0)) / 100;
        $data['total_amount'] = $total + $tax;
        $data['invoice_id'] = $invoice->id;

        InvoiceItem::create($data);

        // Recalculate invoice totals
        $invoice->calculateTotals();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Item added to invoice successfully.');
    }

    /*==========================================
     | Show form to edit an existing item
     *==========================================*/
    public function edit($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = InvoiceItem::where('invoice_id', $invoiceId)->findOrFail($id);

        return view('invoice_items.edit', compact('invoice', 'item'));
    }

    /*==========================================
     | Update an existing item
     *==========================================*/
    public function update(Request $request, $invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = InvoiceItem::where('invoice_id', $invoiceId)->findOrFail($id);

        $data = $request->validate([
            'item_type' => 'required|in:procedure,medicine,inventory,other',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'item_id' => 'nullable|integer'
        ]);

        $total = ($data['quantity'] * $data['unit_price']) - ($data['discount'] ?? 0);
        $tax = ($total * ($data['tax_percent'] ?? 0)) / 100;
        $data['total_amount'] = $total + $tax;

        $item->update($data);

        $invoice->calculateTotals();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Item updated successfully.');
    }

    /*==========================================
     | Delete an item from invoice
     *==========================================*/
    public function destroy($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $item = InvoiceItem::where('invoice_id', $invoiceId)->findOrFail($id);

        $item->delete();

        $invoice->calculateTotals();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Item removed from invoice successfully.');
    }
}
