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
    /**
     * Display a listing of allocations for a payment
     */
    public function index(Request $request)
    {
        $query = PaymentAllocation::with(['payment', 'installment', 'treatmentSession', 'creator']);

        if ($request->has('payment_id')) {
            $query->where('payment_id', $request->payment_id);
        }

        if ($request->has('installment_id')) {
            $query->where('installment_id', $request->installment_id);
        }

        if ($request->has('treatment_session_id')) {
            $query->where('treatment_session_id', $request->treatment_session_id);
        }

        $allocations = $query->orderBy('allocation_date', 'desc')->paginate(20);

        return view('payment-allocations.index', compact('allocations'));
    }

    /**
     * Show form to create allocation for a specific payment
     */
    public function create(Request $request)
    {
        $payment = Payment::findOrFail($request->payment_id);

        // Get unpaid installments for this payment's invoice
        $installments = PaymentInstallment::where('invoice_id', $payment->invoice_id)
            ->where('status', '!=', 'paid')
            ->orWhereRaw('amount_paid < amount_due')
            ->get();

        // Get treatment sessions for this payment's treatment (if any)
        $sessions = [];
        if ($payment->for_treatment_session_id) {
            $sessions = TreatmentSession::where(
                'treatment_id',
                $payment->installment?->invoice?->treatment_id ??
                    $payment->treatmentSession?->treatment_id
            )
                ->get();
        }

        // Calculate remaining unallocated amount
        $allocatedAmount = PaymentAllocation::where('payment_id', $payment->id)
            ->sum('allocated_amount');
        $remainingAmount = $payment->amount - $allocatedAmount;

        return view('payment-allocations.create', compact(
            'payment',
            'installments',
            'sessions',
            'remainingAmount'
        ));
    }

    /**
     * Store a newly created allocation
     */
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

        // Ensure at least one allocation target
        if (!$request->installment_id && !$request->treatment_session_id) {
            return redirect()->back()
                ->withErrors(['error' => 'Please select either an installment or treatment session to allocate to.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $payment = Payment::findOrFail($request->payment_id);

            // Check if allocation exceeds payment amount
            $allocatedAmount = PaymentAllocation::where('payment_id', $payment->id)
                ->sum('allocated_amount');
            $remainingAmount = $payment->amount - $allocatedAmount;

            if ($request->allocated_amount > $remainingAmount) {
                return redirect()->back()
                    ->withErrors(['allocated_amount' => "Allocation amount exceeds remaining payment amount (₹{$remainingAmount})"])
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

            // Update installment if allocated to one
            if ($request->installment_id) {
                $installment = PaymentInstallment::find($request->installment_id);
                $installment->amount_paid += $request->allocated_amount;

                // Update status if fully paid
                if ($installment->amount_paid >= $installment->amount_due) {
                    $installment->status = 'paid';
                } elseif ($installment->amount_paid > 0) {
                    $installment->status = 'partial';
                }

                $installment->save();

                // Update parent invoice
                $invoice = $installment->invoice;
                $invoice->paid_amount += $request->allocated_amount;
                $invoice->balance_amount = $invoice->total_amount - $invoice->paid_amount;

                if ($invoice->balance_amount <= 0) {
                    $invoice->status = 'paid';
                } elseif ($invoice->paid_amount > 0) {
                    $invoice->status = 'partial';
                }

                $invoice->save();
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

    /**
     * Display the specified allocation
     */
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

    /**
     * Show form to edit allocation
     */
    public function edit(PaymentAllocation $paymentAllocation)
    {
        $paymentAllocation->load(['payment']);

        $installments = PaymentInstallment::where('invoice_id', $paymentAllocation->payment->invoice_id)
            ->get();

        $sessions = TreatmentSession::where(
            'treatment_id',
            $paymentAllocation->payment->installment?->invoice?->treatment_id ??
                $paymentAllocation->payment->treatmentSession?->treatment_id
        )
            ->get();

        return view('payment-allocations.edit', compact(
            'paymentAllocation',
            'installments',
            'sessions'
        ));
    }

    /**
     * Update the specified allocation
     */
    public function update(Request $request, PaymentAllocation $paymentAllocation)
    {
        $request->validate([
            'installment_id' => 'nullable|exists:payment_installments,id',
            'treatment_session_id' => 'nullable|exists:treatment_sessions,id',
            'allocated_amount' => 'required|numeric|min:0.01',
            'allocation_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Ensure at least one allocation target
        if (!$request->installment_id && !$request->treatment_session_id) {
            return redirect()->back()
                ->withErrors(['error' => 'Please select either an installment or treatment session to allocate to.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $oldAllocation = clone $paymentAllocation;
            $oldAmount = $oldAllocation->allocated_amount;

            // Revert old allocation if amount changed or target changed
            if ($oldAllocation->installment_id) {
                $oldInstallment = PaymentInstallment::find($oldAllocation->installment_id);
                $oldInstallment->amount_paid -= $oldAmount;

                // Recalculate status
                if ($oldInstallment->amount_paid <= 0) {
                    $oldInstallment->status = 'pending';
                } elseif ($oldInstallment->amount_paid < $oldInstallment->amount_due) {
                    $oldInstallment->status = 'partial';
                }

                $oldInstallment->save();

                // Update parent invoice
                $oldInvoice = $oldInstallment->invoice;
                $oldInvoice->paid_amount -= $oldAmount;
                $oldInvoice->balance_amount = $oldInvoice->total_amount - $oldInvoice->paid_amount;

                if ($oldInvoice->paid_amount <= 0) {
                    $oldInvoice->status = 'pending';
                } elseif ($oldInvoice->paid_amount < $oldInvoice->total_amount) {
                    $oldInvoice->status = 'partial';
                }

                $oldInvoice->save();
            }

            // Update allocation
            $paymentAllocation->update([
                'installment_id' => $request->installment_id,
                'treatment_session_id' => $request->treatment_session_id,
                'allocated_amount' => $request->allocated_amount,
                'allocation_date' => $request->allocation_date,
                'notes' => $request->notes,
            ]);

            // Apply new allocation
            if ($request->installment_id) {
                $newInstallment = PaymentInstallment::find($request->installment_id);
                $newInstallment->amount_paid += $request->allocated_amount;

                // Update status
                if ($newInstallment->amount_paid >= $newInstallment->amount_due) {
                    $newInstallment->status = 'paid';
                } elseif ($newInstallment->amount_paid > 0) {
                    $newInstallment->status = 'partial';
                }

                $newInstallment->save();

                // Update parent invoice
                $newInvoice = $newInstallment->invoice;
                $newInvoice->paid_amount += $request->allocated_amount;
                $newInvoice->balance_amount = $newInvoice->total_amount - $newInvoice->paid_amount;

                if ($newInvoice->balance_amount <= 0) {
                    $newInvoice->status = 'paid';
                } elseif ($newInvoice->paid_amount > 0) {
                    $newInvoice->status = 'partial';
                }

                $newInvoice->save();
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

    /**
     * Remove the specified allocation
     */
    public function destroy(PaymentAllocation $paymentAllocation)
    {
        DB::beginTransaction();

        try {
            // Revert allocation before deleting
            if ($paymentAllocation->installment_id) {
                $installment = PaymentInstallment::find($paymentAllocation->installment_id);
                $installment->amount_paid -= $paymentAllocation->allocated_amount;

                // Recalculate status
                if ($installment->amount_paid <= 0) {
                    $installment->status = 'pending';
                } elseif ($installment->amount_paid < $installment->amount_due) {
                    $installment->status = 'partial';
                }

                $installment->save();

                // Update parent invoice
                $invoice = $installment->invoice;
                $invoice->paid_amount -= $paymentAllocation->allocated_amount;
                $invoice->balance_amount = $invoice->total_amount - $invoice->paid_amount;

                if ($invoice->paid_amount <= 0) {
                    $invoice->status = 'pending';
                } elseif ($invoice->paid_amount < $invoice->total_amount) {
                    $invoice->status = 'partial';
                }

                $invoice->save();
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

    /**
     * Get allocations for a specific payment (AJAX)
     */
    public function getByPayment($paymentId)
    {
        $allocations = PaymentAllocation::with(['installment', 'treatmentSession'])
            ->where('payment_id', $paymentId)
            ->get();

        return response()->json($allocations);
    }

    /**
     * Get allocation summary for a payment
     */
    public function getSummary($paymentId)
    {
        $totalAllocated = PaymentAllocation::where('payment_id', $paymentId)
            ->sum('allocated_amount');

        $payment = Payment::findOrFail($paymentId);
        $remainingAmount = $payment->amount - $totalAllocated;

        return response()->json([
            'total_allocated' => $totalAllocated,
            'remaining_amount' => $remainingAmount,
            'payment_amount' => $payment->amount,
        ]);
    }
}
