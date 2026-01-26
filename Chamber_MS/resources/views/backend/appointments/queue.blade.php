@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Queue Display -
                {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h2>

            <div class="flex flex-wrap gap-2">
                <!-- Back Button -->
                <a href="{{ route('backend.appointments.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>

                <!-- Today Button -->
                <a href="{{ route('backend.appointments.today') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'Calendar', 'class' => 'w-4 h-4'])
                    Today
                </a>
            </div>

        </div>

        <!-- Queue Columns -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach (['scheduled', 'checked_in', 'in_progress'] as $status)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-gray-800 text-white text-center py-2">
                        <h4 class="font-semibold">{{ ucfirst(str_replace('_', ' ', $status)) }}</h4>
                    </div>
                    <div class="p-3 space-y-2 min-h-[200px]">
                        @if (isset($appointments[$status]) && $appointments[$status]->count())
                            @foreach ($appointments[$status] as $appointment)
                                <div
                                    class="flex justify-between items-center bg-gray-50 border border-gray-200 rounded px-3 py-2 hover:bg-gray-100 transition">
                                    <div class="space-y-1 text-sm">
                                        <div class="font-medium">
                                            {{ date('h:i A', strtotime($appointment->appointment_time)) }} -
                                            {{ $appointment->patient->full_name }}</div>
                                        <div class="text-gray-500 text-xs">
                                            ({{ $appointment->doctor->user->full_name ?? '-' }})
                                        </div>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs rounded 
                                                    {{ $appointment->priority == 'urgent' ? 'bg-red-200 text-red-800' : ($appointment->priority == 'high' ? 'bg-yellow-200 text-yellow-800' : 'bg-cyan-200 text-cyan-800') }}">
                                        {{ ucfirst($appointment->priority) }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-400 text-center py-8">No appointments</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection
