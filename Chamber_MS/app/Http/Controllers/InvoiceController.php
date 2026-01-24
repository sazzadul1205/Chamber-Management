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
    public function index(Request $request)
    {
        $query = Invoice::with(['patient', 'treatment', 'appointment']);

        // Apply filters
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_plan')) {
            $query->where('payment_plan', $request->payment_plan);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('invoice_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('invoice_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                            ->orWhere('patient_code', 'like', "%{$search}%");
                    });
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

        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();

        return view('invoices.index', compact('invoices', 'summary', 'patients'));
    }

    public function create(Request $request)
    {
        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $treatments = Treatment::where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $appointments = Appointment::where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->limit(100)
            ->get();

        $procedures = ProcedureCatalog::where('status', 'active')->orderBy('procedure_name')->get();
        $medicines = Medicine::where('status', 'active')->orderBy('brand_name')->get();
        $inventoryItems = InventoryItem::where('status', 'active')
            ->whereHas('stock', function ($q) {
                $q->where('selling_price', '>', 0);
            })
            ->orderBy('name')
            ->get();

        // Pre-select if coming from treatment or appointment
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

    public function store(Request $request)
    {
        $request->validate([
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
        ]);

        DB::beginTransaction();

        try {
            // Create invoice
            $invoice = Invoice::create([
                'invoice_no' => Invoice::generateInvoiceNo(),
                'patient_id' => $request->patient_id,
                'treatment_id' => $request->treatment_id,
                'appointment_id' => $request->appointment_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'payment_plan' => $request->payment_plan,
                'advance_amount' => $request->advance_amount ?? 0,
                'discount_percent' => $request->discount_percent ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'payment_terms' => $request->payment_terms,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => 1,
                'updated_by' => 1
            ]);

            // Add invoice items
            $subtotal = 0;
            foreach ($request->items as $item) {
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

            // Calculate totals
            $invoice->subtotal = $subtotal;
            $invoice->discount_amount = ($invoice->discount_percent / 100) * $subtotal;
            $invoice->total_amount = $subtotal - $invoice->discount_amount + $invoice->tax_amount;

            // Apply advance payment if any
            if ($invoice->advance_amount > 0) {
                $invoice->paid_amount = $invoice->advance_amount;
                $invoice->status = 'partial';
            }

            $invoice->updateBalance();

            // Create installments if payment plan is installment
            if ($request->payment_plan == 'installment' && $request->has('installments')) {
                $this->createInstallments($invoice, $request->installments);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with([
            'patient',
            'treatment',
            'appointment',
            'items',
            'payments' => function ($q) {
                $q->orderBy('payment_date', 'desc');
            },
            'installments' => function ($q) {
                $q->orderBy('due_date', 'asc');
            }
        ])->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Only draft invoices can be edited.');
        }

        $patients = Patient::where('status', 'active')->orderBy('full_name')->get();
        $treatments = Treatment::where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $appointments = Appointment::where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->limit(100)
            ->get();

        $procedures = ProcedureCatalog::where('status', 'active')->orderBy('procedure_name')->get();
        $medicines = Medicine::where('status', 'active')->orderBy('brand_name')->get();
        $inventoryItems = InventoryItem::where('status', 'active')
            ->whereHas('stock', function ($q) {
                $q->where('selling_price', '>', 0);
            })
            ->orderBy('name')
            ->get();

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

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status != 'draft') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Only draft invoices can be edited.');
        }

        $request->validate([
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
        ]);

        DB::beginTransaction();

        try {
            // Update invoice
            $invoice->update([
                'patient_id' => $request->patient_id,
                'treatment_id' => $request->treatment_id,
                'appointment_id' => $request->appointment_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'payment_plan' => $request->payment_plan,
                'advance_amount' => $request->advance_amount ?? 0,
                'discount_percent' => $request->discount_percent ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'payment_terms' => $request->payment_terms,
                'notes' => $request->notes,
                'updated_by' => 1
            ]);

            // Delete existing items
            $invoice->items()->delete();

            // Add new invoice items
            $subtotal = 0;
            foreach ($request->items as $item) {
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

            // Calculate totals
            $invoice->subtotal = $subtotal;
            $invoice->discount_amount = ($invoice->discount_percent / 100) * $subtotal;
            $invoice->total_amount = $subtotal - $invoice->discount_amount + $invoice->tax_amount;

            // Apply advance payment if any
            if ($invoice->advance_amount > 0) {
                $invoice->paid_amount = $invoice->advance_amount;
                $invoice->status = 'partial';
            } else {
                $invoice->paid_amount = 0;
                $invoice->status = 'draft';
            }

            $invoice->updateBalance();

            // Update installments if payment plan is installment
            if ($request->payment_plan == 'installment') {
                $invoice->installments()->delete();
                if ($request->has('installments')) {
                    $this->createInstallments($invoice, $request->installments);
                }
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

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

    public function print($id)
    {
        $invoice = Invoice::with([
            'patient',
            'treatment',
            'appointment',
            'items',
            'payments' => function ($q) {
                $q->orderBy('payment_date', 'desc');
            }
        ])->findOrFail($id);

        return view('invoices.print', compact('invoice'));
    }

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

        // Create payment (we'll create Payment model in next package)
        // For now, just update the invoice
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

    public function overdueReport()
    {
        $overdueInvoices = Invoice::with('patient')
            ->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('due_date', '<', now())
                    ->whereIn('status', ['sent', 'partial']);
            })
            ->orderBy('due_date', 'asc')
            ->get();

        $totalOverdue = $overdueInvoices->sum('balance_amount');

        return view('invoices.reports.overdue', compact('overdueInvoices', 'totalOverdue'));
    }

    public function patientInvoices($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $invoices = Invoice::where('patient_id', $patientId)
            ->orderBy('invoice_date', 'desc')
            ->paginate(20);

        return view('invoices.patient', compact('patient', 'invoices'));
    }

    // Helper method to create installments
    private function createInstallments($invoice, $installments)
    {
        foreach ($installments as $index => $installment) {
            \App\Models\PaymentInstallment::create([
                'invoice_id' => $invoice->id,
                'installment_number' => $index + 1,
                'description' => $installment['description'] ?? "Installment " . ($index + 1),
                'due_date' => $installment['due_date'],
                'amount_due' => $installment['amount'],
                'amount_paid' => 0,
                'status' => 'pending',
                'created_by' => 1
            ]);
        }
    }

    // Get item details for autocomplete
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

        if (!$item) {
            return response()->json(['error' => 'Item not found']);
        }

        $data = [
            'description' => $item->name ?? $item->procedure_name ?? $item->brand_name,
            'unit_price' => 0
        ];

        if ($type == 'procedure') {
            $data['unit_price'] = $item->standard_cost;
        } elseif ($type == 'medicine') {
            $data['unit_price'] = 0; // Medicines might not have fixed price
        } elseif ($type == 'inventory' && $item->stock) {
            $data['unit_price'] = $item->stock->selling_price ?? 0;
        }

        return response()->json($data);
    }
}
