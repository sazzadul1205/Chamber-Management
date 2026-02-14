@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Appointments Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.appointments.today') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'Today', 'class' => 'w-4 h-4 text-white'])
                    <span>Today's Appointments</span>
                </a>

                <a href="{{ route('backend.appointments.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4 text-white'])
                    <span>New Appointment</span>
                </a>
            </div>
        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded mb-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded mb-2">{{ session('error') }}</div>
        @endif

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.appointments.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient / code"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <select name="status"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Status</option>
                    @foreach (App\Models\Appointment::statuses() as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <select name="doctor_id"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Doctors</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->user->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                    Filter
                </button>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Code</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Patient</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Doctor</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Date & Time</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Priority</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ ($appointments->currentPage() - 1) * $appointments->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="bg-cyan-100 text-cyan-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $appointment->appointment_code }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('backend.patients.show', $appointment->patient_id) }}"
                                    class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                    {{ $appointment->patient->full_name }}
                                </a>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                {{ $appointment->doctor->user->full_name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="font-medium">{{ $appointment->appointment_date->format('d/m/Y') }}</span>
                                <br>
                                <span class="text-xs text-gray-500">
                                    {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">{{ $appointment->appointment_type_text }}</td>

                            <td class="px-4 py-3 text-sm">{!! $appointment->status_badge !!}</td>

                            <td class="px-4 py-3 text-sm">{!! $appointment->priority_badge !!}</td>

                            <td class="px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-1">
                                    @php
                                        $btnBaseClasses =
                                            'relative flex items-center justify-center px-2 py-1 text-white rounded text-xs w-8 h-8 group';
                                    @endphp

                                    <!-- View -->
                                    <a href="{{ route('backend.appointments.show', $appointment) }}"
                                        class="{{ $btnBaseClasses }} bg-blue-500 hover:bg-blue-600"
                                        data-tooltip="View Appointment">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            View Appointment
                                        </span>
                                    </a>

                                    @if ($appointment->status !== 'completed')
                                        <!-- Reschedule -->
                                        <button type="button"
                                            class="{{ $btnBaseClasses }} bg-yellow-400 hover:bg-yellow-500"
                                            onclick="showRescheduleModal('{{ $appointment->id }}')"
                                            data-tooltip="Reschedule Appointment">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Reschedule',
                                                'class' => 'w-4 h-4',
                                            ])
                                            <span
                                                class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                                Reschedule Appointment
                                            </span>
                                        </button>
                                    @endif

                                    @if ($appointment->status === 'scheduled')
                                        <!-- Check-In -->
                                        <form method="POST"
                                            action="{{ route('backend.appointments.check-in', $appointment) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="{{ $btnBaseClasses }} bg-indigo-600 hover:bg-indigo-700"
                                                data-tooltip="Check-In">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Tick',
                                                    'class' => 'w-4 h-4',
                                                ])
                                                <span
                                                    class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                                    Check-In
                                                </span>
                                            </button>
                                        </form>
                                    @endif

                                    @if ($appointment->status === 'checked_in')
                                        <!-- Start -->
                                        <form method="POST"
                                            action="{{ route('backend.appointments.start', $appointment) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="{{ $btnBaseClasses }} bg-orange-500 hover:bg-orange-600"
                                                data-tooltip="Start Appointment">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Play',
                                                    'class' => 'w-4 h-4',
                                                ])
                                                <span
                                                    class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                                    Start Appointment
                                                </span>
                                            </button>
                                        </form>
                                    @endif

                                    @if (in_array($appointment->status, ['checked_in', 'in_progress']))
                                        <!-- Complete -->
                                        <form method="POST"
                                            action="{{ route('backend.appointments.complete', $appointment) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="{{ $btnBaseClasses }} bg-green-600 hover:bg-green-700"
                                                data-tooltip="Complete Appointment">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Tick',
                                                    'class' => 'w-4 h-4',
                                                ])
                                                <span
                                                    class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                                    Complete Appointment
                                                </span>
                                            </button>
                                        </form>
                                    @endif

                                    @if (in_array($appointment->status, ['scheduled', 'checked_in']))
                                        <!-- No Show -->
                                        <form method="POST"
                                            action="{{ route('backend.appointments.no-show', $appointment) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="{{ $btnBaseClasses }} bg-red-600 hover:bg-red-700"
                                                data-tooltip="Mark as No Show">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Cross',
                                                    'class' => 'w-4 h-4',
                                                ])
                                                <span
                                                    class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                                    Mark as No Show
                                                </span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>


                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No appointments found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$appointments" />
        </div>

        @include('backend.appointments.online-bookings')

    </div>

    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-20">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Reschedule Appointment</h3>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="rescheduleForm" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Date</label>
                        <input type="date" name="appointment_date" id="appointmentDate" required
                            class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        @error('appointment_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Time</label>
                        <input type="time" name="appointment_time" id="appointmentTime" required
                            class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        @error('appointment_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="text-sm text-gray-600 mt-4 p-3 bg-gray-50 rounded-md">
                        <p class="font-medium mb-1">Current Schedule:</p>
                        <p id="currentSchedule" class="text-gray-700"></p>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg flex justify-end space-x-3">
                <button type="button" onclick="closeRescheduleModal()"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="button" onclick="submitRescheduleForm()"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentAppointmentId = null;

        function showRescheduleModal(appointmentId) {
            currentAppointmentId = appointmentId;

            // Get appointment data (you might want to fetch this via AJAX in a real app)
            const appointmentRow = document.querySelector(`tr[data-appointment-id="${appointmentId}"]`);
            const currentDate = appointmentRow?.querySelector('.appointment-date')?.textContent || '';
            const currentTime = appointmentRow?.querySelector('.appointment-time')?.textContent || '';

            // Set form action
            document.getElementById('rescheduleForm').action = `/backend/appointments/${appointmentId}/reschedule`;

            // Set current schedule info
            document.getElementById('currentSchedule').textContent = `${currentDate} at ${currentTime}`;

            // Show modal
            document.getElementById('rescheduleModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeRescheduleModal() {
            document.getElementById('rescheduleModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            currentAppointmentId = null;
        }

        function submitRescheduleForm() {
            document.getElementById('rescheduleForm').submit();
            closeRescheduleModal();
        }

        // Close modal when clicking outside
        document.getElementById('rescheduleModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRescheduleModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('rescheduleModal').classList.contains('hidden')) {
                closeRescheduleModal();
            }
        });

        // Add data-appointment-id to each row for easier selection
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('tbody tr').forEach((row, index) => {
                row.setAttribute('data-appointment-id', index + 1);
            });
        });
    </script>
@endsection
