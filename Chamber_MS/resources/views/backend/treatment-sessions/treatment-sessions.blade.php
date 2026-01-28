@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Treatment Sessions</h2>
                <p class="text-gray-600 mt-1">
                    For Treatment: <span class="font-semibold text-blue-700">{{ $treatment->treatment_code }}</span> -
                    Patient: <span class="font-medium">{{ $treatment->patient->full_name }}</span>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.treatments.show', $treatment) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    Back to Treatment
                </a>
                @if($treatment->canAddSession())
                    <a href="{{ route('backend.treatment-sessions.create', ['treatment_id' => $treatment->id]) }}"
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                        <i class="fas fa-plus"></i>
                        Add Session
                    </a>
                @endif
                <a href="{{ route('backend.treatment-sessions.today') }}"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                    <i class="fas fa-calendar-day"></i>
                    Today's Sessions
                </a>
            </div>
        </div>

        <!-- Sessions Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-clock text-orange-600"></i>
                        Sessions ({{ $sessions->count() }}/{{ $treatment->estimated_sessions }})
                    </h3>
                    @if($treatment->canAddSession())
                        <a href="{{ route('backend.treatment-sessions.create', ['treatment_id' => $treatment->id]) }}"
                            class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                            <i class="fas fa-plus"></i> Add Session
                        </a>
                    @endif
                </div>
            </div>

            @if($sessions->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Session #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Date & Time</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Title</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Duration</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sessions as $session)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <!-- Session Number -->
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-bold">
                                                        {{ $session->session_number }}
                                                    </span>
                                                </td>

                                                <!-- Date & Time -->
                                                <td class="px-4 py-3">
                                                    <div class="font-medium">
                                                        {{ $session->scheduled_date->format('d/m/Y') }}
                                                    </div>
                                                    @if($session->actual_date && $session->actual_date->format('Y-m-d') != $session->scheduled_date->format('Y-m-d'))
                                                        <div class="text-xs text-gray-500">
                                                            Actual: {{ $session->actual_date->format('d/m/Y') }}
                                                        </div>
                                                    @elseif($session->appointment)
                                                        <div class="text-xs text-gray-500">
                                                            {{ date('h:i A', strtotime($session->appointment->appointment_time)) }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <!-- Title -->
                                                <td class="px-4 py-3">
                                                    <div class="font-medium">{{ $session->session_title }}</div>
                                                    @if($session->chair)
                                                        <div class="text-xs text-gray-500">Chair: {{ $session->chair->name }}</div>
                                                    @endif
                                                    @if($session->procedure_details)
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ Str::limit($session->procedure_details, 50) }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <!-- Duration -->
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-1">
                                                        <span class="font-medium">{{ $session->duration_actual ?? $session->duration_planned }}
                                                            min</span>
                                                        @if($session->duration_actual && $session->duration_actual != $session->duration_planned)
                                                            <span
                                                                class="text-xs {{ $session->duration_actual > $session->duration_planned ? 'text-red-500' : 'text-green-500' }}">
                                                                ({{ $session->duration_actual > $session->duration_planned ? '+' : '' }}{{ $session->duration_actual - $session->duration_planned }})
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Status -->
                                                <td class="px-4 py-3">
                                                    @php
                                                        $statusColors = [
                                                            'scheduled' => 'bg-blue-100 text-blue-800',
                                                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                                                            'completed' => 'bg-green-100 text-green-800',
                                                            'cancelled' => 'bg-red-100 text-red-800',
                                                            'postponed' => 'bg-gray-100 text-gray-800'
                                                        ];
                                                    @endphp
                                <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$session->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                                    </span>
                                                </td>

                                                <!-- Cost -->
                                                <td class="px-4 py-3">
                                                    @if($session->cost_for_session)
                                                        <span class="font-medium">৳ {{ number_format($session->cost_for_session, 2) }}</span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>

                                              <!-- Actions -->
<td class="px-4 py-3">
    <div class="flex items-center space-x-2">
        <!-- View -->
        <a href="{{ route('backend.treatment-sessions.show', $session) }}"
            class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition-colors"
            title="View Details">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </a>

        <!-- Edit -->
        <a href="{{ route('backend.treatment-sessions.edit', $session) }}"
            class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-600 bg-yellow-50 rounded hover:bg-yellow-100 transition-colors"
            title="Edit">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </a>

        <!-- Quick Actions -->
        @if($session->status == 'scheduled')
            <form action="{{ route('backend.treatment-sessions.start', $session) }}" method="POST"
                class="inline">
                @csrf
                <button type="submit"
                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 rounded hover:bg-green-100 transition-colors"
                    title="Start Session">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </form>
        @endif

        @if($session->status == 'in_progress')
            <form action="{{ route('backend.treatment-sessions.complete', $session) }}"
                method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 rounded hover:bg-green-100 transition-colors"
                    title="Mark Complete">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </form>
        @endif
    </div>
</td>
                                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-clock text-orange-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Sessions Yet</h3>
                    <p class="text-gray-500 mb-6">Add sessions to schedule dental appointments for this treatment</p>
                    @if($treatment->canAddSession())
                        <a href="{{ route('backend.treatment-sessions.create', ['treatment_id' => $treatment->id]) }}"
                            class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Create First Session
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Treatment Progress -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-600"></i>
                    Treatment Progress
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Progress Bar -->
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                            <span class="text-sm font-bold text-blue-600">{{ $treatment->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-{{ $treatment->status_color }} h-3 rounded-full transition-all duration-500"
                                style="width: {{ $treatment->progress_percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Total Sessions</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $treatment->estimated_sessions }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Completed</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ $sessions->where('status', 'completed')->count() }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">In Progress</p>
                            <p class="text-2xl font-bold text-yellow-600">
                                {{ $sessions->where('status', 'in_progress')->count() }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Scheduled</p>
                            <p class="text-2xl font-bold text-blue-600">
                                {{ $sessions->where('status', 'scheduled')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection