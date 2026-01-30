@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Treatment Details</h2>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-lg font-semibold text-blue-700">{{ $treatment->treatment_code }}</span>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-{{ $treatment->status_color }}">
                        {{ $treatment->status_text }}
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.treatments.patient-treatments', $treatment->patient_id) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    View All Patient Treatments
                </a>
                <a href="{{ route('backend.patients.show', $treatment->patient_id) }}"
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Patient Profile
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Main Treatment Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Treatment Information Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-stethoscope text-blue-600"></i>
                            Treatment Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Patient
                                        Information</h4>
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('backend.patients.show', $treatment->patient_id) }}"
                                                class="text-lg font-semibold text-gray-800 hover:text-blue-700 transition-colors">
                                                {{ $treatment->patient->full_name }}
                                            </a>
                                            <p class="text-sm text-gray-500">{{ $treatment->patient->patient_code }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Doctor
                                        Information</h4>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-md text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">
                                                {{ $treatment->doctor->user->full_name ?? 'Not Assigned' }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $treatment->doctor->specialization ?? 'General Dentist' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @if ($treatment->appointment)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                            Appointment Details</h4>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-calendar-check text-purple-600"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('backend.appointments.show', $treatment->appointment_id) }}"
                                                    class="font-medium text-gray-800 hover:text-purple-700 transition-colors">
                                                    {{ $treatment->appointment->appointment_code }}
                                                </a>
                                                <p class="text-sm text-gray-500">
                                                    {{ $treatment->appointment->appointment_date->format('d/m/Y') }}
                                                    at
                                                    {{ date('h:i A', strtotime($treatment->appointment->appointment_time)) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Treatment
                                        Details</h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Treatment Type:</span>
                                            <span class="font-medium">{{ $treatment->treatment_type_text }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Treatment Date:</span>
                                            <span
                                                class="font-medium">{{ $treatment->treatment_date->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Sessions Progress:</span>
                                            <span class="font-medium">{{ $treatment->session_progress_text }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Financial
                                        Details</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Estimated Cost:</span>
                                            <span class="font-medium">{{ $treatment->formatted_estimated_cost }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Actual Cost:</span>
                                            <span class="font-medium">{{ $treatment->formatted_actual_cost }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Discount:</span>
                                            <span class="font-medium text-red-600">-৳
                                                {{ number_format($treatment->discount, 2) }}</span>
                                        </div>
                                        <!-- ADD THIS LINE: -->
                                        <div class="flex justify-between pt-2 border-t border-gray-200">
                                            <span class="text-gray-600 font-semibold">Total Session Costs:</span>
                                            <span class="font-bold text-blue-700">৳
                                                {{ number_format($costBreakdown['session_costs'] ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Treatment Progress</span>
                                <span class="text-sm font-bold text-blue-600">{{ $treatment->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-{{ $treatment->status_color }} h-3 rounded-full transition-all duration-500"
                                    style="width: {{ $treatment->progress_percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Information Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-medical text-green-600"></i>
                            Medical Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Diagnosis -->
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-diagnoses text-blue-500"></i>
                                Diagnosis
                            </h4>
                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                                <p class="text-gray-800 whitespace-pre-line">{{ $treatment->diagnosis }}</p>
                            </div>
                        </div>

                        <!-- Treatment Plan -->
                        @if ($treatment->treatment_plan)
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-clipboard-list text-green-500"></i>
                                    Treatment Plan
                                </h4>
                                <div class="bg-green-50 border border-green-100 rounded-lg p-4">
                                    <p class="text-gray-800 whitespace-pre-line">{{ $treatment->treatment_plan }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Follow-up Information -->
                        @if ($treatment->followup_date)
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-cyan-500"></i>
                                    Follow-up Information
                                </h4>
                                <div class="bg-cyan-50 border border-cyan-100 rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Follow-up Date</p>
                                            <p class="font-medium">{{ $treatment->followup_date->format('d F, Y') }}</p>
                                        </div>
                                        @if ($treatment->followup_notes)
                                            <div>
                                                <p class="text-sm text-gray-500">Notes</p>
                                                <p class="text-gray-800">{{ $treatment->followup_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Treatments --}}
                @include('backend.treatments.Components.treatments')

                {{-- Sessions --}}
                @include('backend.treatments.Components.session')

                {{-- Prescriptions --}}
                @include('backend.treatments.Components.prescriptions')

                {{-- Medical Files --}}
                @include('backend.treatments.Components.medical-files')

            </div>

            <!-- Right Column: Actions & Timeline -->
            <div class="space-y-6">
                <!-- Quick Actions Card -->
                @include('backend.treatments.Components.quick-action')

                <!-- Timeline Card -->
                @include('backend.treatments.Components.timeline')

                <!-- Statistics Card  -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-emerald-600"></i>
                            Treatment Statistics
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <!-- Session Progress -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-sm text-gray-500">Sessions</p>
                                <p class="text-2xl font-bold text-blue-600">
                                    {{ $treatment->completed_sessions }}/{{ $treatment->estimated_sessions }}
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-sm text-gray-500">Duration</p>
                                <p class="text-2xl font-bold text-green-600">
                                    @if ($treatment->start_date && $treatment->actual_end_date)
                                        {{ $treatment->start_date->diffInDays($treatment->actual_end_date) }} days
                                    @elseif($treatment->start_date)
                                        {{ $treatment->start_date->diffInDays(now()) }} days
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Cost Statistics -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Session Costs:</span>
                                <span class="font-bold text-blue-700">৳
                                    {{ number_format($costBreakdown['session_costs'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Procedures Cost:</span>
                                <span class="font-bold text-purple-700">৳
                                    {{ number_format($costBreakdown['procedure_costs'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Estimated Cost:</span>
                                <span class="font-bold text-gray-700">৳
                                    {{ number_format($costBreakdown['estimated_cost'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Discount:</span>
                                <span class="font-bold text-red-600">-৳
                                    {{ number_format($costBreakdown['discount'] ?? 0, 2) }}</span>
                            </div>
                            <div class="pt-2 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-800 font-semibold">Final Actual Cost:</span>
                                    <span class="text-xl font-bold text-green-700">৳
                                        {{ number_format($costBreakdown['final_actual'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Card -->
                @include('backend.treatments.Components.payment-card')

                <!-- Recent Payments Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-teal-50 to-cyan-50 px-6 py-4 border-b">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-money-bill-wave text-teal-600"></i>
                                Recent Payments
                            </h3>
                            <a href="{{ route('backend.payments.index', ['treatment_id' => $treatment->id]) }}"
                                class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                                View All
                            </a>
                        </div>
                    </div>

                    @if ($treatment->invoices->flatMap->payments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Method
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Amount
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>

                                @php
                                    $paymentMethodColors = [
                                        'cash' => 'bg-green-100 text-green-800',
                                        'card' => 'bg-blue-100 text-blue-800',
                                        'bank_transfer' => 'bg-purple-100 text-purple-800',
                                        'default' => 'bg-gray-100 text-gray-800',
                                    ];

                                    $paymentStatusColors = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'default' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp


                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($treatment->invoices->flatMap->payments->sortByDesc('payment_date')->take(5) as $payment)
                                        <tr class="hover:bg-gray-50">
                                            <!-- Payment Date -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                                            </td>

                                            <!-- Payment Method -->
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentMethodColors[$payment->payment_method] ?? $paymentMethodColors['default'] }}">
                                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                                </span>
                                            </td>

                                            <!-- Payment Amount -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-green-700">
                                                ৳ {{ number_format($payment->amount, 2) }}
                                            </td>

                                            <!-- Payment Status -->
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentStatusColors[$payment->status] ?? $paymentStatusColors['default'] }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <div class="mx-auto w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-money-bill-wave text-teal-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Payments Yet</h3>
                            <p class="text-gray-500 mb-4">Record payments for this treatment</p>
                            <a href="{{ route('backend.payments.create', ['treatment_id' => $treatment->id]) }}"
                                class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Record First Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add some custom CSS for the timeline connector lines -->
    <style>
        .relative.pl-6>.relative:not(:last-child):after {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 1.5rem;
            bottom: -1.5rem;
            width: 2px;
            background-color: #e5e7eb;
        }
    </style>
@endsection
