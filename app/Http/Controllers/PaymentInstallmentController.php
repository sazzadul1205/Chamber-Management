<?php

namespace App\Http\Controllers;

use App\Models\PaymentInstallment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentInstallmentController extends Controller
{
    /*=========================================
     | List Installments for an Invoice
     *=========================================*/
    public function index($invoiceId)
    {
        $invoice = Invoice::with(['installments', 'patient'])->findOrFail($invoiceId);
        return view('payment_installments.index', compact('invoice'));
    }

    /*=========================================
     | Show form to create a new installment
     *=========================================*/
    public function create($invoiceId)
    {
        $invoice = Invoice::with('patient')->findOrFail($invoiceId);

        // Only draft invoices with installment plan can have installments
        if ($invoice->payment_plan != 'installment' || $invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot add installments to this invoice.');
        }

        $nextInstallmentNumber = $invoice->installments()->max('installment_number') + 1;

        return view('payment_installments.create', compact('invoice', 'nextInstallmentNumber'));
    }

    /*=========================================
     | Store new installment
     *=========================================*/
    public function store(Request $request, $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->payment_plan != 'installment' || $invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot add installments to this invoice.');
        }

        $request->validate([
            'installment_number' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'due_date' => 'required|date|after_or_equal:' . $invoice->invoice_date,
            'amount_due' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500'
        ]);

        // Prevent duplicate installment numbers
        $existing = $invoice->installments()
            ->where('installment_number', $request->installment_number)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()
                ->with('error', 'Installment number already exists.');
        }

        PaymentInstallment::create([
            'invoice_id' => $invoice->id,
            'installment_number' => $request->installment_number,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'amount_due' => $request->amount_due,
            'amount_paid' => 0,
            'status' => 'pending',
            'created_by' => 1
        ]);

        return redirect()->route('payment_installments.index', $invoice->id)
            ->with('success', 'Installment added successfully.');
    }

    /*=========================================
     | Show single installment
     *=========================================*/
    public function show($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = PaymentInstallment::where('invoice_id', $invoiceId)->findOrFail($id);

        return view('payment_installments.show', compact('invoice', 'installment'));
    }

    /*=========================================
     | Edit installment
     *=========================================*/
    public function edit($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = PaymentInstallment::where('invoice_id', $invoiceId)->findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot edit installments of a non-draft invoice.');
        }

        return view('payment_installments.edit', compact('invoice', 'installment'));
    }

    /*=========================================
     | Update installment
     *=========================================*/
    public function update(Request $request, $invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = PaymentInstallment::where('invoice_id', $invoiceId)->findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot edit installments of a non-draft invoice.');
        }

        $request->validate([
            'installment_number' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'due_date' => 'required|date|after_or_equal:' . $invoice->invoice_date,
            'amount_due' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500'
        ]);

        // Prevent duplicate numbers
        $existing = $invoice->installments()
            ->where('installment_number', $request->installment_number)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()
                ->with('error', 'Installment number already exists.');
        }

        $installment->update([
            'installment_number' => $request->installment_number,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'amount_due' => $request->amount_due,
            'notes' => $request->notes
        ]);

        return redirect()->route('payment_installments.index', $invoice->id)
            ->with('success', 'Installment updated successfully.');
    }

    /*=========================================
     | Delete installment
     *=========================================*/
    public function destroy($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = PaymentInstallment::where('invoice_id', $invoiceId)->findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot delete installments from a non-draft invoice.');
        }

        if ($installment->amount_paid > 0) {
            return redirect()->route('payment_installments.index', $invoice->id)
                ->with('error', 'Cannot delete installment with payments.');
        }

        $installment->delete();

        return redirect()->route('payment_installments.index', $invoice->id)
            ->with('success', 'Installment deleted successfully.');
    }

    /*=========================================
     | Record payment against installment
     *=========================================*/
    public function addPayment(Request $request, $invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = PaymentInstallment::where('invoice_id', $invoiceId)->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $installment->balance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque,mobile_banking',
            'reference_no' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255'
        ]);

        // Update installment and invoice
        $installment->addPayment($request->amount);

        // Store payment info in notes temporarily
        $installment->notes = ($installment->notes ? $installment->notes . "\n" : '') .
            date('Y-m-d H:i') . ': Payment of ৳' . number_format($request->amount, 2) .
            ' via ' . $request->payment_method .
            ($request->reference_no ? ' (Ref: ' . $request->reference_no . ')' : '') .
            ($request->notes ? ' - ' . $request->notes : '');
        $installment->save();

        return redirect()->route('payment_installments.show', [$invoice->id, $installment->id])
            ->with('success', 'Payment recorded successfully.');
    }

    /*=========================================
     | Apply late fee to installment
     *=========================================*/
    public function applyLateFee(Request $request, $invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = PaymentInstallment::where('invoice_id', $invoiceId)->findOrFail($id);

        $request->validate([
            'fee_amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255'
        ]);

        $installment->applyLateFee($request->fee_amount, $request->reason);

        return redirect()->route('payment_installments.show', [$invoice->id, $installment->id])
            ->with('success', 'Late fee applied successfully.');
    }

    /*=========================================
     | Overdue installments report
     *=========================================*/
    public function overdueReport()
    {
        $overdueInstallments = PaymentInstallment::with(['invoice.patient'])
            ->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->get();

        $totalOverdue = $overdueInstallments->sum('balance');

        return view('payment_installments.reports.overdue', compact('overdueInstallments', 'totalOverdue'));
    }

    /*=========================================
     | Due soon installments report
     *=========================================*/
    public function dueSoonReport()
    {
        $dueSoonInstallments = PaymentInstallment::with(['invoice.patient'])
            ->whereIn('status', ['pending', 'partial'])
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->orderBy('due_date', 'asc')
            ->get();

        return view('payment_installments.reports.due_soon', compact('dueSoonInstallments'));
    }

    /*=========================================
     | Update status (e.g., mark overdue)
     *=========================================*/
    public function updateStatus($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = PaymentInstallment::where('invoice_id', $invoiceId)->findOrFail($id);

        $installment->checkAndUpdateStatus();

        return redirect()->route('payment_installments.show', [$invoice->id, $installment->id])
            ->with('success', 'Status updated.');
    }
}
