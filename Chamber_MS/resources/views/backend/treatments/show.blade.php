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
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
        </svg>
        View All Patient Treatments
    </a>
    <a href="{{ route('backend.patients.show', $treatment->patient_id) }}"
        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
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
                                                    {{ $treatment->doctor->user->full_name ?? 'Not Assigned' }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $treatment->doctor->specialization ?? 'General Dentist' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($treatment->appointment)
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                                Appointment Details</h4>
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-calendar-check text-purple-600"></i>
                                                </div>
                                                <div>
                                                    <a href="{{ route('backend.appointments.show', $treatment->appointment_id) }}"
                                                        class="font-medium text-gray-800 hover:text-purple-700 transition-colors">
                                                        {{ $treatment->appointment->appointment_code }}
                                                    </a>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $treatment->appointment->appointment_date->format('d/m/Y') }}
                                                        at {{ date('h:i A', strtotime($treatment->appointment->appointment_time)) }}
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
                            @if($treatment->treatment_plan)
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
                            @if($treatment->followup_date)
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
                                            @if($treatment->followup_notes)
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

                    <!-- Procedures Section -->
           <!-- Procedures Section -->
    @if($treatment->procedures->count())
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-teeth text-purple-600"></i>
                        Procedures ({{ $treatment->procedures->count() }})
                    </h3>
                    <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
                        class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                        <i class="fas fa-plus"></i> Add Procedure
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Procedure
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Tooth
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Cost
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($treatment->procedures as $procedure)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $procedure->procedure_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $procedure->procedure_code }}</div>
                                    @if($procedure->notes)
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ Str::limit($procedure->notes, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($procedure->tooth_number)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Tooth #{{ $procedure->tooth_number }}
                                        </span>
                                        @if($procedure->surface)
                                            <div class="text-xs text-gray-500 mt-1">{{ $procedure->surface }}</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium">৳ {{ number_format($procedure->cost, 2) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'planned' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$procedure->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $procedure->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $procedure->completed_at?->format('d/m/Y') ?? 'Pending' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        <!-- View Button -->
                                        <a href="{{ route('backend.treatment-procedures.show', $procedure) }}"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition-colors"
                                            title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="{{ route('backend.treatment-procedures.edit', $procedure) }}"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-600 bg-yellow-50 rounded hover:bg-yellow-100 transition-colors"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <!-- Status Action Buttons -->
                                        @if($procedure->status == 'planned')
                                            <form action="{{ route('backend.treatment-procedures.start', $procedure) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 rounded hover:bg-green-100 transition-colors"
                                                        title="Start Procedure">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        @if($procedure->status == 'in_progress')
                                            <form action="{{ route('backend.treatment-procedures.complete', $procedure) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 rounded hover:bg-green-100 transition-colors"
                                                        title="Mark Complete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    @if($treatment->procedures->sum('cost') > 0)
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-semibold">Total Procedures Cost:</td>
                                <td class="px-4 py-3 font-bold text-lg">৳ {{ number_format($treatment->procedures->sum('cost'), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-teeth text-purple-600"></i>
                        Procedures
                    </h3>
                    <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
                        class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                        <i class="fas fa-plus"></i> Add Procedure
                    </a>
                </div>
            </div>
            <div class="p-12 text-center">
                <div class="mx-auto w-24 h-24 bg-purple-50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-teeth text-purple-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Procedures Yet</h3>
                <p class="text-gray-500 mb-6">Add procedures to track dental work for this treatment</p>
                <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Your First Procedure
                </a>
            </div>
        </div>
    @endif

                    <!-- Sessions Section -->
                    @if($treatment->sessions->count())
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-4 border-b">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                        <i class="fas fa-clock text-orange-600"></i>
                                        Treatment Sessions ({{ $treatment->sessions->count() }})
                                    </h3>
                                    @if($treatment->canAddSession())
                                        <form action="{{ route('backend.treatments.add-session', $treatment) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                                                <i class="fas fa-plus"></i> Add Session
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Session #</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Date</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Title</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Duration</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($treatment->sessions->sortBy('session_number') as $session)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-bold">
                                                        {{ $session->session_number }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="font-medium">
                                                        {{ $session->actual_date?->format('d/m/Y') ?? $session->scheduled_date->format('d/m/Y') }}
                                                    </div>
                                                    @if($session->actual_date && $session->actual_date->format('d/m/Y') != $session->scheduled_date->format('d/m/Y'))
                                                        <div class="text-xs text-gray-500">Rescheduled from
                                                            {{ $session->scheduled_date->format('d/m/Y') }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 font-medium">{{ $session->session_title }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="font-medium">{{ $session->duration_actual ?? $session->duration_planned }}
                                                            min</span>
                                                        @if($session->duration_actual && $session->duration_actual != $session->duration_planned)
                                                            <span
                                                                class="text-xs {{ $session->duration_actual > $session->duration_planned ? 'text-red-500' : 'text-green-500' }}">
                                                                ({{ $session->duration_actual > $session->duration_planned ? '+' : '' }}{{ $session->duration_actual - $session->duration_planned }}
                                                                min)
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $session->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                        {{ ucfirst($session->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Actions & Timeline -->
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
                            @if($treatment->status == 'planned')
                                <form action="{{ route('backend.treatments.start', $treatment) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                                        <i class="fas fa-play-circle"></i>
                                        Start Treatment
                                    </button>
                                </form>
                            @endif

                            @if($treatment->status == 'in_progress')
                                <form action="{{ route('backend.treatments.complete', $treatment) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                                        <i class="fas fa-check-circle"></i>
                                        Complete Treatment
                                    </button>
                                </form>

                                <form action="{{ route('backend.treatments.hold', $treatment) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                                        <i class="fas fa-pause-circle"></i>
                                        Put on Hold
                                    </button>
                                </form>
                            @endif

                            @if($treatment->status == 'on_hold')
                                <form action="{{ route('backend.treatments.resume', $treatment) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                                        <i class="fas fa-play-circle"></i>
                                        Resume Treatment
                                    </button>
                                </form>
                            @endif

                            @if(in_array($treatment->status, ['planned', 'in_progress', 'on_hold']))
                                <form action="{{ route('backend.treatments.cancel', $treatment) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to cancel this treatment? This action cannot be undone.')">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white rounded-lg py-3 font-medium flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                                        <i class="fas fa-times-circle"></i>
                                        Cancel Treatment
                                    </button>
                                </form>
                            @endif

                            <!-- Add Procedure Button -->
                            <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
                                class="w-full bg-gradient-to-r from-purple-600 to-violet-600 hover:from-purple-700 hover:to-violet-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                                <i class="fas fa-teeth"></i>
                                Add Procedure
                            </a>

                            <!-- Create Prescription Button -->
                            <a href="{{ route('backend.prescriptions.create', ['treatment' => $treatment->id]) }}"
                                class="w-full bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                                <i class="fas fa-prescription-bottle-alt"></i>
                                Create Prescription
                            </a>

                            <!-- Generate Invoice Button -->
                            <a href="{{ route('backend.invoices.create', ['treatment' => $treatment->id]) }}"
                                class="w-full bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5">
                                <i class="fas fa-file-invoice-dollar"></i>
                                Generate Invoice
                            </a>
                        </div>
                    </div>

                    <!-- Timeline Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-6 py-4 border-b">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-history text-gray-600"></i>
                                Treatment Timeline
                            </h3>
                        </div>
                        <div class="p-4">
                            <div class="relative pl-6 space-y-6">
                                <!-- Created -->
                                <div class="relative">
                                    <div
                                        class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-blue-600 border-4 border-white shadow">
                                    </div>
                                    <div class="ml-2">
                                        <h6 class="font-semibold text-gray-800">Treatment Created</h6>
                                        <p class="text-sm text-gray-500">{{ $treatment->created_at->format('d F, Y H:i') }}</p>
                                        <p class="text-xs text-gray-400">By: {{ $treatment->creator->name ?? 'System' }}</p>
                                    </div>
                                </div>

                                <!-- Started -->
                                @if($treatment->start_date)
                                    <div class="relative">
                                        <div
                                            class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-green-600 border-4 border-white shadow">
                                        </div>
                                        <div class="ml-2">
                                            <h6 class="font-semibold text-gray-800">Treatment Started</h6>
                                            <p class="text-sm text-gray-500">{{ $treatment->start_date->format('d F, Y') }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Expected End -->
                                @if($treatment->expected_end_date)
                                    <div class="relative">
                                        <div
                                            class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-yellow-500 border-4 border-white shadow">
                                        </div>
                                        <div class="ml-2">
                                            <h6 class="font-semibold text-gray-800">Expected Completion</h6>
                                            <p class="text-sm text-gray-500">{{ $treatment->expected_end_date->format('d F, Y') }}
                                            </p>
                                            @if($treatment->expected_end_date->isPast() && $treatment->status != 'completed')
                                                <span
                                                    class="inline-block mt-1 px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                                                    Overdue
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Actual End -->
                                @if($treatment->actual_end_date)
                                    <div class="relative">
                                        <div
                                            class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-purple-600 border-4 border-white shadow">
                                        </div>
                                        <div class="ml-2">
                                            <h6 class="font-semibold text-gray-800">Treatment Completed</h6>
                                            <p class="text-sm text-gray-500">{{ $treatment->actual_end_date->format('d F, Y') }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Updated -->
                                @if($treatment->updated_at != $treatment->created_at)
                                    <div class="relative">
                                        <div
                                            class="absolute -left-6 mt-1 w-4 h-4 rounded-full bg-gray-400 border-4 border-white shadow">
                                        </div>
                                        <div class="ml-2">
                                            <h6 class="font-semibold text-gray-800">Last Updated</h6>
                                            <p class="text-sm text-gray-500">{{ $treatment->updated_at->format('d F, Y H:i') }}</p>
                                            <p class="text-xs text-gray-400">By: {{ $treatment->updater->name ?? 'System' }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-chart-bar text-emerald-600"></i>
                                Treatment Statistics
                            </h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <p class="text-sm text-gray-500">Sessions</p>
                                    <p class="text-2xl font-bold text-blue-600">
                                        {{ $treatment->completed_sessions }}/{{ $treatment->estimated_sessions }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <p class="text-sm text-gray-500">Duration</p>
                                    <p class="text-2xl font-bold text-green-600">
                                        @if($treatment->start_date && $treatment->actual_end_date)
                                            {{ $treatment->start_date->diffInDays($treatment->actual_end_date) }} days
                                        @elseif($treatment->start_date)
                                            {{ $treatment->start_date->diffInDays(now()) }} days
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Procedures Completed:</span>
                                    <span
                                        class="font-medium">{{ $treatment->procedures->where('status', 'completed')->count() }}/{{ $treatment->procedures->count() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Procedures Cost:</span>
                                    <span class="font-medium">৳
                                        {{ number_format($treatment->procedures->sum('cost'), 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Sessions Conducted:</span>
                                    <span
                                        class="font-medium">{{ $treatment->sessions->where('status', 'completed')->count() }}</span>
                                </div>
                            </div>
                        </div>
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