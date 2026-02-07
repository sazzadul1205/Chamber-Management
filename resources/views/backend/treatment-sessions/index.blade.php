@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Treatment Sessions</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.treatment-sessions.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Session
                </a>
                <a href="{{ route('backend.treatment-sessions.today') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Today's Sessions
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.treatment-sessions.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3">

            <!-- Treatment -->
            <div class="md:col-span-3">
                <select name="treatment_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Treatments</option>
                    @foreach($treatments as $treatment)
                        <option value="{{ $treatment->id }}" {{ request('treatment_id') == $treatment->id ? 'selected' : '' }}>
                            {{ $treatment->treatment_code }} - {{ $treatment->patient->full_name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    @foreach(App\Models\TreatmentSession::statuses() as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date -->
            <div class="md:col-span-2">
                <input type="date" name="date" value="{{ request('date') }}" class="w-full border rounded px-3 py-2">
            </div>

            <!-- Dental Chair -->
            <div class="md:col-span-2">
                <select name="chair_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Chairs</option>
                    @foreach($chairs as $chair)
                        <option value="{{ $chair->id }}" {{ request('chair_id') == $chair->id ? 'selected' : '' }}>
                            {{ $chair->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Button -->
            <div class="md:col-span-2">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
            </div>

            <!-- Clear Filters -->
            @if(request()->anyFilled(['treatment_id', 'status', 'date', 'chair_id']))
                <div class="md:col-span-1">
                    <a href="{{ route('backend.treatment-sessions.index') }}"
                        class="w-full inline-block text-center bg-gray-300 hover:bg-gray-400 text-gray-800 rounded px-3 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </a>
                </div>
            @endif
        </form>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Sessions -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Sessions</p>
                        <p class="text-xl font-bold text-gray-800">{{ $sessions->total() }}</p>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Completed</p>
                        <p class="text-xl font-bold text-gray-800">
                            {{ App\Models\TreatmentSession::where('status', 'completed')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">In Progress</p>
                        <p class="text-xl font-bold text-gray-800">
                            {{ App\Models\TreatmentSession::where('status', 'in_progress')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Today's Sessions -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Today's</p>
                        <p class="text-xl font-bold text-gray-800">
                            {{ App\Models\TreatmentSession::today()->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">Session #</th>
                        <th class="px-3 py-2 text-left text-sm">Treatment & Patient</th>
                        <th class="px-3 py-2 text-left text-sm">Date & Time</th>
                        <th class="px-3 py-2 text-left text-sm">Title</th>
                        <th class="px-3 py-2 text-left text-sm">Chair</th>
                        <th class="px-3 py-2 text-left text-sm">Status</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50">
                            <!-- Session Number -->
                            <td class="px-3 py-2">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-bold text-sm">
                                    {{ $session->session_number }}
                                </span>
                            </td>

                            <!-- Treatment & Patient -->
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <a href="{{ route('backend.treatments.show', $session->treatment_id) }}"
                                            class="text-blue-600 hover:underline font-medium block">
                                            {{ $session->treatment->treatment_code }}
                                        </a>
                                        <a href="{{ route('backend.patients.show', $session->treatment->patient_id) }}"
                                            class="text-gray-700 hover:text-blue-600 text-sm block">
                                            {{ $session->treatment->patient->full_name }}
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <!-- Date & Time -->
                            <td class="px-3 py-2">
                                <div class="font-medium text-gray-900">
                                    {{ $session->scheduled_date->format('d M Y') }}
                                </div>
                                @if($session->appointment)
                                    <div class="text-xs text-gray-500">
                                        {{ date('h:i A', strtotime($session->appointment->appointment_time)) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Title -->
                            <td class="px-3 py-2">
                                <div class="font-medium text-gray-900">{{ $session->session_title }}</div>
                                @if($session->procedure_details)
                                    <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                        {{ Str::limit($session->procedure_details, 40) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Chair -->
                            <td class="px-3 py-2">
                                @if($session->chair)
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded bg-cyan-100 text-cyan-800 font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                        </svg>
                                        {{ $session->chair->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-3 py-2">
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
                                    class="px-2 py-1 text-xs font-medium rounded {{ $statusColors[$session->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-3 py-2">
                                <div class="flex justify-center gap-1">
                                    <!-- View -->
                                    <a href="{{ route('backend.treatment-sessions.show', $session) }}"
                                        class="w-8 h-8 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                        title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('backend.treatment-sessions.edit', $session) }}"
                                        class="w-8 h-8 flex items-center justify-center bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs"
                                        title="Edit Session">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <!-- Quick Actions -->
                                    @if($session->status == 'scheduled')
                                        <form method="POST" action="{{ route('backend.treatment-sessions.start', $session) }}"
                                            class="inline" onsubmit="return confirm('Start this session?')">
                                            @csrf
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-green-600 hover:bg-green-700 text-white rounded text-xs"
                                                title="Start Session">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    @if($session->status == 'in_progress')
                                        <form method="POST" action="{{ route('backend.treatment-sessions.complete', $session) }}"
                                            class="inline" onsubmit="return confirm('Mark session as complete?')">
                                            @csrf
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-green-600 hover:bg-green-700 text-white rounded text-xs"
                                                title="Mark Complete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($session->status, ['scheduled', 'in_progress']))
                                        <form method="POST" action="{{ route('backend.treatment-sessions.cancel', $session) }}"
                                            class="inline" onsubmit="return confirm('Cancel this session?')">
                                            @csrf
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded text-xs"
                                                title="Cancel Session">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete -->
                                    @if($session->status != 'completed')
                                        <form method="POST" action="{{ route('backend.treatment-sessions.destroy', $session) }}"
                                            class="inline" onsubmit="return confirm('Delete this session?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded text-xs"
                                                title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Sessions Found</h3>
                                <p class="text-gray-500 mb-4">No treatment sessions match your filters</p>
                                <a href="{{ route('backend.treatment-sessions.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create New Session
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <!-- Pagination -->
        <x-pagination :paginator="$sessions" />
    </div>
@endsection