<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Payment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    /**
     * Display a listing of receipts
     */
    public function index(Request $request)
    {
        $query = Receipt::with(['payment', 'patient', 'creator', 'printer'])
            ->orderBy('receipt_date', 'desc');

        // Apply filters
        if ($request->filled('receipt_no')) {
            $query->where('receipt_no', 'like', '%' . $request->receipt_no . '%');
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('payment_id')) {
            $query->where('payment_id', $request->payment_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('receipt_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('receipt_date', '<=', $request->date_to);
        }

        if ($request->filled('printed')) {
            if ($request->printed === 'yes') {
                $query->whereNotNull('printed_at');
            } else {
                $query->whereNull('printed_at');
            }
        }

        $receipts = $query->paginate(20);

        // For patient filter dropdown
        $patients = Patient::where('status', 'active')
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'patient_code']);

        return view('receipts.index', compact('receipts', 'patients'));
    }

    /**
     * Show form to create a new receipt
     */
    public function create(Request $request)
    {
        $payment = null;
        $payments = collect();

        if ($request->filled('payment_id')) {
            $payment = Payment::with(['patient', 'invoice'])->findOrFail($request->payment_id);

            // Check if receipt already exists for this payment
            $existingReceipt = Receipt::where('payment_id', $payment->id)->first();
            if ($existingReceipt) {
                return redirect()->route('receipts.show', $existingReceipt)
                    ->with('info', 'A receipt already exists for this payment.');
            }
        } else {
            // Get payments without receipts
            $payments = Payment::whereDoesntHave('receipt')
                ->where('status', 'completed')
                ->with(['patient', 'invoice'])
                ->orderBy('payment_date', 'desc')
                ->limit(50)
                ->get();
        }

        return view('receipts.create', compact('payment', 'payments'));
    }

    /**
     * Store a newly created receipt
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'receipt_date' => 'required|date',
            'receipt_no' => 'nullable|string|max:20|unique:receipts,receipt_no',
        ]);

        DB::beginTransaction();

        try {
            $payment = Payment::with(['patient'])->findOrFail($request->payment_id);

            // Check if receipt already exists
            $existingReceipt = Receipt::where('payment_id', $payment->id)->first();
            if ($existingReceipt) {
                return redirect()->route('receipts.show', $existingReceipt)
                    ->with('warning', 'Receipt already exists for this payment.');
            }

            // Create receipt
            $receipt = Receipt::create([
                'payment_id' => $payment->id,
                'patient_id' => $payment->patient_id,
                'receipt_date' => $request->receipt_date,
                'receipt_no' => $request->receipt_no,
                'amount_words' => Receipt::amountToWords($payment->amount),
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('receipts.show', $receipt)
                ->with('success', 'Receipt created successfully. You can now print it.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create receipt: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified receipt
     */
    public function show(Receipt $receipt)
    {
        $receipt->load([
            'payment.invoice.treatment',
            'payment.allocations.installment',
            'payment.allocations.treatmentSession',
            'patient',
            'creator',
            'printer'
        ]);

        return view('receipts.show', compact('receipt'));
    }

    /**
     * Show the form for editing the specified receipt
     */
    public function edit(Receipt $receipt)
    {
        // Only allow editing of unprinted receipts
        if ($receipt->is_printed) {
            return redirect()->route('receipts.show', $receipt)
                ->with('warning', 'Printed receipts cannot be edited.');
        }

        $receipt->load(['payment']);

        return view('receipts.edit', compact('receipt'));
    }

    /**
     * Update the specified receipt
     */
    public function update(Request $request, Receipt $receipt)
    {
        // Only allow updating of unprinted receipts
        if ($receipt->is_printed) {
            return redirect()->route('receipts.show', $receipt)
                ->with('warning', 'Printed receipts cannot be updated.');
        }

        $request->validate([
            'receipt_date' => 'required|date',
            'receipt_no' => 'required|string|max:20|unique:receipts,receipt_no,' . $receipt->id,
        ]);

        $receipt->update([
            'receipt_date' => $request->receipt_date,
            'receipt_no' => $request->receipt_no,
        ]);

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'Receipt updated successfully.');
    }

    /**
     * Remove the specified receipt
     */
    public function destroy(Receipt $receipt)
    {
        // Only allow deletion of unprinted receipts
        if ($receipt->is_printed) {
            return redirect()->route('receipts.show', $receipt)
                ->with('warning', 'Printed receipts cannot be deleted.');
        }

        $receipt->delete();

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt deleted successfully.');
    }

    /**
     * Generate PDF for receipt
     */
    public function pdf(Receipt $receipt)
    {
        $receipt->load([
            'payment.invoice.treatment',
            'payment.allocations.installment',
            'payment.allocations.treatmentSession',
            'patient',
            'creator',
            'printer'
        ]);

        // Mark as printed if not already
        if (!$receipt->is_printed) {
            $receipt->markAsPrinted(auth()->id());
        }

        $data = $receipt->getReceiptData();

        $pdf = PDF::loadView('receipts.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download("receipt-{$receipt->receipt_no}.pdf");
    }

    /**
     * Preview receipt as HTML
     */
    public function preview(Receipt $receipt)
    {
        $receipt->load([
            'payment.invoice.treatment',
            'payment.allocations.installment',
            'payment.allocations.treatmentSession',
            'patient',
            'creator',
            'printer'
        ]);

        $data = $receipt->getReceiptData();

        return view('receipts.preview', $data);
    }

    /**
     * Mark receipt as printed
     */
    public function markPrinted(Receipt $receipt)
    {
        if (!$receipt->is_printed) {
            $receipt->markAsPrinted(auth()->id());
            return redirect()->route('receipts.show', $receipt)
                ->with('success', 'Receipt marked as printed.');
        }

        return redirect()->route('receipts.show', $receipt)
            ->with('info', 'Receipt is already marked as printed.');
    }

    /**
     * Search payments for receipt creation
     */
    public function searchPayments(Request $request)
    {
        $query = Payment::whereDoesntHave('receipt')
            ->where('status', 'completed')
            ->with(['patient', 'invoice']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_no', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('patient_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('invoice', function ($q) use ($search) {
                        $q->where('invoice_no', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')
            ->limit(20)
            ->get();

        return response()->json($payments);
    }

    /**
     * Get receipt statistics
     */
    public function statistics(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? $request->start_date
            : now()->startOfMonth()->toDateString();

        $endDate = $request->filled('end_date')
            ? $request->end_date
            : now()->endOfMonth()->toDateString();

        $stats = DB::table('receipts')
            ->selectRaw('
                COUNT(*) as total_receipts,
                SUM(payments.amount) as total_amount,
                COUNT(CASE WHEN receipts.printed_at IS NOT NULL THEN 1 END) as printed_receipts,
                COUNT(CASE WHEN receipts.printed_at IS NULL THEN 1 END) as unprinted_receipts
            ')
            ->join('payments', 'receipts.payment_id', '=', 'payments.id')
            ->whereDate('receipts.receipt_date', '>=', $startDate)
            ->whereDate('receipts.receipt_date', '<=', $endDate)
            ->first();

        // Daily receipt counts
        $dailyStats = DB::table('receipts')
            ->selectRaw('
                DATE(receipt_date) as date,
                COUNT(*) as count,
                SUM(payments.amount) as amount
            ')
            ->join('payments', 'receipts.payment_id', '=', 'payments.id')
            ->whereDate('receipts.receipt_date', '>=', $startDate)
            ->whereDate('receipts.receipt_date', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('receipts.statistics', compact('stats', 'dailyStats', 'startDate', 'endDate'));
    }
}
