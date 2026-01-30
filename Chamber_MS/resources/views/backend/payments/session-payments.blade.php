    @extends('backend.layout.structure')

    @section('content')
        <div class="space-y-6">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h2 class="text-2xl font-semibold mb-1">Session Payments</h2>
                    <div class="text-sm text-gray-600 flex flex-wrap gap-4">
                        <div class="flex items-center gap-1">
                            <span class="font-medium">Treatment:</span>
                            <span class="font-semibold text-blue-700">{{ $treatment->treatment_code }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="font-medium">Patient:</span>
                            <span class="font-semibold text-green-700">{{ $treatment->patient->full_name }}</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('backend.treatments.show', $treatment) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Treatment
                </a>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-md border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Sessions</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $treatment->sessions->count() }}</p>
                        </div>
                        <div class="p-2 bg-blue-100 rounded">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-md border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Sessions Cost</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">
                                ৳{{ number_format($treatment->sessions->sum('cost_for_session'), 2) }}
                            </p>
                        </div>
                        <div class="p-2 bg-green-100 rounded">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-md border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Paid</p>
                            <p class="text-2xl font-bold text-blue-600 mt-1">
                                ৳{{ number_format($treatment->sessions->sum(fn($s) => $s->payments->sum('amount')), 2) }}
                            </p>
                        </div>
                        <div class="p-2 bg-blue-100 rounded">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-md border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Balance</p>
                            <p class="text-2xl font-bold text-red-600 mt-1">
                                ৳{{ number_format($treatment->sessions->sum(fn($s) => $s->cost_for_session - $s->payments->sum('amount')), 2) }}
                            </p>
                        </div>
                        <div class="p-2 bg-red-100 rounded">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessions Table -->
            <div class="overflow-x-auto bg-white rounded shadow border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-3 py-2 text-left text-sm">#</th>
                            <th class="px-3 py-2 text-left text-sm">Session Details</th>
                            <th class="px-3 py-2 text-left text-sm">Date & Status</th>
                            <th class="px-3 py-2 text-left text-sm">Cost</th>
                            <th class="px-3 py-2 text-left text-sm">Payment Details</th>
                            <th class="px-3 py-2 text-left text-sm">Balance</th>
                            <th class="px-3 py-2 text-center text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($treatment->sessions as $session)
                            @php
                                $sessionPaid = $session->payments->sum('amount');
                                $sessionBalance = max(0, $session->cost_for_session - $sessionPaid);
                                $paymentPercentage =
                                    $session->cost_for_session > 0
                                        ? round(($sessionPaid / $session->cost_for_session) * 100, 2)
                                        : 0;

                                // Define status colors
                                $statusColors = [
                                    'scheduled' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                                    'in_progress' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                                    'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                    'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                                    'postponed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
                                ];
                                $statusColor = $statusColors[$session->status] ?? $statusColors['scheduled'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <div class="h-8 w-8 bg-blue-100 rounded flex items-center justify-center">
                                        <span class="text-sm font-bold text-blue-700">{{ $session->session_number }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="text-sm font-medium text-gray-900">{{ $session->session_title }}</div>
                                    <div class="text-xs text-gray-500">
                                        Duration: {{ $session->duration_planned }} minutes
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="space-y-1">
                                        <div class="text-sm text-gray-600">
                                            {{ $session->scheduled_date->format('d/m/Y') }}
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColor['bg'] }} {{ $statusColor['text'] }}">
                                            {{ $session->status_text }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    {{-- Progress --}}
                                    @php
                                        $progressColor = match (true) {
                                            $paymentPercentage >= 100 => 'bg-green-500',
                                            $paymentPercentage > 0 => 'bg-yellow-500',
                                            default => 'bg-red-500',
                                        };
                                    @endphp

                                    {{-- Progress Bar --}}
                                    <div class="space-y-1">
                                        <div class="text-sm font-bold text-gray-900">
                                            ৳{{ number_format($session->cost_for_session, 2) }}
                                        </div>

                                        @if ($session->cost_for_session > 0)
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="h-1.5 rounded-full {{ $progressColor }}"
                                                    style="width: {{ min($paymentPercentage, 100) }}%">
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $paymentPercentage }}% paid</div>
                                        @endif
                                    </div>

                                </td>
                                <td class="px-3 py-2">
                                    @if ($session->payments->count() > 0)
                                        <div class="space-y-1">
                                            @php
                                                $paymentMethodClasses = [
                                                    'cash' => 'bg-green-100 text-green-800',
                                                    'card' => 'bg-blue-100 text-blue-800',
                                                    'bank_transfer' => 'bg-indigo-100 text-indigo-800',
                                                    'mobile_banking' => 'bg-purple-100 text-purple-800',
                                                    'cheque' => 'bg-yellow-100 text-yellow-800',
                                                ];
                                            @endphp


                                            <div class="text-sm">
                                                <span class="font-medium">৳{{ number_format($sessionPaid, 2) }}</span>
                                                <span class="text-gray-600"> ({{ $session->payments->count() }})</span>
                                            </div>

                                            @foreach ($session->payments as $payment)
                                                <div class="text-xs text-gray-500">
                                                    {{ $payment->payment_date->format('d/m') }}

                                                    <span
                                                        class="ml-1 px-1 py-0.5 text-xs rounded {{ $paymentMethodClasses[$payment->payment_method] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                                    </span>

                                                    <span class="font-medium">
                                                        ৳{{ number_format($payment->amount, 2) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-sm text-red-600">No payments</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if ($sessionBalance > 0)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium bg-red-100 text-red-800">
                                            ৳{{ number_format($sessionBalance, 2) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium bg-green-100 text-green-800">
                                            Paid
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center gap-1">
                                        @if ($sessionBalance > 0)
                                            <button type="button"
                                                class="flex items-center justify-center gap-2 px-3 py-2 min-w-[80px] bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-medium transition"
                                                onclick="openPaymentModal({{ $session->id }})" title="Record Payment">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Pay',
                                                    'class' => 'w-4 h-4 text-white',
                                                ])
                                                Pay
                                            </button>
                                        @endif

                                        <a href="{{ route('backend.treatment-sessions.show', $session) }}"
                                            class="flex items-center justify-center gap-2 px-3 py-2 min-w-[80px] bg-gray-500 hover:bg-gray-600 text-white rounded text-xs font-medium transition"
                                            title="View Session">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_View',
                                                'class' => 'w-4 h-4 text-white',
                                            ])
                                            View
                                        </a>
                                    </div>

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    No sessions found.
                                    <a href="{{ route('backend.treatment-sessions.create', ['treatment_id' => $treatment->id]) }}"
                                        class="text-blue-600 hover:underline ml-1">
                                        Add first session
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Modal -->
        <div id="paymentModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 px-4">
            <div class="bg-white w-full max-w-lg rounded shadow border mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center px-4 py-3 border-b">
                    <h3 class="text-base font-semibold text-gray-900">Record Payment</h3>
                    <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-4">
                    <form id="paymentForm" action="{{ route('backend.payments.store-session') }}" method="POST">
                        @csrf
                        <input type="hidden" name="for_treatment_session_id" id="sessionId">
                        <input type="hidden" name="patient_id" value="{{ $treatment->patient_id }}">

                        <!-- Session Info -->
                        <div class="mb-4 bg-blue-50 border border-blue-200 rounded p-3 text-sm">
                            <h4 class="font-medium text-blue-900 mb-2">Session Details</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-gray-500">Session</p>
                                    <p class="font-medium" id="sessionTitle"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Cost</p>
                                    <p class="font-medium text-green-600" id="sessionCost"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Paid</p>
                                    <p class="font-medium text-blue-600" id="paidAmount"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Balance</p>
                                    <p class="font-medium text-red-600" id="balanceAmount"></p>
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
            // Session data array
            const sessionData = {
                @foreach ($treatment->sessions as $session)
                    {{ $session->id }}: {
                        title: "{{ $session->session_title }}",
                        cost: {{ $session->cost_for_session }},
                        paid: {{ $session->payments->sum('amount') }},
                        balance: {{ max(0, $session->cost_for_session - $session->payments->sum('amount')) }}
                    },
                @endforeach
            };

            let currentSessionId = null;

            function openPaymentModal(sessionId) {
                currentSessionId = sessionId;
                const data = sessionData[sessionId];
                if (!data) return;

                // Set form values
                document.getElementById('sessionId').value = sessionId;
                document.getElementById('sessionTitle').textContent = data.title;
                document.getElementById('sessionCost').textContent = '৳' + data.cost.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });
                document.getElementById('paidAmount').textContent = '৳' + data.paid.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });
                document.getElementById('balanceAmount').textContent = '৳' + data.balance.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });

                // Set amount field
                const amountInput = document.getElementById('paymentAmount');
                const maxSpan = document.getElementById('maxAmount');
                amountInput.max = data.balance;
                amountInput.value = data.balance > 0 ? data.balance.toFixed(2) : '';
                maxSpan.textContent = '৳' + data.balance.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });

                // Show modal
                document.getElementById('paymentModal').classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                // Focus amount field
                setTimeout(() => {
                    amountInput.focus();
                    amountInput.select();
                }, 100);
            }

            function closePaymentModal() {
                document.getElementById('paymentModal').classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                document.getElementById('paymentForm').reset();
                currentSessionId = null;
            }

            // Close modal on ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closePaymentModal();
            });

            // Close modal when clicking outside
            document.getElementById('paymentModal').addEventListener('click', (e) => {
                if (e.target.id === 'paymentModal') closePaymentModal();
            });

            // Validate amount doesn't exceed balance
            document.getElementById('paymentAmount').addEventListener('change', function() {
                const max = parseFloat(this.max);
                const value = parseFloat(this.value);
                if (value > max) {
                    alert('Amount cannot exceed balance of ৳' + max.toFixed(2));
                    this.value = max.toFixed(2);
                }
            });

            // Handle form submission with AJAX
            document.getElementById('paymentForm').addEventListener('submit', async function(e) {
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
                notification.className = `notification-toast fixed top-4 right-4 z-50 px-4 py-3 rounded shadow text-white font-medium ${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 
                    'bg-blue-500'
                }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            }
        </script>
    @endsection
