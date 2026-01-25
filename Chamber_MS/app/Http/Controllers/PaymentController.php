<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PaymentInstallment;
use App\Models\TreatmentSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /*-----------------------------------
     | List Payments with Filters
     *-----------------------------------*/
    public function index(Request $request)
    {
        $query = Payment::with(['invoice', 'patient', 'installment', 'treatmentSession']);

        // Apply filters
        foreach (['patient_id', 'invoice_id', 'status', 'payment_method', 'payment_type'] as $field) {
            if ($request->filled($field)) $query->where($field, $request->$field);
        }

        if ($request->filled('start_date')) $query->whereDate('payment_date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('payment_date', '<=', $request->end_date);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_no', 'like', "%{$search}%")
                    ->orWhere('reference_no', 'like', "%{$search}%")
                    ->orWhereHas('patient', fn($q2) => $q2->where('full_name', 'like', "%{$search}%")->orWhere('patient_code', 'like', "%{$search}%"))
                    ->orWhereHas('invoice', fn($q2) => $q2->where('invoice_no', 'like', "%{$search}%"));
            });
        }

        $payments = $query->latest()->paginate(20);

        // Summary statistics
        $summary = Payment::selectRaw('COUNT(*) as total, SUM(amount) as total_amount')
            ->first()
            ->toArray() + [
                'completed' => Payment::where('status', 'completed')->count(),
                'pending' => Payment::where('status', 'pending')->count(),
                'cancelled' => Payment::where('status', 'cancelled')->count(),
                'refunded' => Payment::where('status', 'refunded')->count()
            ];

        $patients = Patient::active()->orderBy('full_name')->get();
        $invoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->orderByDesc('invoice_no')->limit(100)->get();

        return view('payments.index', compact('payments', 'summary', 'patients', 'invoices'));
    }

    /*-----------------------------------
     | Show Create Payment Form
     *-----------------------------------*/
    public function create(Request $request)
    {
        $patients = Patient::active()->orderBy('full_name')->get();
        $invoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->orderByDesc('invoice_no')->get();

        // Preselected values if coming from invoice page
        $preSelected = $request->only(['invoice_id', 'patient_id', 'installment_id']);

        $paymentMethods = ['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank Transfer', 'cheque' => 'Cheque', 'mobile_banking' => 'Mobile Banking', 'other' => 'Other'];
        $paymentTypes = ['full' => 'Full Payment', 'partial' => 'Partial Payment', 'advance' => 'Advance Payment'];

        return view('payments.create', compact('patients', 'invoices', 'preSelected', 'paymentMethods', 'paymentTypes'));
    }

    /*-----------------------------------
     | Store Payment
     *-----------------------------------*/
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'patient_id' => 'required|exists:patients,id',
            'installment_id' => 'nullable|exists:payment_installments,id',
            'is_advance' => 'boolean',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque,mobile_banking,other',
            'payment_type' => 'required|in:full,partial,advance',
            'amount' => 'required|numeric|min:0.01',
            'reference_no' => 'nullable|string|max:50',
            'card_last_four' => 'nullable|string|size:4',
            'bank_name' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500'
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $maxAmount = $invoice->balance_amount;

        if ($request->amount > $maxAmount) {
            return back()->withInput()->with('error', "Amount cannot exceed invoice balance of ৳" . number_format($maxAmount, 2));
        }

        if ($request->installment_id) {
            $installment = PaymentInstallment::findOrFail($request->installment_id);
            if ($request->amount > $installment->balance) {
                return back()->withInput()->with('error', "Amount cannot exceed installment balance of ৳" . number_format($installment->balance, 2));
            }
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'payment_no' => Payment::generatePaymentNo(),
                'invoice_id' => $request->invoice_id,
                'patient_id' => $request->patient_id,
                'installment_id' => $request->installment_id,
                'is_advance' => $request->is_advance ?? false,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'payment_type' => $request->payment_type,
                'amount' => $request->amount,
                'reference_no' => $request->reference_no,
                'card_last_four' => $request->card_last_four,
                'bank_name' => $request->bank_name,
                'remarks' => $request->remarks,
                'status' => 'completed',
                'created_by' => 1
            ]);

            $payment->processPayment();
            DB::commit();

            return redirect()->route('payments.show', $payment->id)->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /*-----------------------------------
     | Show Payment
     *-----------------------------------*/
    public function show($id)
    {
        $payment = Payment::with([
            'invoice',
            'patient',
            'installment',
            'treatmentSession',
            'createdBy',
            'allocations.installment',
            'allocations.treatmentSession',
            'receipt'
        ])->findOrFail($id);

        return view('payments.show', compact('payment'));
    }

    /*-----------------------------------
     | Edit Payment
     *-----------------------------------*/
    public function edit($id)
    {
        $payment = Payment::findOrFail($id);

        $patients = Patient::active()->orderBy('full_name')->get();
        $invoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->orderByDesc('invoice_no')->get();
        $installments = $payment->invoice->installments;

        $paymentMethods = ['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank Transfer', 'cheque' => 'Cheque', 'mobile_banking' => 'Mobile Banking', 'other' => 'Other'];
        $paymentTypes = ['full' => 'Full Payment', 'partial' => 'Partial Payment', 'advance' => 'Advance Payment'];

        return view('payments.edit', compact('payment', 'patients', 'invoices', 'installments', 'paymentMethods', 'paymentTypes'));
    }

    /*-----------------------------------
     | Update Payment
     *-----------------------------------*/
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'patient_id' => 'required|exists:patients,id',
            'installment_id' => 'nullable|exists:payment_installments,id',
            'is_advance' => 'boolean',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque,mobile_banking,other',
            'payment_type' => 'required|in:full,partial,advance',
            'amount' => 'required|numeric|min:0.01',
            'reference_no' => 'nullable|string|max:50',
            'card_last_four' => 'nullable|string|size:4',
            'bank_name' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500'
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $maxAmount = $invoice->balance_amount + $payment->amount;

        DB::beginTransaction();
        try {
            // Remove old impact
            $payment->invoice->deductPayment($payment->amount);
            if ($payment->installment_id) $payment->installment->deductPayment($payment->amount);

            $payment->update($request->only([
                'invoice_id',
                'patient_id',
                'installment_id',
                'is_advance',
                'payment_date',
                'payment_method',
                'payment_type',
                'amount',
                'reference_no',
                'card_last_four',
                'bank_name',
                'remarks'
            ]));

            $payment->processPayment();
            DB::commit();

            return redirect()->route('payments.show', $payment->id)->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating payment: ' . $e->getMessage());
        }
    }

    /*-----------------------------------
     | Delete Payment
     *-----------------------------------*/
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    /*-----------------------------------
     | Refund Payment
     *-----------------------------------*/
    public function refund(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $payment = Payment::findOrFail($id);

        $refundPayment = $payment->refund($request->reason);
        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment refunded: ' . $refundPayment->payment_no);
    }

    /*-----------------------------------
     | Cancel Payment
     *-----------------------------------*/
    public function cancel(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $payment = Payment::findOrFail($id);
        $payment->cancel($request->reason);

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment cancelled successfully.');
    }

    /*-----------------------------------
 | Allocate Payment to Installment
 *-----------------------------------*/
    public function allocate(Request $request, $id)
    {
        // First, get the payment
        $payment = Payment::findOrFail($id);

        // Now you can use $payment->amount in validation
        $request->validate([
            'installment_id' => 'required|exists:payment_installments,id',
            'amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'notes' => 'nullable|string|max:255'
        ]);

        // Allocate payment
        $payment->allocateToInstallment($request->installment_id, $request->amount, $request->notes);

        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'Payment allocated successfully.');
    }


    /*-----------------------------------
     | Get Daily Collection
     *-----------------------------------*/
    public function dailyCollection(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');

        $payments = Payment::with(['invoice', 'patient'])
            ->whereDate('payment_date', $date)
            ->where('status', 'completed')
            ->orderByDesc('payment_date')
            ->get();

        $summary = collect($payments)->groupBy('payment_method')->map(fn($group) => $group->sum('amount'));
        return view('payments.reports.daily', compact('payments', 'summary', 'date'));
    }

    /*-----------------------------------
     | Get Invoice Installments
     *-----------------------------------*/
    public function getInstallments($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installments = $invoice->installments()->where('status', '!=', 'paid')->orderBy('due_date')->get();

        return response()->json($installments);
    }
}
