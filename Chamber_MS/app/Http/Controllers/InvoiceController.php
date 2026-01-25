<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\Appointment;
use App\Models\ProcedureCatalog;
use App\Models\Medicine;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * List all invoices with optional filters and summary
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['patient', 'treatment', 'appointment']);

        // Filters
        if ($request->filled('patient_id')) $query->where('patient_id', $request->patient_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('payment_plan')) $query->where('payment_plan', $request->payment_plan);
        if ($request->filled('start_date')) $query->whereDate('invoice_date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('invoice_date', '<=', $request->end_date);

        // Search by invoice_no or patient name/code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('patient', fn($q2) => $q2->where('full_name', 'like', "%{$search}%")
                        ->orWhere('patient_code', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->latest()->paginate(20);

        // Summary statistics
        $summary = [
            'total' => Invoice::count(),
            'total_amount' => Invoice::sum('total_amount'),
            'paid_amount' => Invoice::sum('paid_amount'),
            'balance_amount' => Invoice::sum('balance_amount'),
            'draft' => Invoice::where('status', 'draft')->count(),
            'sent' => Invoice::where('status', 'sent')->count(),
            'partial' => Invoice::where('status', 'partial')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
        ];

        $patients = Patient::active()->orderBy('full_name')->get();

        return view('invoices.index', compact('invoices', 'summary', 'patients'));
    }

    /**
     * Show invoice creation form
     */
    public function create(Request $request)
    {
        $patients = Patient::active()->orderBy('full_name')->get();
        $treatments = Treatment::active()->latest()->limit(100)->get();
        $appointments = Appointment::completed()->latest()->limit(100)->get();
        $procedures = ProcedureCatalog::active()->orderBy('procedure_name')->get();
        $medicines = Medicine::active()->orderBy('brand_name')->get();
        $inventoryItems = InventoryItem::active()->hasStock()->orderBy('name')->get();

        $preSelected = [
            'patient_id' => $request->patient_id,
            'treatment_id' => $request->treatment_id,
            'appointment_id' => $request->appointment_id
        ];

        return view('invoices.create', compact(
            'patients',
            'treatments',
            'appointments',
            'procedures',
            'medicines',
            'inventoryItems',
            'preSelected'
        ));
    }

    /**
     * Store a new invoice
     */
    public function store(Request $request)
    {
        $request->validate($this->validationRules());

        DB::beginTransaction();
        try {
            // Create invoice
            $invoice = Invoice::create(array_merge($request->only([
                'patient_id',
                'treatment_id',
                'appointment_id',
                'invoice_date',
                'due_date',
                'payment_plan',
                'advance_amount',
                'discount_percent',
                'tax_amount',
                'payment_terms',
                'notes'
            ]), [
                'invoice_no' => Invoice::generateInvoiceNo(),
                'status' => 'draft',
                'created_by' => 1,
                'updated_by' => 1
            ]));

            $subtotal = $this->saveInvoiceItems($invoice, $request->items);

            // Totals
            $invoice->subtotal = $subtotal;
            $invoice->discount_amount = ($invoice->discount_percent / 100) * $subtotal;
            $invoice->total_amount = $subtotal - $invoice->discount_amount + $invoice->tax_amount;

            // Advance payment
            $invoice->paid_amount = $invoice->advance_amount ?? 0;
            $invoice->status = $invoice->paid_amount > 0 ? 'partial' : 'draft';

            $invoice->updateBalance();

            // Create installments if needed
            if ($invoice->payment_plan == 'installment' && $request->has('installments')) {
                $this->createInstallments($invoice, $request->installments);
            }

            DB::commit();
            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Show invoice details
     */
    public function show($id)
    {
        $invoice = Invoice::with([
            'patient',
            'treatment',
            'appointment',
            'items',
            'payments' => fn($q) => $q->orderBy('payment_date', 'desc'),
            'installments' => fn($q) => $q->orderBy('due_date', 'asc')
        ])->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show edit form for draft invoices
     */
    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Only draft invoices can be edited.');
        }

        return $this->loadFormData($invoice);
    }

    /**
     * Update a draft invoice
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Only draft invoices can be edited.');
        }

        $request->validate($this->validationRules());

        DB::beginTransaction();
        try {
            $invoice->update($request->only([
                'patient_id',
                'treatment_id',
                'appointment_id',
                'invoice_date',
                'due_date',
                'payment_plan',
                'advance_amount',
                'discount_percent',
                'tax_amount',
                'payment_terms',
                'notes'
            ]) + ['updated_by' => 1]);

            // Delete old items and save new ones
            $invoice->items()->delete();
            $subtotal = $this->saveInvoiceItems($invoice, $request->items);

            // Update totals
            $invoice->subtotal = $subtotal;
            $invoice->discount_amount = ($invoice->discount_percent / 100) * $subtotal;
            $invoice->total_amount = $subtotal - $invoice->discount_amount + $invoice->tax_amount;

            // Apply advance payment if any
            $invoice->paid_amount = $invoice->advance_amount ?? 0;
            $invoice->status = $invoice->paid_amount > 0 ? 'partial' : 'draft';

            $invoice->updateBalance();

            // Update installments if any
            if ($request->payment_plan == 'installment') {
                $invoice->installments()->delete();
                if ($request->has('installments')) $this->createInstallments($invoice, $request->installments);
            }

            DB::commit();
            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Delete draft invoice
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Only draft invoices can be deleted.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Print invoice
     */
    public function print($id)
    {
        $invoice = Invoice::with(['patient', 'treatment', 'appointment', 'items', 'payments'])
            ->findOrFail($id);

        return view('invoices.print', compact('invoice'));
    }

    /**
     * Send draft invoice to patient
     */
    public function send($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Only draft invoices can be sent.');
        }

        $invoice->status = 'sent';
        $invoice->save();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice sent to patient.');
    }

    /**
     * Cancel invoice if no payment made
     */
    public function cancel($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status == 'cancelled') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Invoice is already cancelled.');
        }

        if ($invoice->paid_amount > 0) {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Cannot cancel invoice with payments. Refund payments first.');
        }

        $invoice->status = 'cancelled';
        $invoice->save();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice cancelled successfully.');
    }

    /**
     * Record a payment for invoice
     */
    public function addPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->balance_amount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque,mobile_banking',
            'reference_no' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255'
        ]);

        $invoice->addPayment($request->amount);

        // Update installment if applicable
        if ($request->has('installment_id')) {
            $installment = $invoice->installments()->find($request->installment_id);
            if ($installment) {
                $installment->amount_paid += $request->amount;
                $installment->status = $installment->amount_paid >= $installment->amount_due ? 'paid' : 'partial';
                $installment->save();
            }
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Overdue invoice report
     */
    public function overdueReport()
    {
        $overdueInvoices = Invoice::with('patient')
            ->where('status', 'overdue')
            ->orWhere(fn($q) => $q->where('due_date', '<', now())->whereIn('status', ['sent', 'partial']))
            ->orderBy('due_date')->get();

        $totalOverdue = $overdueInvoices->sum('balance_amount');

        return view('invoices.reports.overdue', compact('overdueInvoices', 'totalOverdue'));
    }

    /**
     * Get all invoices for a patient
     */
    public function patientInvoices($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $invoices = Invoice::where('patient_id', $patientId)->latest('invoice_date')->paginate(20);

        return view('invoices.patient', compact('patient', 'invoices'));
    }

    /**
     * Helper to create installments
     */
    private function createInstallments($invoice, $installments)
    {
        foreach ($installments as $index => $i) {
            \App\Models\PaymentInstallment::create([
                'invoice_id' => $invoice->id,
                'installment_number' => $index + 1,
                'description' => $i['description'] ?? 'Installment ' . ($index + 1),
                'due_date' => $i['due_date'],
                'amount_due' => $i['amount'],
                'amount_paid' => 0,
                'status' => 'pending',
                'created_by' => 1
            ]);
        }
    }

    /**
     * Get item details for autocomplete
     */
    public function getItemDetails(Request $request)
    {
        $type = $request->type;
        $id = $request->id;
        $item = null;

        switch ($type) {
            case 'procedure':
                $item = ProcedureCatalog::find($id);
                break;
            case 'medicine':
                $item = Medicine::find($id);
                break;
            case 'inventory':
                $item = InventoryItem::with('stock')->find($id);
                break;
        }

        if (!$item) return response()->json(['error' => 'Item not found']);

        $data = ['description' => $item->name ?? $item->procedure_name ?? $item->brand_name, 'unit_price' => 0];

        if ($type == 'procedure') $data['unit_price'] = $item->standard_cost;
        elseif ($type == 'inventory' && $item->stock) $data['unit_price'] = $item->stock->selling_price ?? 0;

        return response()->json($data);
    }

    /**
     * Validation rules for store/update
     */
    private function validationRules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'payment_plan' => 'required|in:full,installment',
            'advance_amount' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:procedure,medicine,inventory,other',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100'
        ];
    }

    /**
     * Save invoice items and return subtotal
     */
    private function saveInvoiceItems(Invoice $invoice, array $items): float
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $total = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
            $tax = ($total * ($item['tax_percent'] ?? 0)) / 100;
            $totalWithTax = $total + $tax;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => $item['item_type'],
                'item_id' => $item['item_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'tax_percent' => $item['tax_percent'] ?? 0,
                'total_amount' => $totalWithTax
            ]);

            $subtotal += $totalWithTax;
        }

        return $subtotal;
    }

    /**
     * Load form data for create/edit views
     */
    private function loadFormData($invoice)
    {
        $patients = Patient::active()->orderBy('full_name')->get();
        $treatments = Treatment::active()->latest()->limit(100)->get();
        $appointments = Appointment::completed()->latest()->limit(100)->get();
        $procedures = ProcedureCatalog::active()->orderBy('procedure_name')->get();
        $medicines = Medicine::active()->orderBy('brand_name')->get();
        $inventoryItems = InventoryItem::active()->hasStock()->orderBy('name')->get();

        return view('invoices.edit', compact(
            'invoice',
            'patients',
            'treatments',
            'appointments',
            'procedures',
            'medicines',
            'inventoryItems'
        ));
    }
}
