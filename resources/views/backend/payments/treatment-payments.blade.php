@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Treatment Payments</h1>
                <div class="flex flex-wrap items-center gap-4 mt-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Treatment:</span>
                        <span class="font-semibold text-blue-700">{{ $treatment->treatment_code }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Patient:</span>
                        <span class="font-semibold text-green-700">{{ $treatment->patient->full_name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Doctor:</span>
                        <span class="font-medium">{{ $treatment->doctor->user->full_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('backend.treatments.show', $treatment) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Treatment
                </a>
                <button id="headerOverallPaymentBtn" onclick="openOverallPaymentModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v8m0 0l3-3m-3 3l-3-3m3-11a9 9 0 110 18 9 9 0 010-18z" />
                    </svg>
                    Overall Payment
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Sessions Summary -->
            <div class="bg-white rounded-lg border p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Sessions</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">
                            ৳{{ number_format($sessionTotalCost, 2) }}
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-green-600">
                                Paid: ৳{{ number_format($sessionTotalPaid, 2) }}
                            </span>
                            @if ($sessionBalance > 0)
                                <span class="text-xs text-red-600">
                                    Due: ৳{{ number_format($sessionBalance, 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                @if ($sessionTotalCost > 0)
                    <div class="mt-3">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full"
                                style="width: {{ min(100, ($sessionTotalPaid / $sessionTotalCost) * 100) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 text-right">
                            {{ round(($sessionTotalPaid / $sessionTotalCost) * 100, 1) }}% paid
                        </div>
                    </div>
                @endif
            </div>

            <!-- Procedures Summary -->
            <div class="bg-white rounded-lg border p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Procedures</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">
                            ৳{{ number_format($procedureTotalCost, 2) }}
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-green-600">
                                Paid: ৳{{ number_format($procedureTotalPaid, 2) }}
                            </span>
                            @if ($procedureBalance > 0)
                                <span class="text-xs text-red-600">
                                    Due: ৳{{ number_format($procedureBalance, 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                </div>
                @if ($procedureTotalCost > 0)
                    <div class="mt-3">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full"
                                style="width: {{ min(100, ($procedureTotalPaid / $procedureTotalCost) * 100) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 text-right">
                            {{ round(($procedureTotalPaid / $procedureTotalCost) * 100, 1) }}% paid
                        </div>
                    </div>
                @endif
            </div>

            <!-- Overall Summary -->
            <div class="bg-white rounded-lg border p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Cost</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">
                            ৳{{ number_format($overallTotalCost, 2) }}
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-green-600">
                                Paid: ৳{{ number_format($overallTotalPaid, 2) }}
                            </span>
                            @if ($overallBalance > 0)
                                <span class="text-xs text-red-600">
                                    Due: ৳{{ number_format($overallBalance, 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                @if ($overallTotalCost > 0)
                    <div class="mt-3">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full"
                                style="width: {{ min(100, ($overallTotalPaid / $overallTotalCost) * 100) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 text-right">
                            {{ round(($overallTotalPaid / $overallTotalCost) * 100, 1) }}% paid
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg border p-4 shadow-sm">
                <p class="text-sm text-gray-500 mb-3">Quick Actions</p>
                <div class="space-y-2">
                    <button id="quickActionOverallPaymentBtn" onclick="openOverallPaymentModal()"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v8m0 0l3-3m-3 3l-3-3m3-11a9 9 0 110 18 9 9 0 010-18z" />
                        </svg>
                        Make Overall Payment
                    </button>
                    <button onclick="window.location.href='{{ route('invoices.treatment-invoice', $treatment) }}'"
                        class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium">
                        View/Download Invoice
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button id="allTab" onclick="switchTab('all')"
                    class="py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                    All Items ({{ count($allItems) }})
                </button>
                <button id="sessionsTab" onclick="switchTab('sessions')"
                    class="py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Sessions ({{ count($sessions) }})
                </button>
                <button id="proceduresTab" onclick="switchTab('procedures')"
                    class="py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Procedures ({{ count($procedures) }})
                </button>
            </nav>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow border">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item
                            Details</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment
                            Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Content populated by JavaScript -->
                </tbody>
            </table>

            <div id="noResults" class="hidden px-4 py-8 text-center text-gray-500">
                No items found for the selected filter.
            </div>
        </div>

        <!-- Recent Payments - Show ALL payments -->
        @php
            $allPayments = collect();

            foreach ($sessions as $session) {
                if (isset($session['payments'])) {
                    $allPayments = $allPayments->merge($session['payments']);
                }
            }

            foreach ($procedures as $procedure) {
                if (isset($procedure['payments'])) {
                    $allPayments = $allPayments->merge($procedure['payments']);
                }
            }

            // Remove ->take(5) to show ALL payments
            $recentPayments = $allPayments->sortByDesc('payment_date'); // Removed ->take(5)
        @endphp

        @if ($recentPayments->count() > 0)
            <div class="bg-white rounded-lg shadow border">
                <div class="px-4 py-3 border-b">
                    <h3 class="text-lg font-medium text-gray-900">All Payments ({{ $recentPayments->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    @foreach ($recentPayments as $payment)
                        <div class="px-4 py-3 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-sm font-bold text-green-600">৳</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $payment->payment_no }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $payment->payment_date->format('M d, Y') }} •
                                        <span class="capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-green-600">৳{{ number_format($payment->amount, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->remarks }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Payment Modal - Update the form section -->
    <div id="overallPaymentModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 px-4">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg border mx-auto">
            <div class="flex justify-between items-center px-4 py-3 border-b">
                <h3 class="text-base font-semibold text-gray-900">Overall Payment</h3>
                <button onclick="closeOverallPaymentModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-4">
                <form id="overallPaymentForm" action="{{ route('payments.store-overall-payment') }}" method="POST">
                    @csrf
                    <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
                    <input type="hidden" name="patient_id" value="{{ $treatment->patient_id }}">

                    <!-- Payment Info -->
                    <div class="mb-4 bg-purple-50 border border-purple-200 rounded p-3 text-sm">
                        <h4 class="font-medium text-purple-900 mb-2">Overall Payment Summary</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <p class="text-gray-500">Total Balance Due</p>
                                <p class="font-medium text-red-600 text-xl">
                                    ৳{{ number_format($overallBalance, 2) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">Sessions Due</p>
                                <p class="font-medium">৳{{ number_format($sessionBalance, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Procedures Due</p>
                                <p class="font-medium">৳{{ number_format($procedureBalance, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Date -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Payment Method *</label>
                        <select name="payment_method" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Method</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Amount (৳) *</label>
                        <!-- Remove the value attribute entirely -->
                        <input type="number" name="amount" id="overallPaymentAmount" step="0.01" min="0.01"
                            max="{{ $overallBalance }}" class="w-full border rounded px-3 py-2" required
                            placeholder="Enter amount">

                        <p class="text-xs text-gray-500 mt-1">
                            Max: <span id="overallMaxAmount" class="font-medium">
                                ৳{{ number_format($overallBalance, 2) }}
                            </span>
                        </p>
                    </div>

                    <!-- Reference -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Reference Number</label>
                        <input type="text" name="reference_no" class="w-full border rounded px-3 py-2">
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Notes</label>
                        <textarea name="remarks" rows="2" class="w-full border rounded px-3 py-2"
                            placeholder="Optional notes about this overall payment..."></textarea>
                    </div>

                    <!-- Invoice Allocation -->
                    @if ($treatment->invoices->isNotEmpty())
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="allocate_to_invoice" value="1" class="mr-2" checked>
                                <span class="text-sm">Allocate to Invoice
                                    #{{ $treatment->invoices->first()->invoice_no }}</span>
                            </label>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" onclick="closeOverallPaymentModal()"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded">
                            Process Overall Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Item Payment Modal (for Session/Procedure) -->
    <div id="itemPaymentModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 px-4">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg border mx-auto">
            <div class="flex justify-between items-center px-4 py-3 border-b">
                <h3 class="text-base font-semibold text-gray-900" id="itemModalTitle">Record Payment</h3>
                <button onclick="closeItemPaymentModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-4">
                <form id="itemPaymentForm" action="{{ route('payments.store-treatment-payment') }}" method="POST">
                    @csrf
                    <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
                    <input type="hidden" name="patient_id" value="{{ $treatment->patient_id }}">
                    <input type="hidden" name="payment_for_type" id="itemPaymentForType">
                    <input type="hidden" name="item_id" id="itemItemId">

                    <!-- Item Info -->
                    <div class="mb-4 bg-blue-50 border border-blue-200 rounded p-3 text-sm">
                        <h4 class="font-medium text-blue-900 mb-2">Item Details</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-gray-500">Item</p>
                                <p class="font-medium" id="itemName"></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Cost</p>
                                <p class="font-medium text-green-600" id="itemCost"></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Paid</p>
                                <p class="font-medium text-blue-600" id="itemPaid"></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Balance</p>
                                <p class="font-medium text-red-600" id="itemBalance"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Date -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Payment Method *</label>
                        <select name="payment_method" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Method</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Amount (৳) *</label>
                        <input type="number" name="amount" id="itemPaymentAmount" step="0.01" min="0.01"
                            class="w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-gray-500 mt-1">
                            Max: <span id="itemMaxAmount" class="font-medium"></span>
                        </p>
                    </div>

                    <!-- Reference -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Reference Number</label>
                        <input type="text" name="reference_no" class="w-full border rounded px-3 py-2">
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Notes</label>
                        <textarea name="remarks" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" onclick="closeItemPaymentModal()"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                            Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        /*************************************************
         * DATA FROM PHP
         *************************************************/
        const allItems = @json($allItems);
        const sessions = @json($sessions);
        const procedures = @json($procedures);

        let currentTab = 'all';

        /*************************************************
         * TAB ELEMENTS
         *************************************************/
        const tabs = {
            all: document.getElementById('allTab'),
            sessions: document.getElementById('sessionsTab'),
            procedures: document.getElementById('proceduresTab'),
        };

        /*************************************************
         * TABLE RENDERING
         *************************************************/
        function renderTable(items) {
            const tbody = document.getElementById('itemsTableBody');
            const noResults = document.getElementById('noResults');

            if (!items || items.length === 0) {
                tbody.innerHTML = '';
                noResults.classList.remove('hidden');
                return;
            }

            noResults.classList.add('hidden');

            tbody.innerHTML = items.map(item => {
                const statusColors = {
                    scheduled: 'bg-blue-100 text-blue-800',
                    completed: 'bg-green-100 text-green-800',
                    in_progress: 'bg-yellow-100 text-yellow-800',
                    planned: 'bg-gray-100 text-gray-800',
                    cancelled: 'bg-red-100 text-red-800'
                };

                const progressColor =
                    item.percentage >= 100 ? 'bg-green-500' :
                        item.percentage > 0 ? 'bg-yellow-500' :
                            'bg-red-500';

                return `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 capitalize font-medium">
                    <span class="inline-flex items-center px-2 py-1 text-xs rounded ${item.type === 'session' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'
                    }">
                        ${item.type}
                    </span>
                </td>

                <td class="px-4 py-3">
                    <div class="font-medium text-gray-900">${item.description}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        ${item.type === 'session'
                        ? `Session #${item.number}`
                        : `Code: ${item.code}`}
                        • ${new Date(item.date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        })}
                    </div>
                </td>

                <td class="px-4 py-3">
                    ${item.status ? `
                                                                    <span class="px-2 py-1 text-xs rounded font-medium ${statusColors[item.status] || 'bg-gray-100 text-gray-800'}">
                                                                        ${item.status.replace('_', ' ')}
                                                                    </span>` : '<span class="text-gray-400">—</span>'}
                </td>

                <td class="px-4 py-3 font-bold text-gray-900">
                    ৳${Number(item.cost).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                </td>

                <td class="px-4 py-3">
                    <div class="text-xs text-gray-600 mb-1">${item.percentage}% paid</div>
                    <div class="w-full bg-gray-200 rounded h-1.5">
                        <div class="h-1.5 rounded ${progressColor}" style="width:${Math.min(item.percentage, 100)}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Paid: ৳${Number(item.paid).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                    </div>
                </td>

                <td class="px-4 py-3">
                    ${item.balance > 0
                        ? `<span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-red-100 text-red-800">
                                                                        ৳${Number(item.balance).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                                                       </span>`
                        : `<span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-green-100 text-green-800">
                                                                        Paid
                                                                       </span>`
                    }
                </td>

                <td class="px-4 py-3">
                    ${item.balance > 0
                        ? `<button
                                                                        onclick="openItemPaymentModal(${item.id}, '${item.type}')"
                                                                        class="px-3 py-1.5 bg-${item.type === 'session' ? 'blue' : 'purple'}-600 hover:bg-${item.type === 'session' ? 'blue' : 'purple'}-700 text-white rounded text-sm font-medium transition">
                                                                        Pay Now
                                                                       </button>`
                        : `<span class="text-gray-400 text-sm">No balance</span>`
                    }
                </td>
            </tr>
            `;
            }).join('');
        }

        /*************************************************
         * TAB SWITCHING
         *************************************************/
        function setActiveTab(tab) {
            Object.values(tabs).forEach(el => {
                el.classList.remove('border-blue-500', 'text-blue-600');
                el.classList.add('border-transparent', 'text-gray-500');
            });

            tabs[tab].classList.remove('border-transparent', 'text-gray-500');
            tabs[tab].classList.add('border-blue-500', 'text-blue-600');
        }

        function switchTab(tab) {
            currentTab = tab;
            setActiveTab(tab);

            if (tab === 'sessions') {
                renderTable(sessions);
            } else if (tab === 'procedures') {
                renderTable(procedures);
            } else {
                renderTable(allItems);
            }
        }

        /*************************************************
         * ITEM PAYMENT MODAL FUNCTIONS (Session/Procedure)
         *************************************************/
        function openItemPaymentModal(itemId, type) {
            // Find the item from the appropriate array
            let item;
            if (type === 'session') {
                item = sessions.find(s => s.id == itemId);
            } else if (type === 'procedure') {
                item = procedures.find(p => p.id == itemId);
            }

            if (!item) {
                console.error('Item not found:', {
                    itemId,
                    type,
                    sessions,
                    procedures
                });
                return;
            }

            const modal = document.getElementById('itemPaymentModal');

            // Show modal
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Set modal title and type
            document.getElementById('itemModalTitle').innerText =
                type === 'session' ? 'Session Payment' : 'Procedure Payment';
            document.getElementById('itemPaymentForType').value = type;
            document.getElementById('itemItemId').value = itemId;

            // Update item info display
            document.getElementById('itemName').textContent = item.description;
            document.getElementById('itemCost').textContent =
                '৳' + Number(item.cost).toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });
            document.getElementById('itemPaid').textContent =
                '৳' + Number(item.paid).toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });
            document.getElementById('itemBalance').textContent =
                '৳' + Number(item.balance).toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });

            // Set max amount and initial value
            const itemBalance = Number(item.balance);
            const amountInput = document.getElementById('itemPaymentAmount');
            const maxSpan = document.getElementById('itemMaxAmount');

            amountInput.max = itemBalance;
            amountInput.value = itemBalance > 0 ? itemBalance.toFixed(2) : '';
            maxSpan.textContent = '৳' + itemBalance.toLocaleString('en-US', {
                minimumFractionDigits: 2
            });

            // Focus amount field
            setTimeout(() => {
                amountInput.focus();
                amountInput.select();
            }, 100);
        }

        function closeItemPaymentModal() {
            document.getElementById('itemPaymentModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('itemPaymentForm').reset();
        }

        /*************************************************
         * OVERALL PAYMENT MODAL FUNCTIONS - FIXED
         *************************************************/
        function openOverallPaymentModal() {
            console.log('Opening overall payment modal...');
            const modal = document.getElementById('overallPaymentModal');

            // Show modal
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Set max amount for overall payment
            const overallBalance = {{ $overallBalance }};
            const amountInput = document.getElementById('overallPaymentAmount');
            const maxSpan = document.getElementById('overallMaxAmount');

            amountInput.max = overallBalance;
            // Set to empty instead of auto-filling with max balance
            amountInput.value = '';
            maxSpan.textContent = '৳' + overallBalance.toLocaleString('en-US', {
                minimumFractionDigits: 2
            });

            // Focus amount field
            setTimeout(() => {
                amountInput.focus();
            }, 100);
        }

        function closeOverallPaymentModal() {
            document.getElementById('overallPaymentModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('overallPaymentForm').reset();
        }

        /*************************************************
         * AMOUNT VALIDATION - SIMPLIFIED
         *************************************************/
        function validateAmount(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            const maxAmount = parseFloat(input.max);
            const enteredAmount = parseFloat(input.value) || 0;

            // Only validate if entered amount exceeds max
            if (enteredAmount > maxAmount) {
                input.value = maxAmount.toFixed(2);
                showNotification(`Amount adjusted to maximum allowed: ৳${maxAmount.toFixed(2)}`, 'warning');
            }
        }

        /*************************************************
         * FORM SUBMISSION HANDLERS
         *************************************************/
        // Item Payment Form (Session/Procedure) - KEEP AS IS
        document.getElementById('itemPaymentForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();

            const amount = parseFloat(document.getElementById('itemPaymentAmount').value);
            const maxAmount = parseFloat(document.getElementById('itemPaymentAmount').max);

            // Validate amount
            if (amount > maxAmount) {
                alert('Payment amount cannot exceed ৳' + maxAmount.toFixed(2));
                return;
            }

            const form = this;
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;

            // Get CSRF token
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                const csrfInput = form.querySelector('input[name="_token"]');
                csrfToken = csrfInput ? csrfInput.value : null;
            }

            if (!csrfToken) {
                showNotification('Security token missing. Please refresh the page.', 'error');
                return;
            }

            // Disable submit button
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const contentType = response.headers.get("content-type");

                if (contentType && contentType.includes("application/json")) {
                    const result = await response.json();

                    if (response.ok) {
                        showNotification('Payment recorded successfully!', 'success');
                        closeItemPaymentModal();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification(result.message || 'Error recording payment', 'error');
                        submitButton.disabled = false;
                        submitButton.textContent = originalButtonText;
                    }
                } else {
                    // Handle non-JSON response (likely redirect)
                    showNotification('Payment recorded!', 'success');
                    closeItemPaymentModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Network error. Please try again.', 'error');
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        });

        // Overall Payment Form - SIMPLIFIED
        document.getElementById('overallPaymentForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();

            const amount = parseFloat(document.getElementById('overallPaymentAmount').value);
            const maxAmount = parseFloat(document.getElementById('overallPaymentAmount').max);

            // Validate amount
            if (amount > maxAmount) {
                alert('Payment amount cannot exceed ৳' + maxAmount.toFixed(2));
                return;
            }

            const form = this;
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;

            // Get CSRF token
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                const csrfInput = form.querySelector('input[name="_token"]');
                csrfToken = csrfInput ? csrfInput.value : null;
            }

            if (!csrfToken) {
                showNotification('Security token missing. Please refresh the page.', 'error');
                return;
            }

            // Disable submit button
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showNotification('Overall payment processed successfully!', 'success');

                    // Show allocation details
                    if (result.allocated_items && result.allocated_items.length > 0) {
                        let allocationDetails = 'Allocation Details:\n';
                        result.allocated_items.forEach((item, index) => {
                            allocationDetails +=
                                `${index + 1}. ${item.type === 'session' ? 'Session' : 'Procedure'}: ${item.type === 'session' ? '#' + item.session_number : item.procedure_name} - ৳${item.amount.toFixed(2)}\n`;
                        });
                        if (result.remaining_credit > 0) {
                            allocationDetails += `\nCredit Balance: ৳${result.remaining_credit.toFixed(2)}`;
                        }
                        setTimeout(() => {
                           
                        }, 500);
                    }

                    closeOverallPaymentModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showNotification(result.message || 'Error processing overall payment', 'error');
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Network error. Please try again.', 'error');
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        });

        /*************************************************
         * NOTIFICATION FUNCTION
         *************************************************/
        function showNotification(message, type = 'info') {
            const existingNotification = document.querySelector('.notification-toast');
            if (existingNotification) {
                existingNotification.remove();
            }

            const notification = document.createElement('div');
            notification.className = `notification-toast fixed top-4 right-4 z-50 px-4 py-3 rounded shadow text-white font-medium ${type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' :
                        'bg-blue-500'
                }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        /*************************************************
         * INITIALIZATION - FIXED
         *************************************************/
        document.addEventListener('DOMContentLoaded', function () {
            // Amount validation for item payment
            const itemAmountInput = document.getElementById('itemPaymentAmount');
            if (itemAmountInput) {
                itemAmountInput.addEventListener('change', function () {
                    validateAmount('itemPaymentAmount');
                });
            }

            // Amount validation for overall payment - SIMPLIFIED
            const overallAmountInput = document.getElementById('overallPaymentAmount');
            if (overallAmountInput) {
                overallAmountInput.addEventListener('change', function () {
                    validateAmount('overallPaymentAmount');
                });
            }

            // Initialize tabs
            tabs.all.addEventListener('click', () => switchTab('all'));
            tabs.sessions.addEventListener('click', () => switchTab('sessions'));
            tabs.procedures.addEventListener('click', () => switchTab('procedures'));

            // Load initial table
            switchTab('all');

            // Close modals on ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeItemPaymentModal();
                    closeOverallPaymentModal();
                }
            });

            // Close modals when clicking outside
            const itemModal = document.getElementById('itemPaymentModal');
            if (itemModal) {
                itemModal.addEventListener('click', (e) => {
                    if (e.target.id === 'itemPaymentModal') {
                        closeItemPaymentModal();
                    }
                });
            }

            const overallModal = document.getElementById('overallPaymentModal');
            if (overallModal) {
                overallModal.addEventListener('click', (e) => {
                    if (e.target.id === 'overallPaymentModal') {
                        closeOverallPaymentModal();
                    }
                });
            }

            // REMOVED: The problematic duplicate event listener block
            // This was causing the modal to open twice
            /*
            document.querySelectorAll('button').forEach(btn => {
                if (btn.textContent.includes('Overall Payment') || btn.onclick?.toString().includes(
                        'openOverallPaymentModal')) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        openOverallPaymentModal();
                    });
                }
            });
            */
        });
    </script>

    <style>
        .notification-toast {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Ensure modal is properly positioned */
        .fixed.inset-0 {
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        /* Prevent body scrolling when modal is open */
        body.overflow-hidden {
            overflow: hidden;
        }

        /* Modal z-index stacking */
        #itemPaymentModal,
        #overallPaymentModal {
            z-index: 9999;
        }
    </style>
@endsection