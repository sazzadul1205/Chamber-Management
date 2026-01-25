<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\PaymentInstallment;
use App\Models\TreatmentSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentAllocationController extends Controller
{
    /*-----------------------------------
     | List Allocations
     *-----------------------------------*/
    public function index(Request $request)
    {
        $query = PaymentAllocation::with(['payment', 'installment', 'treatmentSession', 'creator']);

        if ($request->has('payment_id')) $query->where('payment_id', $request->payment_id);
        if ($request->has('installment_id')) $query->where('installment_id', $request->installment_id);
        if ($request->has('treatment_session_id')) $query->where('treatment_session_id', $request->treatment_session_id);

        $allocations = $query->orderBy('allocation_date', 'desc')->paginate(20);

        return view('payment-allocations.index', compact('allocations'));
    }

    /*-----------------------------------
     | Show Form to Create Allocation
     *-----------------------------------*/
    public function create(Request $request)
    {
        $payment = Payment::findOrFail($request->payment_id);

        // Unpaid installments
        $installments = PaymentInstallment::where('invoice_id', $payment->invoice_id)
            ->where('status', '!=', 'paid')
            ->get();

        // Treatment sessions (if any)
        $sessions = $payment->for_treatment_session_id
            ? TreatmentSession::where('treatment_id', $payment->treatmentSession->treatment_id)->get()
            : [];

        // Calculate remaining allocation amount
        $allocatedAmount = PaymentAllocation::where('payment_id', $payment->id)->sum('allocated_amount');
        $remainingAmount = $payment->amount - $allocatedAmount;

        return view('payment-allocations.create', compact('payment', 'installments', 'sessions', 'remainingAmount'));
    }

    /*-----------------------------------
     | Store New Allocation
     *-----------------------------------*/
    public function store(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'installment_id' => 'nullable|exists:payment_installments,id',
            'treatment_session_id' => 'nullable|exists:treatment_sessions,id',
            'allocated_amount' => 'required|numeric|min:0.01',
            'allocation_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (!$request->installment_id && !$request->treatment_session_id) {
            return redirect()->back()->withErrors(['error' => 'Select at least one allocation target.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $payment = Payment::findOrFail($request->payment_id);
            $remainingAmount = $payment->amount - PaymentAllocation::where('payment_id', $payment->id)->sum('allocated_amount');

            if ($request->allocated_amount > $remainingAmount) {
                return redirect()->back()
                    ->withErrors(['allocated_amount' => "Allocation exceeds remaining payment amount (₹{$remainingAmount})"])
                    ->withInput();
            }

            // Create allocation
            $allocation = PaymentAllocation::create([
                'payment_id' => $request->payment_id,
                'installment_id' => $request->installment_id,
                'treatment_session_id' => $request->treatment_session_id,
                'allocated_amount' => $request->allocated_amount,
                'allocation_date' => $request->allocation_date,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Update installment and invoice if allocated to installment
            if ($request->installment_id) {
                $this->applyAllocationToInstallment($request->installment_id, $request->allocated_amount);
            }

            DB::commit();
            return redirect()->route('payment-allocations.show', $allocation)
                ->with('success', 'Payment allocation created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create allocation: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /*-----------------------------------
     | Show Single Allocation
     *-----------------------------------*/
    public function show(PaymentAllocation $paymentAllocation)
    {
        $paymentAllocation->load([
            'payment.invoice.patient',
            'installment',
            'treatmentSession.treatment',
            'creator'
        ]);

        return view('payment-allocations.show', compact('paymentAllocation'));
    }

    /*-----------------------------------
     | Edit Allocation
     *-----------------------------------*/
    public function edit(PaymentAllocation $paymentAllocation)
    {
        $installments = PaymentInstallment::where('invoice_id', $paymentAllocation->payment->invoice_id)->get();
        $sessions = $paymentAllocation->payment->for_treatment_session_id
            ? TreatmentSession::where('treatment_id', $paymentAllocation->payment->treatmentSession->treatment_id)->get()
            : [];

        return view('payment-allocations.edit', compact('paymentAllocation', 'installments', 'sessions'));
    }

    /*-----------------------------------
     | Update Allocation
     *-----------------------------------*/
    public function update(Request $request, PaymentAllocation $paymentAllocation)
    {
        $request->validate([
            'installment_id' => 'nullable|exists:payment_installments,id',
            'treatment_session_id' => 'nullable|exists:treatment_sessions,id',
            'allocated_amount' => 'required|numeric|min:0.01',
            'allocation_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (!$request->installment_id && !$request->treatment_session_id) {
            return redirect()->back()->withErrors(['error' => 'Select at least one allocation target.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $oldAmount = $paymentAllocation->allocated_amount;

            // Revert previous allocation from installment & invoice
            if ($paymentAllocation->installment_id) {
                $this->revertAllocationFromInstallment($paymentAllocation->installment_id, $oldAmount);
            }

            // Update allocation
            $paymentAllocation->update($request->only('installment_id', 'treatment_session_id', 'allocated_amount', 'allocation_date', 'notes'));

            // Apply new allocation to installment & invoice
            if ($request->installment_id) {
                $this->applyAllocationToInstallment($request->installment_id, $request->allocated_amount);
            }

            DB::commit();
            return redirect()->route('payment-allocations.show', $paymentAllocation)
                ->with('success', 'Payment allocation updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update allocation: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /*-----------------------------------
     | Delete Allocation
     *-----------------------------------*/
    public function destroy(PaymentAllocation $paymentAllocation)
    {
        DB::beginTransaction();
        try {
            if ($paymentAllocation->installment_id) {
                $this->revertAllocationFromInstallment($paymentAllocation->installment_id, $paymentAllocation->allocated_amount);
            }

            $paymentAllocation->delete();
            DB::commit();

            return redirect()->route('payment-allocations.index')
                ->with('success', 'Payment allocation deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete allocation: ' . $e->getMessage()]);
        }
    }

    /*-----------------------------------
     | AJAX: Get Allocations by Payment
     *-----------------------------------*/
    public function getByPayment($paymentId)
    {
        $allocations = PaymentAllocation::with(['installment', 'treatmentSession'])
            ->where('payment_id', $paymentId)->get();

        return response()->json($allocations);
    }

    /*-----------------------------------
     | AJAX: Allocation Summary for Payment
     *-----------------------------------*/
    public function getSummary($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $totalAllocated = PaymentAllocation::where('payment_id', $paymentId)->sum('allocated_amount');
        $remainingAmount = $payment->amount - $totalAllocated;

        return response()->json([
            'total_allocated' => $totalAllocated,
            'remaining_amount' => $remainingAmount,
            'payment_amount' => $payment->amount,
        ]);
    }

    /*-----------------------------------
     | Helper: Apply Allocation to Installment & Invoice
     *-----------------------------------*/
    private function applyAllocationToInstallment($installmentId, $amount)
    {
        $installment = PaymentInstallment::find($installmentId);
        $installment->amount_paid += $amount;
        $installment->status = $installment->amount_paid >= $installment->amount_due
            ? 'paid' : ($installment->amount_paid > 0 ? 'partial' : 'pending');
        $installment->save();

        $invoice = $installment->invoice;
        $invoice->paid_amount += $amount;
        $invoice->balance_amount = $invoice->total_amount - $invoice->paid_amount;
        $invoice->status = $invoice->balance_amount <= 0
            ? 'paid' : ($invoice->paid_amount > 0 ? 'partial' : 'pending');
        $invoice->save();
    }

    /*-----------------------------------
     | Helper: Revert Allocation from Installment & Invoice
     *-----------------------------------*/
    private function revertAllocationFromInstallment($installmentId, $amount)
    {
        $installment = PaymentInstallment::find($installmentId);
        $installment->amount_paid -= $amount;
        $installment->status = $installment->amount_paid <= 0
            ? 'pending' : ($installment->amount_paid < $installment->amount_due ? 'partial' : 'paid');
        $installment->save();

        $invoice = $installment->invoice;
        $invoice->paid_amount -= $amount;
        $invoice->balance_amount = $invoice->total_amount - $invoice->paid_amount;
        $invoice->status = $invoice->paid_amount <= 0
            ? 'pending' : ($invoice->paid_amount < $invoice->total_amount ? 'partial' : 'paid');
        $invoice->save();
    }
}
