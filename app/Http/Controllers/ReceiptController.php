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
     * Load common receipt relationships
     */
    private function loadRelations(Receipt $receipt): Receipt
    {
        return $receipt->load([
            'payment.invoice.treatment',
            'payment.allocations.installment',
            'payment.allocations.treatmentSession',
            'patient',
            'creator',
            'printer'
        ]);
    }

    /**
     * Display a listing of receipts
     */
    public function index(Request $request)
    {
        // Base query with relations
        $query = Receipt::with(['payment', 'patient', 'creator', 'printer'])
            ->orderBy('receipt_date', 'desc');

        // Dynamic filters
        foreach (['receipt_no', 'patient_id', 'payment_id', 'date_from', 'date_to'] as $filter) {
            if ($request->$filter) {
                if ($filter === 'date_from') $query->whereDate('receipt_date', '>=', $request->$filter);
                elseif ($filter === 'date_to') $query->whereDate('receipt_date', '<=', $request->$filter);
                else $query->where($filter, 'like', '%' . $request->$filter . '%');
            }
        }

        // Filter by printed/unprinted
        if ($request->printed) {
            $query->whereNotNull('printed_at', $request->printed === 'yes');
        }

        $receipts = $query->paginate(20);

        // Active patients for filter dropdown
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
        $payment = $request->payment_id
            ? Payment::with(['patient', 'invoice'])->findOrFail($request->payment_id)
            : null;

        // Existing receipt check
        if ($payment) {
            $existing = Receipt::where('payment_id', $payment->id)->first();
            if ($existing) return redirect()->route('receipts.show', $existing)
                ->with('info', 'Receipt already exists.');
        }

        // List of recent payments without receipts
        $payments = $payment ? collect() : Payment::whereDoesntHave('receipt')
            ->where('status', 'completed')
            ->with(['patient', 'invoice'])
            ->orderByDesc('payment_date')
            ->limit(50)
            ->get();

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
            $payment = Payment::with('patient')->findOrFail($request->payment_id);

            // Prevent duplicate receipt
            if (Receipt::where('payment_id', $payment->id)->exists()) {
                return redirect()->route('receipts.show', $payment->receipt)
                    ->with('warning', 'Receipt already exists.');
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
                ->with('success', 'Receipt created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create receipt: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display a specific receipt
     */
    public function show(Receipt $receipt)
    {
        $receipt = $this->loadRelations($receipt);
        return view('receipts.show', compact('receipt'));
    }

    /**
     * Edit a receipt
     */
    public function edit(Receipt $receipt)
    {
        // Remove safe condition: editing allowed even if printed
        $receipt->load('payment');
        return view('receipts.edit', compact('receipt'));
    }

    /**
     * Update receipt
     */
    public function update(Request $request, Receipt $receipt)
    {
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
     * Delete receipt
     */
    public function destroy(Receipt $receipt)
    {
        $receipt->delete();
        return redirect()->route('receipts.index')
            ->with('success', 'Receipt deleted successfully.');
    }

    /**
     * Generate PDF for receipt
     */
    public function pdf(Receipt $receipt)
    {
        $receipt = $this->loadRelations($receipt);

        // Mark printed
        $receipt->markAsPrinted(auth()->id());

        $pdf = PDF::loadView('receipts.pdf', $receipt->getReceiptData())
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download("receipt-{$receipt->receipt_no}.pdf");
    }

    /**
     * Preview receipt HTML
     */
    public function preview(Receipt $receipt)
    {
        $receipt = $this->loadRelations($receipt);
        return view('receipts.preview', $receipt->getReceiptData());
    }

    /**
     * Mark receipt as printed
     */
    public function markPrinted(Receipt $receipt)
    {
        $receipt->markAsPrinted(auth()->id());
        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'Receipt marked as printed.');
    }

    /**
     * Search payments for receipt creation
     */
    public function searchPayments(Request $request)
    {
        $query = Payment::whereDoesntHave('receipt')
            ->where('status', 'completed')
            ->with(['patient', 'invoice']);

        if ($request->search) {
            $search = $request->search;
            $query->where(
                fn($q) =>
                $q->where('payment_no', 'like', "%{$search}%")
                    ->orWhereHas(
                        'patient',
                        fn($q2) =>
                        $q2->where('full_name', 'like', "%{$search}%")
                            ->orWhere('patient_code', 'like', "%{$search}%")
                    )
                    ->orWhereHas(
                        'invoice',
                        fn($q3) =>
                        $q3->where('invoice_no', 'like', "%{$search}%")
                    )
            );
        }

        return response()->json($query->orderByDesc('payment_date')->limit(20)->get());
    }

    /**
     * Receipt statistics
     */
    public function statistics(Request $request)
    {
        $startDate = $request->start_date ?: now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?: now()->endOfMonth()->toDateString();

        $stats = DB::table('receipts')
            ->selectRaw('
                COUNT(*) as total_receipts,
                SUM(payments.amount) as total_amount,
                COUNT(CASE WHEN receipts.printed_at IS NOT NULL THEN 1 END) as printed_receipts,
                COUNT(CASE WHEN receipts.printed_at IS NULL THEN 1 END) as unprinted_receipts
            ')
            ->join('payments', 'receipts.payment_id', '=', 'payments.id')
            ->whereBetween('receipts.receipt_date', [$startDate, $endDate])
            ->first();

        $dailyStats = DB::table('receipts')
            ->selectRaw('DATE(receipt_date) as date, COUNT(*) as count, SUM(payments.amount) as amount')
            ->join('payments', 'receipts.payment_id', '=', 'payments.id')
            ->whereBetween('receipts.receipt_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();

        return view('receipts.statistics', compact('stats', 'dailyStats', 'startDate', 'endDate'));
    }
}
