@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Appointment Queue Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.appointments.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'list', 'class' => 'w-4 h-4'])
                    <span>List View</span>
                </a>

                <a href="{{ route('backend.appointments.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    <span>New Appointment</span>
                </a>

                <button onclick="refreshQueue()"
                    class="flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Refresh', 'class' => 'w-4 h-4'])
                    <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded mb-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded mb-2">{{ session('error') }}</div>
        @endif

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total in Queue</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Queue',
                            'class' => 'w-5 h-5 text-blue-600',
                        ])
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    For {{ \Carbon\Carbon::parse($selectedDate)->format('M d') }}
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Waiting</p>
                        <p class="text-2xl font-bold text-orange-800">{{ $stats['waiting'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Waiting',
                            'class' => 'w-5 h-5 text-orange-600',
                        ])
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Scheduled & Checked-In
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">In Progress</p>
                        <p class="text-2xl font-bold text-red-800">{{ $stats['in_progress'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Play',
                            'class' => 'w-5 h-5 text-red-600',
                        ])
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Currently with doctor
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Avg Wait Time</p>
                        <p class="text-2xl font-bold text-green-800">{{ $stats['avg_wait_time'] ?? '0 min' }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Waiting',
                            'class' => 'w-5 h-5 text-green-600',
                        ])
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Average waiting time
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" action="{{ route('backend.appointments.queue') }}"
                class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>
                    <select name="doctor_id"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        <option value="">All Doctors</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ $selectedDoctor == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->user->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" value="{{ $selectedDate }}"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select name="priority"
                        class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        <option value="">All Priorities</option>
                        <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                        Apply Filters
                    </button>

                    <a href="?date={{ now()->toDateString() }}"
                        class="flex-1 bg-teal-600 hover:bg-teal-700 text-white rounded-md px-4 py-2 font-medium text-center">
                        Today
                    </a>
                </div>
            </form>
        </div>

        <!-- QUEUE -->
        <div class="bg-white rounded-lg shadow">
            <!-- Queue Header -->
            <div class="px-6 py-4 border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Appointment Queue
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('l, F d, Y') }}
                            @if ($selectedDoctor)
                                •
                                {{ $doctors->where('id', $selectedDoctor)->first()->user->full_name ?? 'Selected Doctor' }}
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">
                            Displaying: {{ $appointments->count() }} appointments
                        </span>
                        <button onclick="toggleCompactView()"
                            class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50">
                            Compact View
                        </button>
                    </div>
                </div>
            </div>

            <!-- Queue List -->
            <div class="divide-y divide-gray-200">
                @php $queueNumber = 1; @endphp
                @forelse($appointments as $appointment)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-150 
                        {{ $appointment->status === 'completed' ? 'bg-green-50' : '' }}
                        {{ $appointment->priority === 'urgent' ? 'border-r-4 border-r-red-500' : ($appointment->priority === 'high' ? 'border-r-4 border-r-yellow-500' : '') }}"
                        data-appointment-id="{{ $appointment->id }}" data-status="{{ $appointment->status }}"
                        data-priority="{{ $appointment->priority }}">

                        <div class="flex items-start justify-between">
                            <!-- Queue Number & Basic Info -->
                            <div class="flex items-start gap-4">
                                <div class="flex flex-col items-center">
                                    <span class="text-2xl font-bold text-gray-700">{{ $queueNumber++ }}</span>
                                    <span class="text-xs text-gray-500 mt-1">Queue</span>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <a href="{{ route('backend.patients.show', $appointment->patient_id) }}"
                                            class="font-semibold text-gray-900 hover:text-blue-600 hover:underline">
                                            {{ $appointment->patient->full_name }}
                                        </a>
                                        <span class="text-xs font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">
                                            #{{ $appointment->appointment_code }}
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                        <div class="flex items-center gap-1">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Doctor',
                                                'class' => 'w-4 h-4',
                                            ])
                                            <span>{{ $appointment->doctor->user->full_name ?? 'No Doctor Assigned' }}</span>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Clock',
                                                'class' => 'w-4 h-4',
                                            ])
                                            <span>{{ date('h:i A', strtotime($appointment->appointment_time)) }}</span>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Type',
                                                'class' => 'w-4 h-4',
                                            ])
                                            <span>{{ $appointment->appointment_type_text }}</span>
                                        </div>
                                    </div>

                                    @if ($appointment->chief_complaint)
                                        <div class="mt-2 text-sm text-gray-700 max-w-2xl">
                                            <span class="font-medium">Chief Complaint:</span>
                                            {{ Str::limit($appointment->chief_complaint, 150) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Status & Actions -->
                            <div class="flex flex-col items-end gap-2">
                                <!-- Status and Priority Badges -->
                                <div class="flex gap-2">
                                    {!! $appointment->status_badge !!}
                                    {!! $appointment->priority_badge !!}
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-1">
                                    <a href="{{ route('backend.appointments.show', $appointment) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                        title="View Details">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-5 h-5',
                                        ])
                                    </a>

                                    <!-- Status-Specific Actions -->
                                    @if ($appointment->status === 'scheduled')
                                        <form method="POST"
                                            action="{{ route('backend.appointments.check-in', $appointment) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs"
                                                title="Check-In Patient">
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
                                                title="Start Consultation">
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
                                        <button type="button"
                                            class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs"
                                            onclick="showRescheduleModal('{{ $appointment->id }}', '{{ $appointment->appointment_date->format('Y-m-d') }}', '{{ date('H:i', strtotime($appointment->appointment_time)) }}')"
                                            title="Reschedule">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Reschedule',
                                                'class' => 'w-5 h-5',
                                            ])
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                            @include('partials.sidebar-icon', [
                                'name' => 'B_Empty',
                                'class' => 'w-8 h-8 text-gray-400',
                            ])
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-1">No appointments in queue</h4>
                        <p class="text-gray-500 mb-4">No appointments found for the selected filters</p>
                        <a href="?date={{ now()->toDateString() }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium">
                            View Today's Queue
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- LEGEND -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Queue Legend</h4>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span class="text-sm text-gray-600">Scheduled</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-indigo-500"></div>
                            <span class="text-sm text-gray-600">Checked-In</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-orange-500"></div>
                            <span class="text-sm text-gray-600">In Progress</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-sm text-gray-600">Completed</span>
                        </div>
                    </div>
                </div>

                <div class="text-sm text-gray-500">
                    Last updated: <span id="lastUpdated">{{ now()->format('h:i:s A') }}</span>
                </div>
            </div>
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
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Time</label>
                        <input type="time" name="appointment_time" id="appointmentTime" required
                            class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
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
        let isCompactView = false;

        function refreshQueue() {
            window.location.reload();
        }

        function toggleCompactView() {
            isCompactView = !isCompactView;
            const appointments = document.querySelectorAll('[data-appointment-id]');
            const button = document.querySelector('button[onclick="toggleCompactView()"]');

            appointments.forEach(appointment => {
                if (isCompactView) {
                    appointment.querySelector('.chief-complaint')?.classList.add('hidden');
                    appointment.querySelector('.doctor-info')?.classList.add('hidden');
                    button.textContent = 'Detailed View';
                } else {
                    appointment.querySelector('.chief-complaint')?.classList.remove('hidden');
                    appointment.querySelector('.doctor-info')?.classList.remove('hidden');
                    button.textContent = 'Compact View';
                }
            });
        }

        function showRescheduleModal(appointmentId, currentDate, currentTime) {
            currentAppointmentId = appointmentId;

            document.getElementById('rescheduleForm').action = `/backend/appointments/${appointmentId}/reschedule`;

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
            document.getElementById('appointmentDate').value = currentDate;
            document.getElementById('appointmentTime').value = currentTime;

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
        }

        // Auto-refresh queue every 60 seconds
        setInterval(refreshQueue, 60000);

        // Update timestamp
        function updateTimestamp() {
            document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
        }

        setInterval(updateTimestamp, 1000);

        // Close modal when clicking outside
        document.getElementById('rescheduleModal').addEventListener('click', function(e) {
            if (e.target === this) closeRescheduleModal();
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('rescheduleModal').classList.contains('hidden')) {
                closeRescheduleModal();
            }

            if (e.key === 'r' || e.key === 'R') {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    refreshQueue();
                }
            }
        });
    </script>

    <style>
        /* Queue item animations */
        [data-appointment-id] {
            transition: all 0.2s ease;
        }

        [data-priority="urgent"] {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.9;
            }
        }

        /* Status color indicators */
        [data-status="scheduled"] .status-badge {
            background-color: #dbeafe;
            color: #1e40af;
        }

        [data-status="checked_in"] .status-badge {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        [data-status="in_progress"] .status-badge {
            background-color: #ffedd5;
            color: #9a3412;
        }

        [data-status="completed"] .status-badge {
            background-color: #dcfce7;
            color: #166534;
        }
    </style>
@endsection
