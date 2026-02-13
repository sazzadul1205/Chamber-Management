@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Refund History</h2>
            <a href="{{ route('backend.payments.index') }}"
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded text-sm">
                Back to Payment History
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500 mb-1">Total Refund Records</div>
                <div class="text-2xl font-bold">{{ number_format($summary['count']) }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500 mb-1">Total Refunded (All Time)</div>
                <div class="text-2xl font-bold text-red-600">৳{{ number_format($summary['amount'], 2) }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500 mb-1">Filtered Records</div>
                <div class="text-2xl font-bold">{{ number_format($summary['period_count']) }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500 mb-1">Filtered Refund Amount</div>
                <div class="text-2xl font-bold text-orange-600">৳{{ number_format($summary['period_amount'], 2) }}</div>
            </div>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 bg-white p-4 rounded shadow">
            <div class="md:col-span-4">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search refund no/ref/patient/remarks" class="w-full border rounded px-3 py-2">
            </div>
            <div class="md:col-span-3">
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
                <select name="payment_method" class="w-full border rounded px-3 py-2">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                    <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>
                        Bank Transfer</option>
                    <option value="cheque" {{ request('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="mobile_banking" {{ request('payment_method') === 'mobile_banking' ? 'selected' : '' }}>
                        Mobile Banking</option>
                    <option value="other" {{ request('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="md:col-span-1">
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div class="md:col-span-1">
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div class="md:col-span-1">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">Filter</button>
            </div>
        </form>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">Refund No</th>
                        <th class="px-3 py-2 text-left text-sm">Date</th>
                        <th class="px-3 py-2 text-left text-sm">Patient</th>
                        <th class="px-3 py-2 text-left text-sm">Original Payment</th>
                        <th class="px-3 py-2 text-left text-sm">Method</th>
                        <th class="px-3 py-2 text-left text-sm">Amount</th>
                        <th class="px-3 py-2 text-left text-sm">Created By</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($refunds as $refund)
                        @php
                            $originalPaymentNo = str_starts_with((string) $refund->reference_no, 'REF-')
                                ? substr($refund->reference_no, 4)
                                : null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div class="font-medium text-red-600">{{ $refund->payment_no }}</div>
                                <div class="text-xs text-gray-500">{{ $refund->reference_no ?? '-' }}</div>
                            </td>
                            <td class="px-3 py-2 text-sm">{{ $refund->payment_date->format('d/m/Y h:i A') }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route('backend.patients.show', $refund->patient_id) }}"
                                    class="text-blue-600 hover:underline">
                                    {{ $refund->patient->full_name }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $refund->patient->patient_code }}</div>
                            </td>
                            <td class="px-3 py-2">
                                @if ($originalPaymentNo)
                                    <span class="text-gray-700">{{ $originalPaymentNo }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm">{{ ucfirst(str_replace('_', ' ', $refund->payment_method)) }}</td>
                            <td class="px-3 py-2 font-bold text-red-600">৳{{ number_format(abs($refund->amount), 2) }}</td>
                            <td class="px-3 py-2 text-sm">{{ $refund->createdBy->name ?? 'System' }}</td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('backend.payments.show', $refund->id) }}"
                                    class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs" title="View">
                                    @include('partials.sidebar-icon', ['name' => 'B_View', 'class' => 'w-4 h-4'])
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">No refund records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$refunds" />
    </div>
@endsection

