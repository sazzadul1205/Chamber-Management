<?php

namespace App\Http\Controllers;

use App\Models\PaymentInstallment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentInstallmentController extends Controller
{
    public function index($invoiceId)
    {
        $invoice = Invoice::with(['installments', 'patient'])->findOrFail($invoiceId);

        return view('payment_installments.index', compact('invoice'));
    }

    public function create($invoiceId)
    {
        $invoice = Invoice::with('patient')->findOrFail($invoiceId);

        if ($invoice->payment_plan != 'installment') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'This invoice does not have installment payment plan.');
        }

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot add installments to a non-draft invoice.');
        }

        $nextInstallmentNumber = $invoice->installments()->max('installment_number') + 1;

        return view('payment_installments.create', compact('invoice', 'nextInstallmentNumber'));
    }

    public function store(Request $request, $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->payment_plan != 'installment') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'This invoice does not have installment payment plan.');
        }

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot add installments to a non-draft invoice.');
        }

        $request->validate([
            'installment_number' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'due_date' => 'required|date|after_or_equal:' . $invoice->invoice_date,
            'amount_due' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if installment number already exists
        $existing = $invoice->installments()
            ->where('installment_number', $request->installment_number)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Installment number already exists for this invoice.');
        }

        $installment = PaymentInstallment::create([
            'invoice_id' => $invoice->id,
            'installment_number' => $request->installment_number,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'amount_due' => $request->amount_due,
            'amount_paid' => 0,
            'status' => 'pending',
            'created_by' => 1 // Default admin user
        ]);

        return redirect()->route('payment_installments.index', $invoice->id)
            ->with('success', 'Installment added successfully.');
    }

    public function show($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = PaymentInstallment::where('invoice_id', $invoiceId)->findOrFail($id);

        return view('payment_installments.show', compact('invoice', 'installment'));
    }

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

        // Check if installment number already exists (excluding current)
        $existing = $invoice->installments()
            ->where('installment_number', $request->installment_number)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Installment number already exists for this invoice.');
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
                ->with('error', 'Cannot delete installment with payments. Refund payments first.');
        }

        $installment->delete();

        return redirect()->route('payment_installments.index', $invoice->id)
            ->with('success', 'Installment deleted successfully.');
    }

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

        // Add payment to installment
        $installment->addPayment($request->amount);

        // Create payment record (we'll create Payment model in next package)
        // For now, just record in notes
        $installment->notes = ($installment->notes ? $installment->notes . "\n" : '') .
            date('Y-m-d H:i') . ': Payment of ৳' . number_format($request->amount, 2) .
            ' via ' . $request->payment_method .
            ($request->reference_no ? ' (Ref: ' . $request->reference_no . ')' : '') .
            ($request->notes ? ' - ' . $request->notes : '');
        $installment->save();

        return redirect()->route('payment_installments.show', [$invoice->id, $installment->id])
            ->with('success', 'Payment recorded successfully.');
    }

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

    public function dueSoonReport()
    {
        $dueSoonInstallments = PaymentInstallment::with(['invoice.patient'])
            ->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<=', now()->addDays(7))
            ->where('due_date', '>', now())
            ->orderBy('due_date', 'asc')
            ->get();

        return view('payment_installments.reports.due_soon', compact('dueSoonInstallments'));
    }

    public function updateStatus($invoiceId, $id)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installment = PaymentInstallment::where('invoice_id', $invoiceId)->findOrFail($id);

        $installment->checkAndUpdateStatus();

        return redirect()->route('payment_installments.show', [$invoice->id, $installment->id])
            ->with('success', 'Status updated.');
    }
}
