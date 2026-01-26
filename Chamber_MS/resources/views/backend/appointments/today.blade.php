@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header & Stats -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Today's Appointments</h2>

            <div class="flex flex-wrap gap-3">
                <div class="bg-gray-100 px-4 py-2 rounded text-sm">
                    <span class="font-medium">Scheduled:</span> {{ $stats['scheduled'] }}
                </div>
                <div class="bg-indigo-100 px-4 py-2 rounded text-sm">
                    <span class="font-medium">Checked-In:</span> {{ $stats['checked_in'] }}
                </div>
                <div class="bg-orange-100 px-4 py-2 rounded text-sm">
                    <span class="font-medium">In Progress:</span> {{ $stats['in_progress'] }}
                </div>
                <div class="bg-green-100 px-4 py-2 rounded text-sm">
                    <span class="font-medium">Completed:</span> {{ $stats['completed'] }}
                </div>
                <div class="bg-gray-300 px-4 py-2 rounded text-sm">
                    <span class="font-medium">Total:</span> {{ $stats['total'] }}
                </div>
            </div>

            <!-- Back Button -->
            <a href="{{ route('backend.appointments.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>

        <!-- Appointments Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">Code</th>
                        <th class="px-3 py-2 text-left text-sm">Patient</th>
                        <th class="px-3 py-2 text-left text-sm">Doctor</th>
                        <th class="px-3 py-2 text-left text-sm">Time</th>
                        <th class="px-3 py-2 text-left text-sm">Status</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse(['scheduled','checked_in','in_progress','completed'] as $status)
                        @foreach ($todayAppointments->get($status, collect()) as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 text-xs rounded bg-cyan-100 text-cyan-800">
                                        {{ $appointment->appointment_code }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('backend.patients.show', $appointment->patient_id) }}"
                                        class="text-blue-600 hover:underline">
                                        {{ $appointment->patient->full_name }}
                                    </a>
                                </td>
                                <td class="px-3 py-2">
                                    {{ $appointment->doctor->user->full_name ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                </td>
                                <td class="px-3 py-2">
                                    {!! $appointment->status_badge !!}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('backend.appointments.show', $appointment) }}"
                                            class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                            title="View">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_View',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </a>

                                        @if (!in_array($appointment->status, ['completed', 'no_show']))
                                            <!-- Reschedule -->
                                            <button type="button"
                                                class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs"
                                                data-modal-target="reschedule-modal-{{ $appointment->id }}"
                                                data-modal-toggle="reschedule-modal-{{ $appointment->id }}"
                                                title="Reschedule">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7H16M8 11H16M8 15H12M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </button>

                                            <!-- No Show -->
                                            <form method="POST"
                                                action="{{ route('backend.appointments.no-show', $appointment) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="w-8 h-8 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded text-xs"
                                                    title="No Show">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Reschedule Modal -->
                            @if (!in_array($appointment->status, ['completed', 'no_show']))
                                <div id="reschedule-modal-{{ $appointment->id }}"
                                    class="hidden fixed inset-0 z-50 overflow-y-auto">
                                    <div class="flex items-center justify-center min-h-screen px-4">
                                        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                                            <h3 class="text-lg font-semibold mb-4">Reschedule Appointment</h3>
                                            <form action="{{ route('backend.appointments.reschedule', $appointment) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">New
                                                        Date</label>
                                                    <input type="date" name="appointment_date"
                                                        value="{{ $appointment->appointment_date->format('Y-m-d') }}"
                                                        required class="w-full border rounded px-3 py-2">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">New
                                                        Time</label>
                                                    <input type="time" name="appointment_time"
                                                        value="{{ $appointment->appointment_time }}" required
                                                        class="w-full border rounded px-3 py-2">
                                                </div>
                                                <div class="flex justify-end gap-2 mt-4">
                                                    <button type="button"
                                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                                        data-modal-hide="reschedule-modal-{{ $appointment->id }}">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-yellow-400 text-white rounded hover:bg-yellow-500">
                                                        Save
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                No appointments for today.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- Modal JS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('button[data-modal-toggle]').forEach(btn => {
                const modalId = btn.getAttribute('data-modal-toggle');
                const modal = document.getElementById(modalId);
                btn.addEventListener('click', () => modal.classList.remove('hidden'));
            });
            document.querySelectorAll('button[data-modal-hide]').forEach(btn => {
                const modalId = btn.getAttribute('data-modal-hide');
                const modal = document.getElementById(modalId);
                btn.addEventListener('click', () => modal.classList.add('hidden'));
            });
        });
    </script>
@endsection
