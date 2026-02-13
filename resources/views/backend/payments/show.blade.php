@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold">Payment Details</h2>
                <div class="text-gray-600 mt-1">
                    Payment #{{ $payment->payment_no }}
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-3 md:mt-0">
                @if ($payment->is_refundable)
                    <!-- Print Receipt -->


                    <!-- Refund Payment -->
                    <button onclick="openRefundModal()"
                        class="flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-md text-sm font-medium transition">
                        @include('partials.sidebar-icon', ['name' => 'B_Undo', 'class' => 'w-4 h-4'])
                        Refund Payment
                    </button>
                @endif

                <!-- Edit Payment -->
                <a href="{{ route('backend.payments.edit', $payment->id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Edit', 'class' => 'w-4 h-4'])
                    Edit
                </a>

                <!-- Back to List -->
                <a href="{{ route('backend.payments.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Back', 'class' => 'w-4 h-4'])
                    Back to List
                </a>
            </div>
        </div>

        <!-- Status Banner -->
        <div
            class="p-4 rounded-md {{ $payment->status == 'completed'
                ? 'bg-green-50 border border-green-200'
                : ($payment->status == 'cancelled'
                    ? 'bg-red-50 border border-red-200'
                    : ($payment->status == 'refunded'
                        ? 'bg-orange-50 border border-orange-200'
                        : 'bg-yellow-50 border border-yellow-200')) }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @php
                        $statusIcons = [
                            'completed' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'pending' => 'M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            'cancelled' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'refunded' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
                        ];
                    @endphp
                    <svg class="w-6 h-6 {{ $payment->status == 'completed'
                        ? 'text-green-600'
                        : ($payment->status == 'cancelled'
                            ? 'text-red-600'
                            : ($payment->status == 'refunded'
                                ? 'text-orange-600'
                                : 'text-yellow-600')) }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="{{ $statusIcons[$payment->status] ?? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}" />
                    </svg>
                    <div>
                        <h3
                            class="text-lg font-semibold {{ $payment->status == 'completed'
                                ? 'text-green-800'
                                : ($payment->status == 'cancelled'
                                    ? 'text-red-800'
                                    : ($payment->status == 'refunded'
                                        ? 'text-orange-800'
                                        : 'text-yellow-800')) }}">
                            Payment {{ ucfirst($payment->status) }}
                        </h3>
                        <p
                            class="text-sm {{ $payment->status == 'completed'
                                ? 'text-green-700'
                                : ($payment->status == 'cancelled'
                                    ? 'text-red-700'
                                    : ($payment->status == 'refunded'
                                        ? 'text-orange-700'
                                        : 'text-yellow-700')) }}">
                            @if ($payment->status == 'completed')
                                Payment was successfully processed on {{ $payment->payment_date->format('F d, Y') }}
                            @elseif($payment->status == 'cancelled')
                                Payment was cancelled on {{ $payment->updated_at->format('F d, Y') }}
                            @elseif($payment->status == 'refunded')
                                Payment was refunded on {{ $payment->updated_at->format('F d, Y') }}
                            @else
                                Payment is pending approval
                            @endif
                        </p>
                    </div>
                </div>

                @if ($payment->status != 'cancelled' && $payment->status != 'refunded')
                    <button onclick="openCancelModal()"
                        class="px-4 py-2 border border-red-300 text-red-700 hover:bg-red-50 rounded-md text-sm font-medium">
                        Cancel Payment
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Payment Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Payment Information Card -->
                <div class="bg-white rounded shadow">
                    <div class="border-b px-6 py-4">
                        <h3 class="font-medium">Payment Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Payment Details -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Payment Number</label>
                                    <div class="text-lg font-semibold text-blue-600">{{ $payment->payment_no }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Payment Date</label>
                                    <div class="font-medium">{{ $payment->payment_date->format('F d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $payment->payment_date->format('h:i A') }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Payment Method</label>
                                    <div class="flex items-center gap-2">
                                        @php
                                            $methodIcons = [
                                                'cash' =>
                                                    'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                                'card' =>
                                                    'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                                                'bank_transfer' =>
                                                    'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z',
                                                'cheque' =>
                                                    'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                                'mobile_banking' =>
                                                    'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
                                                'other' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                            ];
                                        @endphp
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $methodIcons[$payment->payment_method] ?? $methodIcons['other'] }}" />
                                        </svg>
                                        <span class="px-3 py-1 bg-gray-100 rounded-full text-sm font-medium">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </div>

                                    @if ($payment->bank_name)
                                        <div class="text-sm text-gray-600 mt-1">
                                            Bank: {{ $payment->bank_name }}
                                        </div>
                                    @endif

                                    @if ($payment->card_last_four)
                                        <div class="text-sm text-gray-600 mt-1">
                                            Card: **** {{ $payment->card_last_four }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Amount & Type -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Amount</label>
                                    <div class="text-3xl font-bold text-gray-900">
                                        ৳{{ number_format($payment->amount, 2) }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Payment Type</label>
                                    <span
                                        class="px-3 py-1 rounded-full text-sm font-medium
                                          {{ $payment->payment_type == 'full'
                                              ? 'bg-green-100 text-green-800'
                                              : ($payment->payment_type == 'partial'
                                                  ? 'bg-yellow-100 text-yellow-800'
                                                  : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($payment->payment_type) }} Payment
                                    </span>
                                </div>

                                @if ($payment->is_advance)
                                    <div>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                            Advance Payment
                                        </span>
                                    </div>
                                @endif

                                @if ($payment->reference_no)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Reference Number</label>
                                        <div class="font-mono text-gray-800">{{ $payment->reference_no }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($payment->remarks)
                            <div class="mt-6 pt-6 border-t">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Remarks</label>
                                <div class="bg-gray-50 p-4 rounded">
                                    {{ $payment->remarks }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Information Card -->
                <div class="bg-white rounded shadow">
                    <div class="border-b px-6 py-4">
                        <h3 class="font-medium">Related Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Patient Information -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <h4 class="font-medium">Patient Information</h4>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-500 mb-1">Patient Name</label>
                                    <a href="{{ route('backend.patients.show', $payment->patient_id) }}"
                                        class="text-blue-600 hover:underline font-medium">
                                        {{ $payment->patient->full_name }}
                                    </a>
                                    <div class="text-sm text-gray-500">
                                        {{ $payment->patient->patient_code }}
                                    </div>
                                </div>

                                @if ($payment->patient->phone)
                                    <div>
                                        <label class="block text-sm text-gray-500 mb-1">Contact</label>
                                        <div class="font-medium">{{ $payment->patient->phone }}</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Invoice Information -->
                            @if ($payment->invoice)
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h4 class="font-medium">Invoice Information</h4>
                                    </div>

                                    <div>
                                        <label class="block text-sm text-gray-500 mb-1">Invoice Number</label>
                                        <a href="{{ route('invoices.show', $payment->invoice_id) }}"
                                            class="text-green-600 hover:underline font-medium">
                                            {{ $payment->invoice->invoice_no }}
                                        </a>
                                    </div>

                                    <div>
                                        <label class="block text-sm text-gray-500 mb-1">Invoice Amount</label>
                                        <div class="font-medium">
                                            ৳{{ number_format($payment->invoice->total_amount, 2) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Balance: ৳{{ number_format($payment->invoice->balance_amount, 2) }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm text-gray-500 mb-1">Invoice Status</label>
                                        <span
                                            class="px-2 py-1 text-xs rounded 
                                                                      {{ $payment->invoice->status == 'paid'
                                                                          ? 'bg-green-100 text-green-800'
                                                                          : ($payment->invoice->status == 'partial'
                                                                              ? 'bg-yellow-100 text-yellow-800'
                                                                              : ($payment->invoice->status == 'overdue'
                                                                                  ? 'bg-red-100 text-red-800'
                                                                                  : 'bg-blue-100 text-blue-800')) }}">
                                            {{ ucfirst($payment->invoice->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Session/Procedure Information -->
                            @if ($payment->treatmentSession || $payment->payable)
                                <div class="space-y-3 md:col-span-2">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <h4 class="font-medium">Treatment Information</h4>
                                    </div>

                                    @if ($payment->treatmentSession)
                                        <div>
                                            <label class="block text-sm text-gray-500 mb-1">Treatment Session</label>
                                            <div class="font-medium">{{ $payment->treatmentSession->session_title }}</div>
                                            <div class="text-sm text-gray-500">
                                                Session #{{ $payment->treatmentSession->session_number }} •
                                                {{ $payment->treatmentSession->scheduled_date->format('d M Y') }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($payment->payable && $payment->payable_type == 'App\\Models\\TreatmentProcedure')
                                        <div>
                                            <label class="block text-sm text-gray-500 mb-1">Treatment Procedure</label>
                                            <div class="font-medium">{{ $payment->payable->procedure_name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $payment->payable->procedure_code }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Allocation History -->
                @if ($payment->allocations && $payment->allocations->count() > 0)
                    <div class="bg-white rounded shadow">
                        <div class="border-b px-6 py-4">
                            <h3 class="font-medium">Allocation History</h3>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Allocated To
                                            </th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Amount</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray500">Created By
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($payment->allocations as $allocation)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm">
                                                    {{ $allocation->allocation_date->format('d/m/Y') }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    @if ($allocation->treatmentSession)
                                                        <div class="font-medium">Session
                                                            #{{ $allocation->treatmentSession->session_number }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $allocation->treatmentSession->session_title }}</div>
                                                    @elseif($allocation->treatmentProcedure)
                                                        <div class="font-medium">
                                                            {{ $allocation->treatmentProcedure->procedure_name }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $allocation->treatmentProcedure->procedure_code }}</div>
                                                    @elseif($allocation->installment)
                                                        <div class="font-medium">Installment</div>
                                                        <div class="text-xs text-gray-500">Due:
                                                            {{ $allocation->installment->due_date->format('d/m/Y') }}</div>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 font-medium">
                                                    ৳{{ number_format($allocation->allocated_amount, 2) }}
                                                </td>
                                                <td class="px-4 py-2 text-sm">
                                                    {{ $allocation->createdBy->name ?? 'System' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="bg-gray-50">
                                            <td colspan="2" class="px-4 py-3 text-right font-medium">
                                                Total Allocated:
                                            </td>
                                            <td class="px-4 py-3 font-bold">
                                                ৳{{ number_format($payment->allocations->sum('allocated_amount'), 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Actions & History -->
            <div class="space-y-6">
                <!-- Payment Actions -->
                <div class="bg-white rounded shadow">
                    <div class="border-b px-6 py-4">
                        <h3 class="font-medium">Payment Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <!-- View Receipt -->
                        @if ($payment->receipt)
                            <a href="{{ route('receipts.show', $payment->receipt->id) }}"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition">
                                @include('partials.sidebar-icon', [
                                    'name' => 'B_View',
                                    'class' => 'w-4 h-4',
                                ])
                                View Receipt
                            </a>
                        @endif

                        <!-- Allocate to Installment -->
                        @if ($payment->status == 'completed' && $payment->invoice && !$payment->installment_id)
                            <button onclick="openAllocateModal()"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                                @include('partials.sidebar-icon', [
                                    'name' => 'B_Link',
                                    'class' => 'w-4 h-4',
                                ])
                                Allocate to Installment
                            </button>
                        @endif

                        <!-- Back to Payments -->
                        <a href="{{ route('backend.payments.index') }}"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-md text-sm font-medium transition">
                            @include('partials.sidebar-icon', ['name' => 'B_List', 'class' => 'w-4 h-4'])
                            All Payments
                        </a>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="bg-white rounded shadow">
                    <div class="border-b px-6 py-4">
                        <h3 class="font-medium">Payment History</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Created -->
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 mt-2 bg-green-500 rounded-full"></div>
                                <div>
                                    <div class="font-medium">Payment Created</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $payment->created_at->format('F d, Y h:i A') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        By: {{ $payment->createdBy->name ?? 'System' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Updated -->
                            @if ($payment->updated_at != $payment->created_at)
                                <div class="flex items-start gap-3">
                                    <div class="w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                                    <div>
                                        <div class="font-medium">Last Updated</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $payment->updated_at->format('F d, Y h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Status Changes -->
                            @if ($payment->status == 'cancelled')
                                <div class="flex items-start gap-3">
                                    <div class="w-2 h-2 mt-2 bg-red-500 rounded-full"></div>
                                    <div>
                                        <div class="font-medium">Payment Cancelled</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $payment->updated_at->format('F d, Y h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($payment->status == 'refunded')
                                <div class="flex items-start gap-3">
                                    <div class="w-2 h-2 mt-2 bg-orange-500 rounded-full"></div>
                                    <div>
                                        <div class="font-medium">Payment Refunded</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $payment->updated_at->format('F d, Y h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white rounded shadow">
                    <div class="border-b px-6 py-4">
                        <h3 class="font-medium">System Information</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Payment ID</label>
                            <div class="font-mono text-sm">{{ $payment->id }}</div>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Created</label>
                            <div class="text-sm">{{ $payment->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-500 mb-1">Last Updated</label>
                            <div class="text-sm">{{ $payment->updated_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    <div id="refundModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded shadow-lg w-full max-w-md mx-4">
            <div class="border-b px-6 py-4">
                <h3 class="text-lg font-medium">Refund Payment</h3>
            </div>

            <form method="POST" action="{{ route('backend.payments.refund', $payment->id) }}" class="p-6">
                @csrf
                <div class="mb-4">
                    <div class="text-center mb-6">
                        <div class="text-2xl font-bold text-red-600 mb-2">
                            ৳{{ number_format($payment->amount, 2) }}
                        </div>
                        <p class="text-gray-600">Are you sure you want to refund this payment?</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Reason for Refund *</label>
                    <textarea name="reason" rows="3" class="w-full border rounded px-3 py-2"
                        placeholder="Enter reason for refund..." required></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeRefundModal()"
                        class="px-4 py-2 border rounded text-sm hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                        Confirm Refund
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded shadow-lg w-full max-w-md mx-4">
            <div class="border-b px-6 py-4">
                <h3 class="text-lg font-medium">Cancel Payment</h3>
            </div>

            {{-- <form method="POST" action="{{ route('backend.payments.cancel', $payment->id) }}" class="p-6">
                @csrf
                <div class="mb-4">
                    <div class="text-center mb-6">
                        <div class="text-2xl font-bold text-red-600 mb-2">
                            ৳{{ number_format($payment->amount, 2) }}
                        </div>
                        <p class="text-gray-600">Are you sure you want to cancel this payment?</p>
                        <p class="text-sm text-red-500 mt-2">This action cannot be undone.</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Reason for Cancellation *</label>
                    <textarea name="reason" rows="3" 
                              class="w-full border rounded px-3 py-2" 
                              placeholder="Enter reason for cancellation..." required></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeCancelModal()"
                            class="px-4 py-2 border rounded text-sm hover:bg-gray-50">
                        Keep Payment
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                        Cancel Payment
                    </button>
                </div>
            </form> --}}
        </div>
    </div>

    <!-- Allocate Modal -->
    <div id="allocateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded shadow-lg w-full max-w-md mx-4">
            <div class="border-b px-6 py-4">
                <h3 class="text-lg font-medium">Allocate to Installment</h3>
            </div>

            {{-- <form method="POST" action="{{ route('backend.payments.allocate', $payment->id) }}" class="p-6">
                @csrf
                <div class="mb-4">
                    <div class="mb-2">
                        <label class="block text-sm font-medium mb-1">Payment Amount</label>
                        <div class="text-lg font-bold">৳{{ number_format($payment->amount, 2) }}</div>
                    </div>

                    <label class="block text-sm font-medium mb-1">Select Installment *</label>
                    <select name="installment_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Select Installment --</option>
                        @if ($payment->invoice)
                            @foreach ($payment->invoice->installments as $installment)
                                @if ($installment->balance > 0)
                                    <option value="{{ $installment->id }}">
                                        Installment {{ $installment->installment_number }} - 
                                        Due: {{ $installment->due_date->format('d/m/Y') }} - 
                                        Balance: ৳{{ number_format($installment->balance, 2) }}
                                    </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Allocation Amount *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">৳</span>
                        <input type="number" name="amount" 
                               class="w-full pl-8 border rounded px-3 py-2" 
                               value="{{ number_format($payment->amount, 2) }}"
                               step="0.01" min="0.01" max="{{ $payment->amount }}" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeAllocateModal()"
                            class="px-4 py-2 border rounded text-sm hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                        Allocate
                    </button>
                </div>
            </form> --}}
        </div>
    </div>

    <script>
        function openRefundModal() {
            document.getElementById('refundModal').classList.remove('hidden');
        }

        function closeRefundModal() {
            document.getElementById('refundModal').classList.add('hidden');
        }

        function openCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        function openAllocateModal() {
            document.getElementById('allocateModal').classList.remove('hidden');
        }

        function closeAllocateModal() {
            document.getElementById('allocateModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        [document.getElementById('refundModal'),
            document.getElementById('cancelModal'),
            document.getElementById('allocateModal')
        ].forEach(modal => {
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        if (modal.id === 'refundModal') closeRefundModal();
                        if (modal.id === 'cancelModal') closeCancelModal();
                        if (modal.id === 'allocateModal') closeAllocateModal();
                    }
                });
            }
        });
    </script>
@endsection
