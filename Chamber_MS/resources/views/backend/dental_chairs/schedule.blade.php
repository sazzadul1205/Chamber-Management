@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header & Date Filter -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-2">
            <h2 class="text-2xl font-semibold">Chair Schedule - {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</h2>
            <div class="flex gap-2 items-center">
                <form method="GET" action="{{ route('backend.dental-chairs.schedule') }}">
                    <input type="date" name="date" value="{{ $date }}" class="border rounded px-2 py-1 text-sm"
                        onchange="this.form.submit()">
                </form>
                <a href="{{ route('backend.dental-chairs.index') }}"
                    class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm flex items-center gap-1">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        @if ($chairs->isEmpty())
            <div class="p-4 bg-blue-50 text-blue-700 rounded flex items-center gap-2">
                <i class="fas fa-info-circle"></i> No dental chairs found.
            </div>
        @else
            <div class="space-y-4">
                @foreach ($chairs as $chair)
                    <div class="border rounded shadow-sm overflow-hidden">

                        <!-- Chair Header -->
                        <div class="px-4 py-2 flex justify-between items-center bg-gray-50">
                            <h5 class="flex items-center gap-2 font-medium text-gray-800">
                                <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded">{{ $chair->chair_code }}</span>
                                {{ $chair->name }}
                                <small class="text-gray-500">{{ $chair->location ?? 'No location' }}</small>
                            </h5>
                            <span class="px-2 py-1 rounded text-white bg-{{ $chair->status_color }}">
                                {{ $chair->status_name }}
                            </span>
                        </div>

                        <!-- Appointments Table -->
                        <div class="p-4">
                            @if ($chair->appointments->isEmpty())
                                <p class="text-gray-500 text-sm">No appointments scheduled for today.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm table-auto border-collapse">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="py-2 px-3 text-left border-b w-1/6">Time</th>
                                                <th class="py-2 px-3 text-left border-b w-1/4">Patient</th>
                                                <th class="py-2 px-3 text-left border-b w-1/4">Doctor</th>
                                                <th class="py-2 px-3 text-left border-b w-1/5">Type</th>
                                                <th class="py-2 px-3 text-left border-b w-1/6">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($chair->appointments as $appointment)
                                                <tr class="border-b">
                                                    <td class="py-2 px-3">{{ $appointment->appointment_time }}</td>
                                                    <td class="py-2 px-3">
                                                        <strong>{{ $appointment->patient->full_name ?? 'N/A' }}</strong>
                                                        @if ($appointment->chief_complaint)
                                                            <br>
                                                            <small
                                                                class="text-gray-500">{{ Str::limit($appointment->chief_complaint, 50) }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        {{ $appointment->doctor->user->full_name ?? 'N/A' }}</td>
                                                    <td class="py-2 px-3">
                                                        <span
                                                            class="px-2 py-1 rounded bg-gray-300 text-gray-800">{{ ucfirst($appointment->appointment_type) }}</span>
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <span
                                                            class="px-2 py-1 rounded text-white bg-{{ $appointment->status == 'completed' ? 'green' : ($appointment->status == 'cancelled' ? 'red' : 'yellow-500') }}">
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
