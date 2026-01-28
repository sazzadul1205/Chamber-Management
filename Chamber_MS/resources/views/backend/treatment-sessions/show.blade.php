@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Session Details</h2>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-lg font-semibold text-blue-700">
                        Session {{ $treatmentSession->session_number }}: {{ $treatmentSession->session_title }}
                    </span>
                    @php
                        $statusColors = [
                            'scheduled' => 'bg-blue-100 text-blue-800',
                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            'postponed' => 'bg-gray-100 text-gray-800'
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$treatmentSession->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst(str_replace('_', ' ', $treatmentSession->status)) }}
                    </span>
                </div>
            </div>
       <div class="flex flex-wrap gap-2">
    <a href="{{ route('backend.treatments.show', $treatmentSession->treatment_id) }}"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Back to Treatment
    </a>
    <a href="{{ route('backend.treatment-sessions.edit', $treatmentSession) }}"
        class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
        Edit Session
    </a>
    <a href="{{ route('backend.treatment-sessions.index') }}"
        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
        </svg>
        All Sessions
    </a>
</div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Session Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Session Information Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            Session Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-4">
                                <!-- Treatment Info -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Treatment</h4>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-stethoscope text-blue-600"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('backend.treatments.show', $treatmentSession->treatment_id) }}"
                                                class="font-medium text-blue-600 hover:text-blue-800">
                                                {{ $treatmentSession->treatment->treatment_code }}
                                            </a>
                                            <p class="text-sm text-gray-500">
                                                {{ $treatmentSession->treatment->diagnosis }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Patient Info -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Patient</h4>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-green-600"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('backend.patients.show', $treatmentSession->treatment->patient_id) }}"
                                                class="font-medium text-gray-800 hover:text-blue-600">
                                                {{ $treatmentSession->treatment->patient->full_name }}
                                            </a>
                                            <p class="text-sm text-gray-500">
                                                {{ $treatmentSession->treatment->patient->patient_code }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Doctor Info -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Doctor</h4>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-md text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">
                                                {{ $treatmentSession->treatment->doctor->user->full_name ?? 'Not Assigned' }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $treatmentSession->treatment->doctor->specialization ?? 'General Dentist' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-4">
                                <!-- Schedule Info -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Schedule</h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Scheduled Date:</span>
                                            <span class="font-medium">{{ $treatmentSession->scheduled_date->format('d F, Y') }}</span>
                                        </div>
                                        @if($treatmentSession->actual_date)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Actual Date:</span>
                                                <span class="font-medium">{{ $treatmentSession->actual_date->format('d F, Y') }}</span>
                                            </div>
                                        @endif
                                        @if($treatmentSession->appointment)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Appointment Time:</span>
                                                <span class="font-medium">
                                                    {{ date('h:i A', strtotime($treatmentSession->appointment->appointment_time)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Duration Info -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Duration</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Planned:</span>
                                            <span class="font-medium">{{ $treatmentSession->duration_planned }} minutes</span>
                                        </div>
                                        @if($treatmentSession->duration_actual)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Actual:</span>
                                                <span class="font-medium">{{ $treatmentSession->duration_actual }} minutes</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dental Chair -->
                        @if($treatmentSession->chair)
                            <div class="mt-6 pt-6 border-t">
                                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Dental Chair</h4>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-chair text-orange-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $treatmentSession->chair->name }}</p>
                                        <p class="text-sm text-gray-500">
                                            Location: {{ $treatmentSession->chair->location ?? 'Main Clinic' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Procedure Details Card -->
                @if($treatmentSession->procedure_details)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-procedures text-green-600"></i>
                                Procedure Details
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="prose max-w-none">
                                {!! nl2br(e($treatmentSession->procedure_details)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Notes Card -->
                @if($treatmentSession->doctor_notes || $treatmentSession->assistant_notes)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-50 to-violet-50 px-6 py-4 border-b">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-notes-medical text-purple-600"></i>
                                Notes
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($treatmentSession->doctor_notes)
                                    <div>
                                        <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fas fa-user-md text-blue-500"></i>
                                            Doctor's Notes
                                        </h4>
                                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                                            <p class="text-gray-800 whitespace-pre-line">{{ $treatmentSession->doctor_notes }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if($treatmentSession->assistant_notes)
                                    <div>
                                        <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fas fa-user-nurse text-green-500"></i>
                                            Assistant's Notes
                                        </h4>
                                        <div class="bg-green-50 border border-green-100 rounded-lg p-4">
                                            <p class="text-gray-800 whitespace-pre-line">{{ $treatmentSession->assistant_notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Actions & Info -->
            <div class="space-y-6">
                <!-- Quick Actions Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4 border-b">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-bolt text-red-600"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @if($treatmentSession->status == 'scheduled')
                          <form action="{{ route('backend.treatment-sessions.start', $treatmentSession) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2">
                                    <i class="fas fa-play"></i>
                                    Start Session
                                </button>
                            </form>
                        @endif

                        @if($treatmentSession->status == 'in_progress')
                            <form action="{{ route('backend.treatment-sessions.complete', $treatmentSession) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2">
                                    <i class="fas fa-check"></i>
                                    Complete Session
                                </button>
                            </form>
                        @endif

                        @if(in_array($treatmentSession->status, ['scheduled', 'in_progress']))
                            <!-- Postpone Button -->
                            <button type="button"
                                onclick="openPostponeModal()"
                                class="w-full bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-calendar-minus"></i>
                                Postpone Session
                            </button>

                            <!-- Cancel Button -->
                            <form action="{{ route('backend.treatment-sessions.cancel', $treatmentSession) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to cancel this session?')">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2">
                                    <i class="fas fa-times"></i>
                                    Cancel Session
                                </button>
                            </form>
                        @endif

                        <!-- View Treatment -->
                        <a href="{{ route('backend.treatments.show', $treatmentSession->treatment_id) }}"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2">
                            <i class="fas fa-stethoscope"></i>
                            View Treatment
                        </a>

                        <!-- Edit Session -->
                        <a href="{{ route('backend.treatment-sessions.edit', $treatmentSession) }}"
                            class="w-full bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2">
                            <i class="fas fa-edit"></i>
                            Edit Session
                        </a>
                    </div>
                </div>

           <!-- Financial Info Card -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-emerald-100">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Financial Information
        </h3>
    </div>
    
    <div class="p-6 space-y-5">
        <!-- Session Cost -->
        @if($treatmentSession->cost_for_session)
            <div class="flex justify-between items-center p-4 bg-emerald-50 rounded-lg border border-emerald-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-emerald-800">Session Cost</p>
                        <p class="text-xs text-emerald-600">Estimated cost for this session</p>
                    </div>
                </div>
                <span class="text-xl font-bold text-emerald-800">৳ {{ number_format($treatmentSession->cost_for_session, 2) }}</span>
            </div>
        @endif

        <!-- Payment Progress -->
        @php
            $totalPaid = $treatmentSession->getTotalPaidAmount();
            $remaining = $treatmentSession->getRemainingAmount();
            $paymentPercentage = $treatmentSession->cost_for_session > 0 
                ? min(100, ($totalPaid / $treatmentSession->cost_for_session) * 100) 
                : 0;
            $isFullyPaid = $remaining <= 0;
        @endphp
        
        <div class="space-y-4">
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Payment Progress</span>
                    <span class="text-sm font-semibold {{ $isFullyPaid ? 'text-green-600' : 'text-blue-600' }}">
                        {{ number_format($paymentPercentage, 1) }}%
                    </span>
                </div>
                <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full {{ $isFullyPaid ? 'bg-green-500' : 'bg-blue-500' }} transition-all duration-500 ease-out" 
                         style="width: {{ $paymentPercentage }}%">
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs text-gray-600">Total Paid</span>
                    </div>
                    <p class="text-lg font-bold text-green-700">৳ {{ number_format($totalPaid, 2) }}</p>
                </div>

                <div class="bg-gray-50 p-3 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            @if($remaining > 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            @endif
                        </svg>
                        <span class="text-xs text-gray-600">Remaining</span>
                    </div>
                    <p class="text-lg font-bold {{ $remaining > 0 ? 'text-red-700' : 'text-green-700' }}">
                        ৳ {{ number_format($remaining, 2) }}
                    </p>
                </div>
            </div>

            <!-- Payment Status Badge -->
            <div class="flex justify-center">
                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                    {{ $isFullyPaid ? 'bg-green-100 text-green-800' : 
                       ($remaining == $treatmentSession->cost_for_session ? 'bg-red-100 text-red-800' : 
                       'bg-yellow-100 text-yellow-800') }}">
                    @if($isFullyPaid)
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Fully Paid
                        </span>
                    @elseif($remaining == $treatmentSession->cost_for_session)
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            Not Paid
                        </span>
                    @else
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Partial Payment
                        </span>
                    @endif
                </span>
            </div>

            <!-- Payment Actions -->
            <div class="space-y-2 pt-4 border-t border-gray-200">
                @if($remaining > 0)
                    <a href="{{ route('backend.payments.create', ['session_id' => $treatmentSession->id]) }}"
                        class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg py-3 text-center text-sm font-medium flex items-center justify-center gap-2 transition-all hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Add Payment
                    </a>
                @else
                    <div class="w-full bg-green-100 border border-green-200 text-green-800 rounded-lg py-3 text-center text-sm font-medium flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Payment Complete
                    </div>
                @endif

                <!-- View Payment History -->
                @if($treatmentSession->payments()->exists())
                    <a href="{{ route('backend.payments.index', ['session_id' => $treatmentSession->id]) }}"
                        class="w-full bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 text-blue-700 border border-blue-200 rounded-lg py-2 text-center text-xs font-medium flex items-center justify-center gap-2 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        View Payment History
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

                <!-- Timeline Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-6 py-4 border-b">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-history text-gray-600"></i>
                            Session Timeline
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="relative pl-6 space-y-4">
                            <!-- Created -->
                            <div class="relative">
                                <div class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-blue-600 border-4 border-white shadow"></div>
                                <div class="ml-2">
                                    <h6 class="font-semibold text-gray-800">Session Created</h6>
                                    <p class="text-xs text-gray-500">{{ $treatmentSession->created_at->format('d F, Y H:i') }}</p>
                                    <p class="text-xs text-gray-400">By: {{ $treatmentSession->creator->name ?? 'System' }}</p>
                                </div>
                            </div>

                            <!-- Status Changes -->
                            @if($treatmentSession->status == 'completed')
                                <div class="relative">
                                    <div class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-green-600 border-4 border-white shadow"></div>
                                    <div class="ml-2">
                                        <h6 class="font-semibold text-gray-800">Session Completed</h6>
                                        @if($treatmentSession->updated_at)
                                            <p class="text-xs text-gray-500">{{ $treatmentSession->updated_at->format('d F, Y H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @elseif($treatmentSession->status == 'in_progress')
                                <div class="relative">
                                    <div class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-yellow-500 border-4 border-white shadow"></div>
                                    <div class="ml-2">
                                        <h6 class="font-semibold text-gray-800">Session In Progress</h6>
                                        <p class="text-xs text-gray-500">Started recently</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Updated -->
                            @if($treatmentSession->updated_at != $treatmentSession->created_at)
                                <div class="relative">
                                    <div class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-gray-400 border-4 border-white shadow"></div>
                                    <div class="ml-2">
                                        <h6 class="font-semibold text-gray-800">Last Updated</h6>
                                        <p class="text-xs text-gray-500">{{ $treatmentSession->updated_at->format('d F, Y H:i') }}</p>
                                        <p class="text-xs text-gray-400">By: {{ $treatmentSession->updater->name ?? 'System' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Postpone Modal -->
    <div id="postponeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                <h3 class="text-lg font-semibold mb-4">Postpone Session</h3>

                <form action="{{ route('backend.treatment-sessions.postpone', $treatmentSession) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Date *</label>
                        <input type="date" name="new_date" required
                               class="w-full border rounded px-3 py-2"
                               min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3"
                                  class="w-full border rounded px-3 py-2"
                                  placeholder="Reason for postponement..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closePostponeModal()"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                            Postpone Session
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openPostponeModal() {
        document.getElementById('postponeModal').classList.remove('hidden');
    }

    function closePostponeModal() {
        document.getElementById('postponeModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.addEventListener('click', (e) => {
        if (e.target.id === 'postponeModal') {
            closePostponeModal();
        }
    });
    </script>

    <style>
    .relative.pl-6 > .relative:not(:last-child):after {
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