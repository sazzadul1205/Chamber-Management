@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Payment History</h2>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500 mb-1">Total Payments</div>
                <div class="text-2xl font-bold">{{ number_format($summary['total'] ?? 0) }}</div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500 mb-1">Total Amount</div>
                <div class="text-2xl font-bold text-blue-600">
                    ৳{{ number_format($summary['total_amount'] ?? 0, 2) }}
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500 mb-1">Completed</div>
                <div class="text-2xl font-bold text-green-600">{{ $summary['completed'] ?? 0 }}</div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500 mb-1">Pending</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $summary['pending'] ?? 0 }}</div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500 mb-1">Refunded</div>
                <div class="text-2xl font-bold text-red-600">{{ $summary['refunded'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 bg-white p-4 rounded shadow">
            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search payment/invoice/patient" class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-2">
                <select name="patient_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Patients</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->patient_code }} - {{ $patient->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="invoice_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Invoices</option>
                    @foreach ($invoices as $invoice)
                        <option value="{{ $invoice->id }}" {{ request('invoice_id') == $invoice->id ? 'selected' : '' }}>
                            {{ $invoice->invoice_no }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full border rounded px-3 py-2" placeholder="Start Date">
            </div>

            <div class="md:col-span-2">
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full border rounded px-3 py-2" placeholder="End Date">
            </div>

            <div class="md:col-span-2">
                <select name="payment_method" class="w-full border rounded px-3 py-2">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank
                        Transfer</option>
                    <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="mobile_banking" {{ request('payment_method') == 'mobile_banking' ? 'selected' : '' }}>
                        Mobile Banking</option>
                </select>
            </div>

            <div class="md:col-span-4">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
                    Filter
                </button>
            </div>
        </form>

        <!-- Payments Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">Payment No</th>
                        <th class="px-3 py-2 text-left text-sm">Date</th>
                        <th class="px-3 py-2 text-left text-sm">Patient</th>
                        <th class="px-3 py-2 text-left text-sm">Invoice</th>
                        <th class="px-3 py-2 text-left text-sm">Method</th>
                        <th class="px-3 py-2 text-left text-sm">Amount</th>
                        <th class="px-3 py-2 text-left text-sm">Status</th>
                        <th class="px-3 py-2 text-left text-sm">Created By</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <span class="font-medium text-blue-600">
                                    {{ $payment->payment_no }}
                                </span>
                                @if ($payment->reference_no)
                                    <div class="text-xs text-gray-500">
                                        Ref: {{ $payment->reference_no }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm">
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2">
                                <a href="{{ route('backend.patients.show', $payment->patient_id) }}"
                                    class="text-blue-600 hover:underline">
                                    {{ $payment->patient->full_name }}
                                </a>
                                <div class="text-xs text-gray-500">
                                    {{ $payment->patient->patient_code }}
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                @if ($payment->invoice)
                                    <a href="{{ route('invoices.show', $payment->invoice_id) }}"
                                        class="text-green-600 hover:underline">
                                        {{ $payment->invoice->invoice_no }}
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    $methodColors = [
                                        'cash' => 'bg-green-100 text-green-800',
                                        'card' => 'bg-blue-100 text-blue-800',
                                        'bank_transfer' => 'bg-purple-100 text-purple-800',
                                        'cheque' => 'bg-yellow-100 text-yellow-800',
                                        'mobile_banking' => 'bg-pink-100 text-pink-800',
                                        'other' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs rounded {{ $methodColors[$payment->payment_method] ?? 'bg-gray-100' }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <div class="font-bold text-lg text-gray-900">
                                    ৳{{ number_format($payment->amount, 2) }}
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    $statusColors = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'refunded' => 'bg-orange-100 text-orange-800',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs rounded {{ $statusColors[$payment->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-sm">
                                {{ $payment->createdBy->name ?? 'System' }}
                            </td>
                            <td class="px-3 py-2 text-center flex justify-center gap-1">
                                <!-- View -->
                                <a href="{{ route('backend.payments.show', $payment->id) }}"
                                    class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                    title="View Payment">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'B_View',
                                        'class' => 'w-4 h-4',
                                    ])
                                </a>

                                <!-- Receipt -->
                                @if ($payment->receipt)
                                    <a href="{{ route('receipts.show', $payment->receipt->id) }}"
                                        class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs"
                                        title="View Receipt">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$payments" />
    </div>
@endsection
