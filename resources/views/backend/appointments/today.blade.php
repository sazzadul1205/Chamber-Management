@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Today's Appointments</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.appointments.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Back', 'class' => 'w-5 h-5'])
                    <span>Back to All Appointments</span>
                </a>

                <a href="{{ route('backend.appointments.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-5 h-5'])
                    <span>New Appointment</span>
                </a>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Scheduled</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['scheduled'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Calendar',
                            'class' => 'w-5 h-5 text-blue-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Checked-In</p>
                        <p class="text-2xl font-bold text-indigo-800">{{ $stats['checked_in'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Tick',
                            'class' => 'w-5 h-5 text-indigo-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">In Progress</p>
                        <p class="text-2xl font-bold text-orange-800">{{ $stats['in_progress'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Play',
                            'class' => 'w-5 h-5 text-orange-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Completed</p>
                        <p class="text-2xl font-bold text-green-800">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Tick',
                            'class' => 'w-5 h-5 text-green-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Today</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Calendar',
                            'class' => 'w-5 h-5 text-gray-600',
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded mb-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded mb-2">{{ session('error') }}</div>
        @endif

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Code</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Patient</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Doctor</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Time</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $counter = 0;
                        $hasAppointments = false;
                    @endphp

                    @foreach (['scheduled', 'checked_in', 'in_progress', 'completed'] as $status)
                        @if ($todayAppointments->has($status) && $todayAppointments->get($status)->count() > 0)
                            @php $hasAppointments = true; @endphp

                            <!-- Status Group Header -->
                            <tr class="bg-gray-50">
                                <td colspan="7" class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $statusConfig = [
                                                'scheduled' => [
                                                    'color' => 'bg-blue-100 text-blue-800',
                                                    'icon' => 'Calendar',
                                                ],
                                                'checked_in' => [
                                                    'color' => 'bg-indigo-100 text-indigo-800',
                                                    'icon' => 'B_Tick',
                                                ],
                                                'in_progress' => [
                                                    'color' => 'bg-orange-100 text-orange-800',
                                                    'icon' => 'B_Play',
                                                ],
                                                'completed' => [
                                                    'color' => 'bg-green-100 text-green-800',
                                                    'icon' => 'B_Check',
                                                ],
                                            ];
                                            $config = $statusConfig[$status] ?? [
                                                'color' => 'bg-gray-100 text-gray-800',
                                                'icon' => 'B_Dot',
                                            ];
                                        @endphp

                                        <p
                                            class="px-3 py-1 rounded-full text-xs font-medium flex items-center {{ $config['color'] }}">
                                            @include('partials.sidebar-icon', [
                                                'name' => $config['icon'],
                                                'class' => 'w-3 h-3 inline mr-1',
                                            ])
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            <span class="ml-1 bg-white px-2 py-0.5 rounded-full">
                                                {{ $todayAppointments->get($status)->count() }}
                                            </span>
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            @foreach ($todayAppointments->get($status) as $appointment)
                                @php $counter++; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-500">
                                        {{ $counter }}
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
                                        @if ($appointment->priority === 'high')
                                            <span class="ml-2 px-1.5 py-0.5 text-xs bg-red-100 text-red-800 rounded">
                                                High Priority
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex items-center">
                                            @if ($appointment->doctor?->user?->profile_photo)
                                                <img src="{{ asset($appointment->doctor->user->profile_photo) }}"
                                                    alt="{{ $appointment->doctor->user->full_name }}"
                                                    class="w-6 h-6 rounded-full mr-2">
                                            @endif
                                            <span>{{ $appointment->doctor->user->full_name ?? '-' }}</span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-medium">
                                            {{ date('h:i A', strtotime($appointment->appointment_time)) }}</div>
                                        <div class="text-xs text-gray-500">{{ $appointment->appointment_type_text }}</div>
                                    </td>

                                    <td class="px-4 py-3 text-sm">{!! $appointment->status_badge !!}</td>

                                    <td class="px-4 py-3 text-center text-sm">
                                        <div class="flex justify-center gap-1">
                                            <a href="{{ route('backend.appointments.show', $appointment) }}"
                                                class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                                title="View Details">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_View',
                                                    'class' => 'w-5 h-5',
                                                ])
                                            </a>

                                            @if ($appointment->status === 'scheduled')
                                                <form method="POST"
                                                    action="{{ route('backend.appointments.check-in', $appointment) }}"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs"
                                                        title="Check-In">
                                                        @include('partials.sidebar-icon', [
                                                            'name' => 'B_Tick',
                                                            'class' => 'w-5 h-5',
                                                        ])
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($appointment->status === 'checked_in')
                                                <form method="POST"
                                                    action="{{ route('backend.appointments.start', $appointment) }}"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-2 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded text-xs"
                                                        title="Start Appointment">
                                                        @include('partials.sidebar-icon', [
                                                            'name' => 'B_Play',
                                                            'class' => 'w-5 h-5',
                                                        ])
                                                    </button>
                                                </form>
                                            @endif

                                            @if (in_array($appointment->status, ['checked_in', 'in_progress']))
                                                <form method="POST"
                                                    action="{{ route('backend.appointments.complete', $appointment) }}"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs"
                                                        title="Complete Appointment">
                                                        @include('partials.sidebar-icon', [
                                                            'name' => 'B_Tick',
                                                            'class' => 'w-5 h-5',
                                                        ])
                                                    </button>
                                                </form>
                                            @endif

                                            @if (!in_array($appointment->status, ['completed', 'no_show']))
                                                <!-- Reschedule Button -->
                                                <button type="button"
                                                    class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs"
                                                    onclick="showRescheduleModal('{{ $appointment->id }}', '{{ $appointment->appointment_date->format('Y-m-d') }}', '{{ date('H:i', strtotime($appointment->appointment_time)) }}')"
                                                    title="Reschedule Appointment">
                                                    @include('partials.sidebar-icon', [
                                                        'name' => 'B_Reschedule',
                                                        'class' => 'w-5 h-5',
                                                    ])
                                                </button>
                                            @endif

                                            @if (in_array($appointment->status, ['scheduled', 'checked_in']))
                                                <form method="POST"
                                                    action="{{ route('backend.appointments.no-show', $appointment) }}"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs"
                                                        title="Mark as No Show">
                                                        @include('partials.sidebar-icon', [
                                                            'name' => 'B_Cross',
                                                            'class' => 'w-5 h-5',
                                                        ])
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach

                    @if (!$hasAppointments)
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No appointments for today
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

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

        function showRescheduleModal(appointmentId, currentDate, currentTime) {
            currentAppointmentId = appointmentId;

            // Set form action
            document.getElementById('rescheduleForm').action = `/backend/appointments/${appointmentId}/reschedule`;

            // Set current schedule info
            const formattedDate = new Date(currentDate).toLocaleDateString('en-US', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            const formattedTime = new Date(`2000-01-01T${currentTime}`).toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });

            document.getElementById('currentSchedule').textContent = `${formattedDate} at ${formattedTime}`;

            // Set current values in form
            document.getElementById('appointmentDate').value = currentDate;
            document.getElementById('appointmentTime').value = currentTime;

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

        // Set minimum date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('appointmentDate');
            if (dateInput) {
                dateInput.min = today;
            }
        });
    </script>
@endsection
