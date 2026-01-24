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
    public function index(Request $request)
    {
        $query = Payment::with(['invoice', 'patient', 'installment', 'treatmentSession']);

        // Apply filters
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_no', 'like', "%{$search}%")
                    ->orWhere('reference_no', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                            ->orWhere('patient_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('invoice', function ($q2) use ($search) {
                        $q2->where('invoice_no', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->latest()->paginate(20);

        // Summary statistics
        $summary = [
            'total' => Payment::count(),
            'total_amount' => Payment::sum('amount'),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'cancelled' => Payment::where('status', 'cancelled')->count(),
            'refunded' => Payment::where('status', 'refunded')->count(),
        ];

        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $invoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->orderBy('invoice_no', 'desc')
            ->limit(100)
            ->get();

        return view('payments.index', compact('payments', 'summary', 'patients', 'invoices'));
    }

    public function create(Request $request)
    {
        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $invoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->orderBy('invoice_no', 'desc')
            ->get();

        // Pre-select if coming from invoice
        $preSelected = [
            'invoice_id' => $request->invoice_id,
            'patient_id' => $request->patient_id,
            'installment_id' => $request->installment_id
        ];

        $paymentMethods = [
            'cash' => 'Cash',
            'card' => 'Card',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            'mobile_banking' => 'Mobile Banking',
            'other' => 'Other'
        ];

        $paymentTypes = [
            'full' => 'Full Payment',
            'partial' => 'Partial Payment',
            'advance' => 'Advance Payment'
        ];

        return view('payments.create', compact('patients', 'invoices', 'preSelected', 'paymentMethods', 'paymentTypes'));
    }

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

        $invoice = Invoice::find($request->invoice_id);

        // Validate amount
        if ($request->payment_type == 'full') {
            $maxAmount = $invoice->balance_amount;
        } else {
            $maxAmount = $invoice->balance_amount;
        }

        if ($request->amount > $maxAmount) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Amount cannot exceed invoice balance of ৳" . number_format($maxAmount, 2));
        }

        // Check installment if provided
        if ($request->installment_id) {
            $installment = PaymentInstallment::find($request->installment_id);
            if ($installment && $request->amount > $installment->balance) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Amount cannot exceed installment balance of ৳" . number_format($installment->balance, 2));
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
                'created_by' => 1 // Default admin user
            ]);

            // Process the payment
            $payment->processPayment();

            DB::commit();

            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

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

    public function edit($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status != 'pending') {
            return redirect()->route('payments.show', $payment->id)
                ->with('error', 'Only pending payments can be edited.');
        }

        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $invoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->orderBy('invoice_no', 'desc')
            ->get();

        $installments = $payment->invoice->installments;

        $paymentMethods = [
            'cash' => 'Cash',
            'card' => 'Card',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            'mobile_banking' => 'Mobile Banking',
            'other' => 'Other'
        ];

        $paymentTypes = [
            'full' => 'Full Payment',
            'partial' => 'Partial Payment',
            'advance' => 'Advance Payment'
        ];

        return view('payments.edit', compact('payment', 'patients', 'invoices', 'installments', 'paymentMethods', 'paymentTypes'));
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status != 'pending') {
            return redirect()->route('payments.show', $payment->id)
                ->with('error', 'Only pending payments can be edited.');
        }

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

        $invoice = Invoice::find($request->invoice_id);

        // Validate amount
        if ($request->payment_type == 'full') {
            $maxAmount = $invoice->balance_amount + $payment->amount; // Add back the original amount
        } else {
            $maxAmount = $invoice->balance_amount + $payment->amount;
        }

        if ($request->amount > $maxAmount) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Amount cannot exceed invoice balance of ৳" . number_format($maxAmount, 2));
        }

        DB::beginTransaction();

        try {
            // Remove old payment impact
            $payment->invoice->deductPayment($payment->amount);
            if ($payment->installment_id) {
                $payment->installment->deductPayment($payment->amount);
            }

            // Update payment
            $payment->update([
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
                'remarks' => $request->remarks
            ]);

            // Process the updated payment
            $payment->processPayment();

            DB::commit();

            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating payment: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status != 'pending') {
            return redirect()->route('payments.show', $payment->id)
                ->with('error', 'Only pending payments can be deleted.');
        }

        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    public function refund(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        if (!$payment->is_refundable) {
            return redirect()->route('payments.show', $payment->id)
                ->with('error', 'Payment is not refundable (only payments within 30 days can be refunded).');
        }

        try {
            $refundPayment = $payment->refund($request->reason);

            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Payment refunded successfully. Refund reference: ' . $refundPayment->payment_no);
        } catch (\Exception $e) {
            return redirect()->route('payments.show', $payment->id)
                ->with('error', 'Error refunding payment: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        if ($payment->status != 'pending') {
            return redirect()->route('payments.show', $payment->id)
                ->with('error', 'Only pending payments can be cancelled.');
        }

        try {
            $payment->cancel($request->reason);

            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Payment cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payments.show', $payment->id)
                ->with('error', 'Error cancelling payment: ' . $e->getMessage());
        }
    }

    public function allocate(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'installment_id' => 'required|exists:payment_installments,id',
            'amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'notes' => 'nullable|string|max:255'
        ]);

        $installment = PaymentInstallment::find($request->installment_id);

        if ($installment->invoice_id != $payment->invoice_id) {
            return redirect()->route('payments.show', $payment->id)
                ->with('error', 'Installment does not belong to the same invoice.');
        }

        try {
            $payment->allocateToInstallment($request->installment_id, $request->amount, $request->notes);

            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Payment allocated to installment successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payments.show', $payment->id)
                ->with('error', 'Error allocating payment: ' . $e->getMessage());
        }
    }

    public function generateReceipt($id)
    {
        $payment = Payment::with(['invoice', 'patient', 'createdBy'])->findOrFail($id);

        if ($payment->receipt) {
            return redirect()->route('payments.show', $payment->id)
                ->with('info', 'Receipt already generated.');
        }

        // Create receipt (we'll create Receipt model in package 29)
        // For now, just show a message
        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'Receipt generation will be available in the next package.');
    }

    public function patientPayments($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $payments = Payment::where('patient_id', $patientId)
            ->with('invoice')
            ->latest()
            ->paginate(20);

        $summary = [
            'total_payments' => $payments->total(),
            'total_amount' => $payments->sum('amount'),
            'total_invoices' => Invoice::where('patient_id', $patientId)->count(),
            'total_balance' => Invoice::where('patient_id', $patientId)->sum('balance_amount')
        ];

        return view('payments.patient', compact('patient', 'payments', 'summary'));
    }

    public function invoicePayments($invoiceId)
    {
        $invoice = Invoice::with('patient')->findOrFail($invoiceId);
        $payments = Payment::where('invoice_id', $invoiceId)
            ->with(['installment', 'createdBy'])
            ->latest()
            ->get();

        return view('payments.invoice', compact('invoice', 'payments'));
    }

    public function dailyCollection(Request $request)
    {
        $date = $request->filled('date') ? $request->date : date('Y-m-d');

        $payments = Payment::with(['invoice', 'patient'])
            ->whereDate('payment_date', $date)
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'cash' => $payments->where('payment_method', 'cash')->sum('amount'),
            'card' => $payments->where('payment_method', 'card')->sum('amount'),
            'bank_transfer' => $payments->where('payment_method', 'bank_transfer')->sum('amount'),
            'cheque' => $payments->where('payment_method', 'cheque')->sum('amount'),
            'mobile_banking' => $payments->where('payment_method', 'mobile_banking')->sum('amount'),
            'other' => $payments->where('payment_method', 'other')->sum('amount')
        ];

        return view('payments.reports.daily', compact('payments', 'summary', 'date'));
    }

    public function getInstallments($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $installments = $invoice->installments()
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json($installments);
    }
}
