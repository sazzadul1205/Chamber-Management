@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Appointment Queue</h2>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('backend.appointments.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'list', 'class' => 'w-4 h-4'])
                    List View
                </a>

                <a href="{{ route('backend.appointments.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'Add-Circle', 'class' => 'w-4 h-4'])
                    New Appointment
                </a>
            </div>

        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="text-sm font-medium">Doctor</label>
                    <select name="doctor_id" class="w-full border rounded px-3 py-2">
                        <option value="">All Doctors</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected($selectedDoctor == $doctor->id)>
                                {{ $doctor->user->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Date</label>
                    <input type="date" name="date" value="{{ $selectedDate }}"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div class="flex items-end">
                    <button class="w-full bg-blue-600 text-white rounded px-3 py-2">
                        Apply
                    </button>
                </div>

                <div class="flex items-end">
                    <a href="?date={{ now()->toDateString() }}"
                        class="w-full bg-teal-500 text-white rounded px-3 py-2 text-center">
                        Today
                    </a>
                </div>
            </form>
        </div>

        <!-- FIFO Queue -->
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-lg font-semibold mb-4">
                Queue for {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
            </h3>

            @forelse($appointments as $appointment)
                <div
                    class="mb-3 p-4 rounded-lg border-l-4
                {{ $appointment->priority === 'urgent' ? 'border-red-500' : 'border-blue-500' }}
                {{ $appointment->status === 'completed' ? 'opacity-60' : '' }}
                bg-gray-50">

                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold text-gray-800">
                                {{ $appointment->patient->full_name }}
                                <span class="text-xs text-gray-500 ml-2">
                                    #{{ $appointment->appointment_code }}
                                </span>
                            </div>

                            <div class="text-sm text-gray-600">
                                Doctor: {{ $appointment->doctor->user->full_name ?? '-' }}
                            </div>

                            <div class="text-sm text-gray-500 mt-1">
                                Queue No:
                                <span class="font-bold text-gray-800">
                                    {{ $appointment->queue_no ?? '—' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-1">
                            {!! $appointment->status_badge !!}
                            {!! $appointment->priority_badge !!}

                            <a href="{{ route('backend.appointments.show', $appointment) }}"
                                class="px-3 py-1 bg-blue-600 text-white rounded text-xs">
                                View
                            </a>
                        </div>
                    </div>

                    @if ($appointment->chief_complaint)
                        <div class="mt-2 text-sm text-gray-500">
                            {{ Str::limit($appointment->chief_complaint, 120) }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-gray-500 text-sm text-center py-6">
                    No appointments found
                </div>
            @endforelse
        </div>

    </div>
@endsection
