@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Appointment Calendar</h2>
            <div class="flex gap-2">
                <a href="{{ route('appointments.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_List', 'class' => 'w-4 h-4'])
                    List View
                </a>
                <a href="{{ route('appointments.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Plus', 'class' => 'w-4 h-4'])
                    New Appointment
                </a>
            </div>
        </div>

        <!-- Calendar Controls -->
        <div class="bg-white rounded shadow p-4">
            <h3 class="text-lg font-semibold mb-4">Calendar Controls</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Select Doctor</label>
                    <select name="doctor_id" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="">All Doctors</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ $selectedDoctor == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->user->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Select Date</label>
                    <input type="date" name="date" value="{{ $selectedDate }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="md:col-span-1 flex flex-col justify-end">
                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white rounded px-3 py-2 text-sm font-medium transition">
                        View
                    </button>
                </div>
                <div class="md:col-span-1 flex flex-col justify-end">
                    <a href="?date={{ date('Y-m-d') }}"
                        class="w-full bg-teal-500 hover:bg-teal-600 text-white rounded px-3 py-2 text-sm font-medium text-center transition">
                        Today
                    </a>
                </div>
                <div class="md:col-span-1 flex flex-col justify-end">
                    <a href="?date={{ date('Y-m-d', strtotime($selectedDate . ' -1 day')) }}"
                        class="w-full bg-gray-200 hover:bg-gray-300 rounded px-3 py-2 text-sm font-medium text-center transition">
                        Previous
                    </a>
                </div>
            </form>
        </div>

        <!-- Appointments Table -->
        <div class="bg-white rounded shadow p-4">
            <h3 class="text-lg font-semibold mb-4">Appointments for {{ date('F d, Y', strtotime($selectedDate)) }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 w-32">Time</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Appointments</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($timeSlots as $time => $appointments)
                            @php
                                $displayTime = date('h:i A', strtotime($time));
                                $isPast = strtotime($selectedDate . ' ' . $time) < time();
                            @endphp
                            <tr class="{{ $isPast ? 'bg-gray-100' : '' }}">
                                <td class="px-4 py-2 font-semibold">{{ $displayTime }}</td>
                                <td class="px-4 py-2">
                                    @if($appointments->count() > 0)
                                        @foreach($appointments as $appointment)
                                            <div class="mb-2 p-3 rounded border-l-4 border-blue-500 bg-gray-50">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <strong>{{ $appointment->appointment_code }}</strong> -
                                                        <a href="{{ route('patients.show', $appointment->patient_id) }}"
                                                            class="text-blue-600 hover:underline">
                                                            {{ $appointment->patient->full_name }}
                                                        </a>
                                                        <span class="text-gray-500 text-sm">
                                                            ({{ $appointment->doctor->user->full_name ?? '-' }})
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        {!! $appointment->status_badge !!}
                                                        {!! $appointment->priority_badge !!}
                                                        <a href="{{ route('appointments.show', $appointment) }}"
                                                            class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs transition">
                                                            View
                                                        </a>
                                                    </div>
                                                </div>
                                                @if($appointment->chief_complaint)
                                                    <div class="text-gray-500 mt-1 text-sm">
                                                        {{ Str::limit($appointment->chief_complaint, 100) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500">No appointments</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <style>
        /* Tailwind-based Appointment Card Borders for urgency/status */
        .appointment-card.urgent {
            border-left-color: #dc2626;
        }

        .appointment-card.completed {
            border-left-color: #16a34a;
        }
    </style>
@endsection