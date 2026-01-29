@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Appointments</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.appointments.today') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'Today', 'class' => 'w-4 h-4'])
                    Today
                </a>

                <a href="{{ route('backend.appointments.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    New Appointment
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient / code"
                    class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    @foreach (App\Models\Appointment::statuses() as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <select name="doctor_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Doctors</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->user->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <input type="date" name="date" value="{{ request('date') }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-2">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
                    Filter
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">Code</th>
                        <th class="px-3 py-2 text-left text-sm">Patient</th>
                        <th class="px-3 py-2 text-left text-sm">Doctor</th>
                        <th class="px-3 py-2 text-left text-sm">Date & Time</th>
                        <th class="px-3 py-2 text-left text-sm">Type</th>
                        <th class="px-3 py-2 text-left text-sm">Status</th>
                        <th class="px-3 py-2 text-left text-sm">Priority</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
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
                                {{ $appointment->appointment_date->format('d/m/Y') }}<br>
                                <span class="text-xs text-gray-500">
                                    {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                </span>
                            </td>

                            <td class="px-3 py-2">
                                {{ $appointment->appointment_type_text }}
                            </td>

                            <td class="px-3 py-2">
                                {!! $appointment->status_badge !!}
                            </td>

                            <td class="px-3 py-2">
                                {!! $appointment->priority_badge !!}
                            </td>

                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.appointments.show', $appointment) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    @if ($appointment->status !== 'completed')
                                        <!-- Reschedule Button -->
                                        <button type="button"
                                            class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs"
                                            data-modal-target="reschedule-modal-{{ $appointment->id }}"
                                            data-modal-toggle="reschedule-modal-{{ $appointment->id }}"
                                            title="Reschedule Appointment">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Reschedule',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </button>
                                    @endif


                                    <!-- Check-In -->
                                    @if ($appointment->status === 'scheduled')
                                        <form method="POST"
                                            action="{{ route('backend.appointments.check-in', $appointment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs"
                                                title="Check-In">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Tick',
                                                    'class' => 'w-4 h-4',
                                                ])
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Start -->
                                    @if ($appointment->status === 'checked_in')
                                        <form method="POST"
                                            action="{{ route('backend.appointments.start', $appointment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white rounded text-xs"
                                                title="Start Appointment">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Play',
                                                    'class' => 'w-4 h-4',
                                                ])
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Complete -->
                                    @if (in_array($appointment->status, ['checked_in', 'in_progress']))
                                        <form method="POST"
                                            action="{{ route('backend.appointments.complete', $appointment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-green-600 hover:bg-green-700 text-white rounded text-xs"
                                                title="Complete Appointment">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Tick',
                                                    'class' => 'w-4 h-4',
                                                ])
                                            </button>
                                        </form>
                                    @endif

                                    {{-- No Show --}}
                                    @if (in_array($appointment->status, ['scheduled', 'checked_in']))
                                        <form method="POST"
                                            action="{{ route('backend.appointments.no-show', $appointment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded text-xs"
                                                title="Mark as No Show">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Cross',
                                                    'class' => 'w-4 h-4',
                                                ])
                                            </button>
                                        </form>
                                    @endif
                                </div>


                            </td>
                        </tr>


                        <!-- Modal -->
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
                                            <label class="block text-sm font-medium text-gray-700 mb-1">New Date</label>
                                            <input type="date" name="appointment_date"
                                                value="{{ $appointment->appointment_date->format('Y-m-d') }}" required
                                                class="w-full border rounded px-3 py-2">
                                            @error('appointment_date')
                                                <p class="text-red-500 text-xs">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">New Time</label>
                                            <input type="time" name="appointment_time"
                                                value="{{ date('H:i', strtotime($appointment->appointment_time)) }}"
                                                required class="w-full border rounded px-3 py-2">
                                            @error('appointment_time')
                                                <p class="text-red-500 text-xs">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="flex justify-end gap-2 mt-4">
                                            <button type="button" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
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
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                No appointments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$appointments" />
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('button[data-modal-toggle]').forEach(btn => {
                const modalId = btn.getAttribute('data-modal-toggle');
                const modal = document.getElementById(modalId);

                btn.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                });
            });

            document.querySelectorAll('button[data-modal-hide]').forEach(btn => {
                const modalId = btn.getAttribute('data-modal-hide');
                const modal = document.getElementById(modalId);

                btn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            });
        });
    </script>
@endsection
