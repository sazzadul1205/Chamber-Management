@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Queue Display</h2>
                <p class="text-gray-600 mt-1">
                    <span class="font-medium">{{ \Carbon\Carbon::parse($date)->format('l') }}</span>,
                    {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.appointments.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Back', 'class' => 'w-4 h-4'])
                    <span>Back to All Appointments</span>
                </a>

                <a href="{{ route('backend.appointments.today') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'Calendar', 'class' => 'w-4 h-4'])
                    <span>Today's Queue</span>
                </a>

                <button onclick="refreshDisplay()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Refresh', 'class' => 'w-4 h-4'])
                    <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- STATS SUMMARY -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Scheduled</p>
                        <p class="text-2xl font-bold text-blue-800">
                            {{ isset($appointments['scheduled']) ? $appointments['scheduled']->count() : 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Schedule',
                            'class' => 'w-5 h-5 text-blue-600',
                        ])
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Waiting for check-in
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Checked-In</p>
                        <p class="text-2xl font-bold text-indigo-800">
                            {{ isset($appointments['checked_in']) ? $appointments['checked_in']->count() : 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Tick',
                            'class' => 'w-5 h-5 text-indigo-600',
                        ])
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Ready for consultation
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">In Progress</p>
                        <p class="text-2xl font-bold text-orange-800">
                            {{ isset($appointments['in_progress']) ? $appointments['in_progress']->count() : 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Play',
                            'class' => 'w-5 h-5 text-orange-600',
                        ])
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Currently with doctor
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

        <!-- DATE NAVIGATION -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-700">Viewing:</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- QUEUE COLUMNS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @php
                $columnConfigs = [
                    'scheduled' => [
                        'title' => 'Scheduled',
                        'color' => 'blue',
                        'icon' => 'Schedule',
                        'description' => 'Patients waiting for check-in',
                    ],
                    'checked_in' => [
                        'title' => 'Checked-In',
                        'color' => 'indigo',
                        'icon' => 'B_Tick',
                        'description' => 'Ready for consultation',
                    ],
                    'in_progress' => [
                        'title' => 'In Progress',
                        'color' => 'orange',
                        'icon' => 'B_Play',
                        'description' => 'Currently with doctor',
                    ],
                ];
            @endphp

            @foreach ($columnConfigs as $status => $config)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Column Header -->
                    <div class="bg-gray-900 text-white px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                    @include('partials.sidebar-icon', [
                                        'name' => $config['icon'],
                                        'class' => 'w-5 h-5 text-white',
                                    ])
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $config['title'] }}</h3>
                                    <p class="text-sm text-gray-300">{{ $config['description'] }}</p>
                                </div>
                            </div>
                            <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">
                                {{ isset($appointments[$status]) ? $appointments[$status]->count() : 0 }}
                            </span>
                        </div>
                    </div>

                    <!-- Column Body -->
                    <div class="p-4 space-y-3 min-h-[400px] max-h-[600px] overflow-y-auto">
                        @if (isset($appointments[$status]) && $appointments[$status]->count())
                            @foreach ($appointments[$status] as $appointment)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-white hover:shadow-md transition-all duration-200"
                                    data-appointment-id="{{ $appointment->id }}" data-status="{{ $status }}">

                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">
                                                {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                            </span>
                                            <h4 class="font-semibold text-gray-900 mt-1">
                                                {{ $appointment->patient->full_name }}
                                            </h4>
                                        </div>

                                        <div class="flex flex-col items-end gap-2">
                                            <!-- Priority Badge -->
                                            <span
                                                class="px-2.5 py-1 text-xs font-medium rounded-full
                                                @switch($appointment->priority)
                                                    @case('urgent')
                                                        bg-red-100 text-red-800
                                                        @break
                                                    @case('high')
                                                        bg-yellow-100 text-yellow-800
                                                        @break
                                                    @default
                                                        bg-cyan-100 text-cyan-800
                                                @endswitch">
                                                {{ ucfirst($appointment->priority) }}
                                            </span>

                                            <!-- Status Actions -->
                                            <div class="flex gap-1">
                                                @if ($status === 'scheduled')
                                                    <form method="POST"
                                                        action="{{ route('backend.appointments.check-in', $appointment) }}"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs"
                                                            title="Check-In">
                                                            @include('partials.sidebar-icon', [
                                                                'name' => 'B_Tick',
                                                                'class' => 'w-3 h-3',
                                                            ])
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($status === 'checked_in')
                                                    <form method="POST"
                                                        action="{{ route('backend.appointments.start', $appointment) }}"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="px-2 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded text-xs"
                                                            title="Start">
                                                            @include('partials.sidebar-icon', [
                                                                'name' => 'B_Play',
                                                                'class' => 'w-3 h-3',
                                                            ])
                                                        </button>
                                                    </form>
                                                @endif

                                                <a href="{{ route('backend.appointments.show', $appointment) }}"
                                                    class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                                    title="View Details">
                                                    @include('partials.sidebar-icon', [
                                                        'name' => 'B_View',
                                                        'class' => 'w-3 h-3',
                                                    ])
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Info -->
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                @if ($appointment->doctor?->user?->profile_photo)
                                                    <img src="{{ asset($appointment->doctor->user->profile_photo) }}"
                                                        alt="{{ $appointment->doctor->user->full_name }}"
                                                        class="w-6 h-6 rounded-full">
                                                @endif
                                                <span
                                                    class="text-gray-600">{{ $appointment->doctor->user->full_name ?? '-' }}</span>
                                            </div>

                                            <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                                                {{ $appointment->appointment_type_text }}
                                            </span>
                                        </div>

                                        @if ($appointment->notes)
                                            <div class="mt-2 text-xs text-gray-500">
                                                <span class="font-medium">Note:</span>
                                                {{ Str::limit($appointment->notes, 50) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Appointment Code -->
                                    <div class="mt-3 text-center">
                                        <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded text-gray-600">
                                            {{ $appointment->appointment_code }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'B_Empty',
                                        'class' => 'w-8 h-8 text-gray-300',
                                    ])
                                </div>
                                <p class="font-medium">No appointments</p>
                                <p class="text-sm mt-1">All clear in this queue</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- LEGEND -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Queue Status</h4>
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
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Priority Levels</h4>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-cyan-500"></div>
                            <span class="text-sm text-gray-600">Normal</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <span class="text-sm text-gray-600">High</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span class="text-sm text-gray-600">Urgent</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div>
                        <span>Display updated: </span>
                        <span id="lastUpdated">{{ now()->format('h:i:s A') }}</span>
                    </div>
                    <div>
                        <span>Auto-refresh: </span>
                        <span id="refreshCountdown">30</span>
                        <span>s</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function refreshDisplay() {
            window.location.reload();
        }

        // Auto-refresh countdown
        let countdown = 30;
        const countdownElement = document.getElementById('refreshCountdown');

        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                countdown = 30;
                refreshDisplay();
            }
        }, 1000);

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

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'r' || e.key === 'R') {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    refreshDisplay();
                }
            }

            // Navigation shortcuts
            if (e.key === 'ArrowLeft' && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                document.querySelector('a[href*="subDay"]')?.click();
            }

            if (e.key === 'ArrowRight' && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                document.querySelector('a[href*="addDay"]')?.click();
            }

            if (e.key === 't' || e.key === 'T') {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    document.querySelector('a[href*="today"]')?.click();
                }
            }

            if (e.key === 'Escape') {
                // Close any open modals
                const modals = document.querySelectorAll('[id$="Modal"]');
                modals.forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            }
        });

        // Add sound notifications for new appointments
        function playNotificationSound() {
            const audio = new Audio('/path/to/notification-sound.mp3');
            audio.volume = 0.3;
            audio.play().catch(e => console.log('Audio play failed:', e));
        }

        // Simulate real-time updates (in a real app, this would be WebSockets)
        let lastAppointmentCount = {
            scheduled: {{ isset($appointments['scheduled']) ? $appointments['scheduled']->count() : 0 }},
            checked_in: {{ isset($appointments['checked_in']) ? $appointments['checked_in']->count() : 0 }},
            in_progress: {{ isset($appointments['in_progress']) ? $appointments['in_progress']->count() : 0 }}
        };

        // Check for updates every 15 seconds
        setInterval(checkForUpdates, 15000);

        // Highlight urgent appointments
        document.addEventListener('DOMContentLoaded', function() {
            const urgentAppointments = document.querySelectorAll('[data-priority="urgent"]');
            urgentAppointments.forEach(appointment => {
                appointment.style.animation = 'pulse 2s infinite';
            });
        });
    </script>

    <style>
        /* Custom scrollbar for queue columns */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Animation for urgent appointments */
        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }
        }

        /* Hover effects */
        .hover\\:shadow-md {
            transition: all 0.2s ease;
        }

        .hover\\:shadow-md:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        /* Status indicator animation */
        [data-status="scheduled"] {
            border-left: 4px solid #3b82f6;
        }

        [data-status="checked_in"] {
            border-left: 4px solid #6366f1;
        }

        [data-status="in_progress"] {
            border-left: 4px solid #f97316;
        }
    </style>
@endsection
