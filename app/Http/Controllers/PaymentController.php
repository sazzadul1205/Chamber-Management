<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PaymentAllocation;
use App\Models\PaymentInstallment;
use App\Models\Treatment;
use App\Models\TreatmentProcedure;
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

        return view('backend.payments.index', compact('payments', 'summary', 'patients', 'invoices'));
    }

    /*-----------------------------------
    | Refund History
    *-----------------------------------*/
    public function refundHistory(Request $request)
    {
        $query = Payment::with(['invoice', 'patient', 'createdBy'])
            ->where('payment_type', 'refund');

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
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
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                            ->orWhere('patient_code', 'like', "%{$search}%");
                    });
            });
        }

        $refunds = $query->latest('payment_date')->paginate(20);
        $refunds->appends($request->query());

        $summary = [
            'count' => Payment::where('payment_type', 'refund')->count(),
            'amount' => abs((float) Payment::where('payment_type', 'refund')->sum('amount')),
            'period_count' => $refunds->total(),
            'period_amount' => abs((float) (clone $query)->sum('amount')),
        ];

        $patients = Patient::active()->orderBy('full_name')->get();

        return view('backend.payments.refunds', compact('refunds', 'summary', 'patients'));
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

        return view('backend.payments.create', compact('patients', 'invoices', 'preSelected', 'paymentMethods', 'paymentTypes'));
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

            // NEW: Auto-allocate to outstanding sessions/procedures if no installment specified
            if (!$request->installment_id && $request->auto_allocate) {
                $this->autoAllocateToTreatments($payment, $request->amount);
            }

            // Deduct payment from invoice
            DB::commit();

            return redirect()->route('backend.payments.show', $payment->id)->with('success', 'Payment recorded successfully.');
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

        return view('backend.payments.show', compact('payment'));
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

        return view('backend.payments.edit', compact('payment', 'patients', 'invoices', 'installments', 'paymentMethods', 'paymentTypes'));
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

            return redirect()->route('backend.payments.show', $payment->id)->with('success', 'Payment updated successfully.');
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

        return redirect()->route('backend.payments.index')->with('success', 'Payment deleted successfully.');
    }

    /*-----------------------------------
    | Refund Payment
    *-----------------------------------*/
    public function refund(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $payment = Payment::findOrFail($id);
        DB::beginTransaction();
        try {
            $refundPayment = $payment->refund($request->reason);
            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Payment refunded successfully. Refund voucher: ' . $refundPayment->payment_no);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }

    /*-----------------------------------
    | Cancel Payment
    *-----------------------------------*/
    public function cancel(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $payment = Payment::findOrFail($id);
        $payment->cancel($request->reason);

        return redirect()->route('backend.payments.show', $payment->id)->with('success', 'Payment cancelled successfully.');
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

        return redirect()->route('backend.payments.show', $payment->id)
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
        return view('backend.payments.reports.daily', compact('payments', 'summary', 'date'));
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

    /* -----------------------------------
    |Allocate payment to treatments
    *----------------------------------- */
    private function autoAllocateToTreatments(Payment $payment, $amount)
    {
        $invoice = $payment->invoice;
        $patient = $invoice->patient;

        // Get unpaid sessions for this patient's treatments
        $unpaidSessions = TreatmentSession::whereHas('treatment', function ($query) use ($patient) {
            $query->where('patient_id', $patient->id);
        })
            ->whereRaw('cost_for_session > IFNULL((SELECT SUM(amount) FROM payments WHERE for_treatment_session_id = treatment_sessions.id), 0)')
            ->orderBy('scheduled_date')
            ->get();

        $remainingAmount = $amount;

        foreach ($unpaidSessions as $session) {
            if ($remainingAmount <= 0) break;

            $sessionBalance = $session->balance;
            $allocateAmount = min($sessionBalance, $remainingAmount);

            if ($allocateAmount > 0) {
                // Create allocation
                PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'treatment_session_id' => $session->id,
                    'allocated_amount' => $allocateAmount,
                    'allocation_date' => now(),
                    'created_by' => auth()->id(),
                ]);

                // Update session
                $session->addPayment($allocateAmount);

                $remainingAmount -= $allocateAmount;
            }
        }

        // If still have remaining amount, allocate to procedures
        if ($remainingAmount > 0) {
            $unpaidProcedures = TreatmentProcedure::whereHas('treatment', function ($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
                ->where('status', 'completed')
                ->whereRaw('cost > IFNULL((SELECT SUM(allocated_amount) FROM payment_allocations WHERE treatment_procedure_id = treatment_procedures.id), 0)')
                ->get();

            foreach ($unpaidProcedures as $procedure) {
                if ($remainingAmount <= 0) break;

                $procedureBalance = $procedure->cost - ($procedure->paid_amount ?? 0);
                $allocateAmount = min($procedureBalance, $remainingAmount);

                if ($allocateAmount > 0) {
                    PaymentAllocation::create([
                        'payment_id' => $payment->id,
                        'treatment_procedure_id' => $procedure->id,
                        'allocated_amount' => $allocateAmount,
                        'allocation_date' => now(),
                        'created_by' => auth()->id(),
                    ]);

                    $remainingAmount -= $allocateAmount;
                }
            }
        }

        return $amount - $remainingAmount; // Return allocated amount
    }


    /*-----------------------------------
    | Store Session Payment (NEW METHOD)
    *-----------------------------------*/
    public function storeSessionPayment(Request $request)
    {
        $request->validate([
            'for_treatment_session_id' => 'required|exists:treatment_sessions,id',
            'patient_id' => 'required|exists:patients,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque,mobile_banking,other',
            'amount' => 'required|numeric|min:0.01',
            'reference_no' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:500'
        ]);

        // Get the session
        $session = TreatmentSession::findOrFail($request->for_treatment_session_id);

        // Calculate balance
        $sessionPaid = $session->paid_amount; // Now using the paid_amount column
        $sessionBalance = $session->cost_for_session - $sessionPaid;

        // Validate amount doesn't exceed balance
        if ($request->amount > $sessionBalance) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Amount cannot exceed session balance of ৳" . number_format($sessionBalance, 2)
                ], 422);
            }
            return back()->with('error', "Amount cannot exceed session balance of ৳" . number_format($sessionBalance, 2));
        }

        DB::beginTransaction();
        try {
            // Create payment WITHOUT invoice_id (for direct session payments)
            $payment = Payment::create([
                'payment_no' => Payment::generatePaymentNo(),
                'patient_id' => $request->patient_id,
                'for_treatment_session_id' => $request->for_treatment_session_id,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'payment_type' => 'partial',
                'amount' => $request->amount,
                'reference_no' => $request->reference_no,
                'remarks' => $request->remarks,
                'status' => 'completed',
                'created_by' => auth()->id()
            ]);

            // Update the session's paid amount using the model method
            $session->addPayment($request->amount);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment recorded successfully for Session #' . $session->session_number,
                    'payment' => [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'payment_no' => $payment->payment_no
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Payment recorded successfully for Session #' . $session->session_number);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error recording payment: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /*-----------------------------------
    | Store Procedure Payment (NEW METHOD)
    *-----------------------------------*/
    public function storeProcedurePayment(Request $request)
    {
        $request->validate([
            'procedure_id' => 'required|exists:treatment_procedures,id',
            'patient_id' => 'required|exists:patients,id',

            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque,mobile_banking,other',
            'amount' => 'required|numeric|min:0.01',
            'reference_no' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:500'
        ]);

        // Get the procedure
        $procedure = TreatmentProcedure::findOrFail($request->procedure_id);

        // Calculate balance dynamically (same as session)
        $procedurePaid = $procedure->payments()->sum('amount');
        $procedureBalance = max(0, $procedure->cost - $procedurePaid);

        // Validate amount doesn't exceed balance
        if ($request->amount > $procedureBalance) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Amount cannot exceed procedure balance of ৳" . number_format($procedureBalance, 2)
                ], 422);
            }
            return back()->with('error', "Amount cannot exceed procedure balance of ৳" . number_format($procedureBalance, 2));
        }

        DB::beginTransaction();
        try {
            // Create payment using polymorphic relationship
            $payment = Payment::create([
                'payment_no' => Payment::generatePaymentNo(),
                'patient_id' => $request->patient_id,
                'treatment_id' => $request->treatment_id ?? $procedure->treatment_id,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'payment_type' => 'partial',
                'amount' => $request->amount,
                'reference_no' => $request->reference_no,
                'remarks' => $request->remarks,
                'status' => 'completed',
                'payable_type' => 'App\Models\TreatmentProcedure',
                'payable_id' => $procedure->id,
                'for_treatment_session_id' => null,
                'created_by' => auth()->id()
            ]);

            DB::commit(); // NO NEED TO CALL $procedure->addPayment()

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment recorded successfully for Procedure: ' . $procedure->procedure_name,
                    'payment' => [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'payment_no' => $payment->payment_no
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Payment recorded successfully for Procedure: ' . $procedure->procedure_name);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error recording payment: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /*-----------------------------------
    | Show Combined Payments Page for Treatment
    *-----------------------------------*/
    public function treatmentPayments(Treatment $treatment)
    {
        // Eager load all necessary relationships with payments
        $treatment->load([
            'sessions.payments' => function ($query) {
                $query->latest();
            },
            'procedures.payments' => function ($query) {
                $query->latest();
            },
            'patient',
            'invoices.payments',
            'doctor.user' // Add this line to load doctor with user
        ]);

        // Calculate totals
        $sessionTotalCost = $treatment->sessions->sum('cost_for_session');
        $sessionTotalPaid = $treatment->sessions->sum(function ($session) {
            return $session->payments->sum('amount');
        });
        $sessionBalance = $sessionTotalCost - $sessionTotalPaid;

        $procedureTotalCost = $treatment->procedures->sum('cost');
        $procedureTotalPaid = $treatment->procedures->sum(function ($procedure) {
            return $procedure->payments->sum('amount');
        });
        $procedureBalance = $procedureTotalCost - $procedureTotalPaid;

        $overallTotalCost = $sessionTotalCost + $procedureTotalCost;
        $overallTotalPaid = $sessionTotalPaid + $procedureTotalPaid;
        $overallBalance = $overallTotalCost - $overallTotalPaid;

        // Prepare session data for view
        $sessions = $treatment->sessions->map(function ($session) {
            $paid = $session->payments->sum('amount');
            $balance = max(0, $session->cost_for_session - $paid);
            $percentage = $session->cost_for_session > 0 ? round(($paid / $session->cost_for_session) * 100, 2) : 0;

            return [
                'id' => $session->id,
                'type' => 'session',
                'number' => $session->session_number,
                'title' => $session->session_title,
                'description' => "Session #{$session->session_number}: {$session->session_title}",
                'date' => $session->scheduled_date,
                'status' => $session->status,
                'cost' => $session->cost_for_session,
                'paid' => $paid,
                'balance' => $balance,
                'percentage' => $percentage,
                'payments' => $session->payments
            ];
        });

        // Prepare procedure data for view
        $procedures = $treatment->procedures->map(function ($procedure) {
            $paid = $procedure->payments->sum('amount');
            $balance = max(0, $procedure->cost - $paid);
            $percentage = $procedure->cost > 0 ? round(($paid / $procedure->cost) * 100, 2) : 0;

            return [
                'id' => $procedure->id,
                'type' => 'procedure',
                'code' => $procedure->procedure_code,
                'name' => $procedure->procedure_name,
                'description' => $procedure->procedure_name,
                'date' => $procedure->created_at,
                'status' => $procedure->status,
                'cost' => $procedure->cost,
                'paid' => $paid,
                'balance' => $balance,
                'percentage' => $percentage,
                'payments' => $procedure->payments
            ];
        });

        // Combine all items
        $allItems = $sessions->merge($procedures)->sortByDesc('balance')->values();

        return view('backend.payments.treatment-payments', compact(
            'treatment',
            'sessions',
            'procedures',
            'allItems',
            'sessionTotalCost',
            'sessionTotalPaid',
            'sessionBalance',
            'procedureTotalCost',
            'procedureTotalPaid',
            'procedureBalance',
            'overallTotalCost',
            'overallTotalPaid',
            'overallBalance'
        ));
    }

    /*-----------------------------------
    | Store Combined Payment for Treatment
    *-----------------------------------*/
    public function storeTreatmentPayment(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'payment_for_type' => 'required|in:session,procedure,overall',
            'item_id' => 'nullable|integer',
            'patient_id' => 'required|exists:patients,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque,mobile_banking,other',
            'amount' => 'required|numeric|min:0.01',
            'reference_no' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:500',
            'allocate_to_invoice' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $treatment = Treatment::findOrFail($request->treatment_id);

            // Calculate maximum allowed amount based on selection
            $maxAmount = 0;
            $targetItem = null;
            $paymentDetails = [];

            switch ($request->payment_for_type) {
                case 'session':
                    $session = TreatmentSession::findOrFail($request->item_id);
                    $maxAmount = max(0, $session->cost_for_session - $session->payments->sum('amount'));
                    $targetItem = $session;
                    $paymentDetails['session_id'] = $session->id;
                    break;

                case 'procedure':
                    $procedure = TreatmentProcedure::findOrFail($request->item_id);
                    $maxAmount = max(0, $procedure->cost - $procedure->payments->sum('amount'));
                    $targetItem = $procedure;
                    $paymentDetails['procedure_id'] = $procedure->id;
                    break;

                case 'overall':
                    // Calculate total unpaid balance for treatment
                    $sessionBalance = $treatment->sessions->sum(function ($s) {
                        return max(0, $s->cost_for_session - $s->payments->sum('amount'));
                    });
                    $procedureBalance = $treatment->procedures->sum(function ($p) {
                        return max(0, $p->cost - $p->payments->sum('amount'));
                    });
                    $maxAmount = $sessionBalance + $procedureBalance;
                    break;
            }

            // Validate amount
            if ($request->amount > $maxAmount) {
                return back()->withInput()->with(
                    'error',
                    "Amount cannot exceed available balance of ৳" . number_format($maxAmount, 2)
                );
            }

            // Create the payment
            $payment = Payment::create([
                'payment_no' => Payment::generatePaymentNo(),
                'patient_id' => $request->patient_id,
                'treatment_id' => $treatment->id,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'payment_type' => $request->payment_for_type === 'overall' ? 'partial' : 'full',
                'amount' => $request->amount,
                'reference_no' => $request->reference_no,
                'remarks' => $request->remarks,
                'status' => 'completed',
                'created_by' => auth()->id(),
                'for_treatment_session_id' => $paymentDetails['session_id'] ?? null,
                'payable_type' => isset($paymentDetails['procedure_id']) ? 'App\Models\TreatmentProcedure' : null,
                'payable_id' => $paymentDetails['procedure_id'] ?? null,
            ]);

            // Update related models
            if ($request->payment_for_type === 'session' && $targetItem) {
                $targetItem->addPayment($request->amount);
            }

            // For overall payments, allocate to outstanding items
            if ($request->payment_for_type === 'overall') {
                $this->allocateOverallPayment($payment, $request->amount, $treatment);
            }

            // Optionally allocate to invoice
            if ($request->allocate_to_invoice && $treatment->invoices->isNotEmpty()) {
                $invoice = $treatment->invoices->first();
                $payment->update(['invoice_id' => $invoice->id]);
                $invoice->addPayment($request->amount);
            }

            DB::commit();

            return redirect()->route('payments.treatment-payments', $treatment)
                ->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /*-----------------------------------
    | Allocate Overall Payment to Items
    *-----------------------------------*/
    private function allocateOverallPayment(Payment $payment, $amount, Treatment $treatment)
    {
        $remainingAmount = $amount;

        // First allocate to sessions
        $unpaidSessions = $treatment->sessions->filter(function ($session) {
            return $session->cost_for_session > $session->payments->sum('amount');
        })->sortBy('scheduled_date');

        foreach ($unpaidSessions as $session) {
            if ($remainingAmount <= 0) break;

            $sessionBalance = max(0, $session->cost_for_session - $session->payments->sum('amount'));
            $allocateAmount = min($sessionBalance, $remainingAmount);

            if ($allocateAmount > 0) {
                // Create allocation
                PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'treatment_session_id' => $session->id,
                    'allocated_amount' => $allocateAmount,
                    'allocation_date' => now(),
                    'created_by' => auth()->id(),
                ]);

                // Update session
                $session->addPayment($allocateAmount);
                $remainingAmount -= $allocateAmount;
            }
        }

        // Then allocate to procedures
        if ($remainingAmount > 0) {
            $unpaidProcedures = $treatment->procedures->filter(function ($procedure) {
                return $procedure->cost > $procedure->payments->sum('amount');
            })->sortBy('created_at');

            foreach ($unpaidProcedures as $procedure) {
                if ($remainingAmount <= 0) break;

                $procedureBalance = max(0, $procedure->cost - $procedure->payments->sum('amount'));
                $allocateAmount = min($procedureBalance, $remainingAmount);

                if ($allocateAmount > 0) {
                    PaymentAllocation::create([
                        'payment_id' => $payment->id,
                        'treatment_procedure_id' => $procedure->id,
                        'allocated_amount' => $allocateAmount,
                        'allocation_date' => now(),
                        'created_by' => auth()->id(),
                    ]);

                    // Note: Procedure payments are tracked via polymorphic relationship
                    $remainingAmount -= $allocateAmount;
                }
            }
        }

        return $amount - $remainingAmount;
    }

    /*-----------------------------------
    | Store Overall Payment - REUSING EXISTING METHODS
    *-----------------------------------*/
    public function storeOverallPayment(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'patient_id' => 'required|exists:patients,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque,mobile_banking,other',
            'amount' => 'required|numeric|min:0.01',
            'reference_no' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $treatment = Treatment::with(['sessions', 'procedures'])->findOrFail($request->treatment_id);
            $remainingAmount = $request->amount;
            $allocatedItems = [];

            // Get unpaid items
            $unpaidSessions = $this->getUnpaidSessions($treatment);
            $unpaidProcedures = $this->getUnpaidProcedures($treatment);

            // Pay sessions
            foreach ($unpaidSessions as $session) {
                if ($remainingAmount <= 0) break;

                $sessionBalance = $session->cost_for_session - $session->payments()->sum('amount');
                if ($sessionBalance > 0) {
                    $payAmount = min($sessionBalance, $remainingAmount);

                    // Call your existing session payment logic
                    $this->createSessionPayment($session, $payAmount, $request);

                    $allocatedItems[] = [
                        'type' => 'session',
                        'item' => "Session #{$session->session_number}",
                        'amount' => $payAmount
                    ];

                    $remainingAmount -= $payAmount;
                }
            }

            // Pay procedures
            if ($remainingAmount > 0) {
                foreach ($unpaidProcedures as $procedure) {
                    if ($remainingAmount <= 0) break;

                    $procedureBalance = $procedure->cost - $procedure->payments()->sum('amount');
                    if ($procedureBalance > 0) {
                        $payAmount = min($procedureBalance, $remainingAmount);

                        // Call your existing procedure payment logic
                        $this->createProcedurePayment($procedure, $payAmount, $request);

                        $allocatedItems[] = [
                            'type' => 'procedure',
                            'item' => $procedure->procedure_name,
                            'amount' => $payAmount
                        ];

                        $remainingAmount -= $payAmount;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed!',
                'allocated_items' => $allocatedItems,
                'remaining_amount' => $remainingAmount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods
    private function getUnpaidSessions($treatment)
    {
        return $treatment->sessions()
            ->whereRaw('cost_for_session > COALESCE((SELECT SUM(amount) FROM payments WHERE for_treatment_session_id = treatment_sessions.id), 0)')
            ->orderBy('scheduled_date')
            ->get();
    }

    private function getUnpaidProcedures($treatment)
    {
        return $treatment->procedures()
            ->whereRaw('cost > COALESCE((SELECT SUM(amount) FROM payments WHERE payable_type = ? AND payable_id = treatment_procedures.id), 0)', ['App\Models\TreatmentProcedure'])
            ->orderBy('created_at')
            ->get();
    }

    private function createSessionPayment($session, $amount, $request)
    {
        return Payment::create([
            'payment_no' => Payment::generatePaymentNo(),
            'patient_id' => $request->patient_id,
            'for_treatment_session_id' => $session->id,
            'treatment_id' => $session->treatment_id,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'payment_type' => 'partial',
            'amount' => $amount,
            'reference_no' => $request->reference_no,
            'remarks' => $request->remarks . " (Session #{$session->session_number})",
            'status' => 'completed',
            'created_by' => auth()->id()
        ]);
    }

    private function createProcedurePayment($procedure, $amount, $request)
    {
        return Payment::create([
            'payment_no' => Payment::generatePaymentNo(),
            'patient_id' => $request->patient_id,
            'treatment_id' => $procedure->treatment_id,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'payment_type' => 'partial',
            'amount' => $amount,
            'reference_no' => $request->reference_no,
            'remarks' => $request->remarks . " (Procedure: {$procedure->procedure_name})",
            'status' => 'completed',
            'payable_type' => 'App\Models\TreatmentProcedure',
            'payable_id' => $procedure->id,
            'created_by' => auth()->id()
        ]);
    }

    /*-----------------------------------
    | Show Procedure Payments Page
    *-----------------------------------*/
    public function procedurePayments(Request $request)
    {
        $query = TreatmentProcedure::with(['treatment.patient', 'payments']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('procedure_name', 'like', "%{$search}%")
                    ->orWhere('procedure_code', 'like', "%{$search}%")
                    ->orWhereHas('treatment.patient', fn($q2) => $q2->where('full_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('patient_id')) {
            $query->whereHas('treatment', fn($q) => $q->where('patient_id', $request->patient_id));
        }

        if ($request->filled('procedure_status')) {
            $query->where('status', $request->procedure_status);
        }

        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->whereRaw('cost <= (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payable_type = ? AND payable_id = treatment_procedures.id)', ['App\Models\TreatmentProcedure']);
            } elseif ($request->payment_status === 'partial') {
                $query->whereRaw('cost > (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payable_type = ? AND payable_id = treatment_procedures.id) AND (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payable_type = ? AND payable_id = treatment_procedures.id) > 0', ['App\Models\TreatmentProcedure', 'App\Models\TreatmentProcedure']);
            } elseif ($request->payment_status === 'unpaid') {
                $query->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payable_type = ? AND payable_id = treatment_procedures.id) = 0', ['App\Models\TreatmentProcedure']);
            }
        }

        $procedures = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate totals
        $totalProcedures = TreatmentProcedure::count();
        $totalCost = TreatmentProcedure::sum('cost');
        $totalPaid = Payment::where('payable_type', 'App\Models\TreatmentProcedure')->sum('amount');
        $totalBalance = $totalCost - $totalPaid;

        $patients = Patient::active()->orderBy('full_name')->get();

        return view('backend.payments.procedure-payments', compact('procedures', 'totalProcedures', 'totalCost', 'totalPaid', 'totalBalance', 'patients'));
    }

    /*-----------------------------------
| Show Session Payments Page
*-----------------------------------*/
    public function sessionPayments(Request $request)
    {
        $query = TreatmentSession::with(['treatment.patient', 'payments']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('session_number', 'like', "%{$search}%")
                    ->orWhere('session_title', 'like', "%{$search}%")
                    ->orWhereHas('treatment.patient', fn($q2) => $q2->where('full_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('patient_id')) {
            $query->whereHas('treatment', fn($q) => $q->where('patient_id', $request->patient_id));
        }

        if ($request->filled('session_status')) {
            $query->where('status', $request->session_status);
        }

        if ($request->filled('session_date')) {
            $query->whereDate('scheduled_date', $request->session_date);
        }

        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->whereRaw('cost_for_session <= paid_amount');
            } elseif ($request->payment_status === 'partial') {
                $query->whereRaw('cost_for_session > paid_amount AND paid_amount > 0');
            } elseif ($request->payment_status === 'unpaid') {
                $query->where('paid_amount', 0);
            }
        }

        $sessions = $query->orderBy('scheduled_date', 'desc')->paginate(20);

        // Calculate totals
        $totalSessions = TreatmentSession::count();
        $totalCost = TreatmentSession::sum('cost_for_session');
        $totalPaid = TreatmentSession::sum('paid_amount');
        $totalBalance = $totalCost - $totalPaid;

        $patients = Patient::active()->orderBy('full_name')->get();

        return view('backend.payments.session-payments', compact('sessions', 'totalSessions', 'totalCost', 'totalPaid', 'totalBalance', 'patients'));
    }
}
