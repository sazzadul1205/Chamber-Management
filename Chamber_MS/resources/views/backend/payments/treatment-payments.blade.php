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
                        <span class="font-medium">{{ $treatment->doctor->user->name ?? 'N/A' }}</span>
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
                <button onclick="openOverallPaymentModal()"
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
                            @if($sessionBalance > 0)
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
                @if($sessionTotalCost > 0)
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
                            @if($procedureBalance > 0)
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
                @if($procedureTotalCost > 0)
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
                            @if($overallBalance > 0)
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
                @if($overallTotalCost > 0)
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
                    <button onclick="openOverallPaymentModal()"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v8m0 0l3-3m-3 3l-3-3m3-11a9 9 0 110 18 9 9 0 010-18z" />
                        </svg>
                        Make Overall Payment
                    </button>
                    @if($treatment->invoices->isNotEmpty())
                        <a href="{{ route('invoices.show', $treatment->invoices->first()) }}"
                            class="block w-full text-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition">
                            View Invoice
                        </a>
                    @else
                        <button onclick="createInvoice()"
                            class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium">
                            Create Invoice
                        </button>
                    @endif
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item
                            Details</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
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

        <!-- Recent Payments -->
        @php
            $allPayments = collect();
            foreach ($sessions as $session) {
                $allPayments = $allPayments->merge($session['payments']);
            }
            foreach ($procedures as $procedure) {
                $allPayments = $allPayments->merge($procedure['payments']);
            }
            $recentPayments = $allPayments->sortByDesc('payment_date')->take(5);
        @endphp

        @if($recentPayments->count() > 0)
            <div class="bg-white rounded-lg shadow border">
                <div class="px-4 py-3 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Recent Payments</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($recentPayments as $payment)
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

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 px-4">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg border mx-auto">
            <div class="flex justify-between items-center px-4 py-3 border-b">
                <h3 class="text-base font-semibold text-gray-900" id="modalTitle">Record Payment</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-4">
                <form id="paymentForm" action="{{ route('payments.store-treatment-payment') }}" method="POST">
                    @csrf
                    <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
                    <input type="hidden" name="patient_id" value="{{ $treatment->patient_id }}">
                    <input type="hidden" name="payment_for_type" id="paymentForType">
                    <input type="hidden" name="item_id" id="itemId">

                    <!-- Item Info -->
                    <div class="mb-4 bg-blue-50 border border-blue-200 rounded p-3 text-sm" id="itemInfo">
                        <h4 class="font-medium text-blue-900 mb-2" id="itemType">Item Details</h4>
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

                    <!-- Overall Payment Info -->
                    <div class="mb-4 bg-purple-50 border border-purple-200 rounded p-3 text-sm hidden" id="overallInfo">
                        <h4 class="font-medium text-purple-900 mb-2">Overall Payment</h4>
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
                        <input type="number" name="amount" id="paymentAmount" step="0.01" min="0.01"
                            class="w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-gray-500 mt-1">
                            Max: <span id="maxAmount" class="font-medium"></span>
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

                    <!-- Invoice Allocation -->
                    @if($treatment->invoices->isNotEmpty())
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
                        <button type="button" onclick="closePaymentModal()"
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
        // Data from PHP
        const allItems = @json($allItems);
        const sessions = @json($sessions);
        const procedures = @json($procedures);

        let currentTab = 'all';
        let currentItem = null;

        // Initialize table
        function renderTable(items) {
            const tbody = document.getElementById('itemsTableBody');
            const noResults = document.getElementById('noResults');

            if (items.length === 0) {
                tbody.innerHTML = '';
                noResults.classList.remove('hidden');
                return;
            }

            noResults.classList.add('hidden');

            tbody.innerHTML = items.map(item => {
                const typeColor = item.type === 'session' ? 'blue' : 'purple';
                const typeIcon = item.type === 'session' ?
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>' :
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>';

                const statusColors = {
                    'scheduled': 'bg-blue-100 text-blue-800',
                    'completed': 'bg-green-100 text-green-800',
                    'in_progress': 'bg-yellow-100 text-yellow-800',
                    'planned': 'bg-gray-100 text-gray-800'
                };

                const statusText = item.status ?
                    `<span class="px-2 py-1 text-xs rounded ${statusColors[item.status] || 'bg-gray-100 text-gray-800'}">
                        ${item.status.replace('_', ' ')}
                    </span>` : '';

                const progressColor = item.percentage >= 100 ? 'bg-green-500' :
                    item.percentage > 0 ? 'bg-yellow-500' : 'bg-red-500';

                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-${typeColor}-100 flex items-center justify-center">
                                    <span class="text-${typeColor}-600">${typeIcon}</span>
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-900 capitalize">${item.type}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">${item.description}</div>
                            <div class="text-xs text-gray-500">
                                ${item.type === 'session' ? `Session #${item.number}` : `Code: ${item.code}`}
                                • ${new Date(item.date).toLocaleDateString()}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            ${statusText}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">
                                ৳${item.cost.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600">${item.percentage}% paid</span>
                                    <span class="font-medium">৳${item.paid.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full ${progressColor}" style="width: ${Math.min(item.percentage, 100)}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            ${item.balance > 0 ?
                        `<span class="px-2 py-1 text-sm font-medium bg-red-100 text-red-800 rounded">
                                    ৳${item.balance.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                </span>` :
                        `<span class="px-2 py-1 text-sm font-medium bg-green-100 text-green-800 rounded">
                                    Paid
                                </span>`
                    }
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            ${item.balance > 0 ?
                        `<button onclick="openItemPaymentModal(${item.id}, '${item.type}')"
                                    class="px-3 py-1 bg-${typeColor}-600 hover:bg-${typeColor}-700 text-white rounded text-sm font-medium transition">
                                    Pay
                                </button>` :
                        `<span class="text-sm text-gray-500">Fully Paid</span>`
                    }
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Tab switching
        function switchTab(tab) {
            currentTab = tab;

            // Update active tab styling
            ['all', 'sessions', 'procedures'].forEach(t => {
                const tabEl = document.getElementById(`${t}Tab`);
                if (t === tab) {
                    tabEl.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    tabEl.classList.add('border-blue-500', 'text-blue-600');
                } else {
                    tabEl.classList.remove('border-blue-500', 'text-blue-600');
                    tabEl.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                }
            });

            // Filter items
            let items;
            switch (tab) {
                case 'sessions':
                    items = sessions;
                    break;
                case 'procedures':
                    items = procedures;
                    break;
                default:
                    items = allItems;
            }

            renderTable(items);
        }

        // Modal functions
        function openItemPaymentModal(itemId, itemType) {
            currentItem = allItems.find(item => item.id === itemId && item.type === itemType);
            if (!currentItem) return;

            document.getElementById('modalTitle').textContent = `Record ${itemType === 'session' ? 'Session' : 'Procedure'} Payment`;
            document.getElementById('paymentForType').value = itemType;
            document.getElementById('itemId').value = itemId;

            // Show item info, hide overall info
            document.getElementById('itemInfo').classList.remove('hidden');
            document.getElementById('overallInfo').classList.add('hidden');

            // Populate item details
            document.getElementById('itemType').textContent = `${itemType === 'session' ? 'Session' : 'Procedure'} Details`;
            document.getElementById('itemName').textContent = currentItem.description;
            document.getElementById('itemCost').textContent = `৳${currentItem.cost.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
            document.getElementById('itemPaid').textContent = `৳${currentItem.paid.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
            document.getElementById('itemBalance').textContent = `৳${currentItem.balance.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;

            // Set amount field
            const amountInput = document.getElementById('paymentAmount');
            const maxSpan = document.getElementById('maxAmount');
            amountInput.max = currentItem.balance;
            amountInput.value = currentItem.balance > 0 ? currentItem.balance.toFixed(2) : '';
            maxSpan.textContent = `৳${currentItem.balance.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;

            openModal();
        }

        function openOverallPaymentModal() {
            document.getElementById('modalTitle').textContent = 'Record Overall Payment';
            document.getElementById('paymentForType').value = 'overall';
            document.getElementById('itemId').value = '';

            // Show overall info, hide item info
            document.getElementById('itemInfo').classList.add('hidden');
            document.getElementById('overallInfo').classList.remove('hidden');

            // Set amount field
            const amountInput = document.getElementById('paymentAmount');
            const maxSpan = document.getElementById('maxAmount');
            amountInput.max = {{ $overallBalance }};
            amountInput.value = {{ $overallBalance }} > 0 ? {{ $overallBalance }}.toFixed(2) : '';
            maxSpan.textContent = `৳{{ number_format($overallBalance, 2) }}`;

            openModal();
        }

        function openModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Focus amount field
            setTimeout(() => {
                document.getElementById('paymentAmount').focus();
                document.getElementById('paymentAmount').select();
            }, 100);
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('paymentForm').reset();
            currentItem = null;
        }

        // Event listeners
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePaymentModal();
        });

        document.getElementById('paymentModal').addEventListener('click', (e) => {
            if (e.target.id === 'paymentModal') closePaymentModal();
        });

        document.getElementById('paymentAmount').addEventListener('change', function () {
            const max = parseFloat(this.max);
            const value = parseFloat(this.value);
            if (value > max) {
                alert('Amount cannot exceed maximum of ৳' + max.toFixed(2));
                this.value = max.toFixed(2);
            }
        });

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', async function (e) {
            e.preventDefault();

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
                        closePaymentModal();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showNotification(result.message || 'Error recording payment', 'error');
                        submitButton.disabled = false;
                        submitButton.textContent = originalButtonText;
                    }
                } else {
                    showNotification('Payment recorded!', 'success');
                    closePaymentModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Network error. Please try again.', 'error');
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        });

        // Notification function
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

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            switchTab('all');
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
    </style>
@endsection