@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                Appointment Details: {{ $appointment->appointment_code }}
            </h2>

            <div class="flex flex-wrap gap-2">
                @if (!in_array($appointment->status, ['completed', 'no_show']))
                    <button type="button" data-modal-target="rescheduleModal"
                        class="flex justify-center items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition px-4 py-2 w-40">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Reschedule',
                            'class' => 'w-4 h-4',
                        ])
                        <span class="text-center flex-1">Reschedule</span>
                    </button>
                @endif

                <a href="{{ route('backend.appointments.calendar') }}"
                    class="flex justify-center items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow transition px-4 py-2 w-40">
                    @include('partials.sidebar-icon', [
                        'name' => 'Calendar',
                        'class' => 'w-4 h-4',
                    ])
                    <span class="text-center flex-1">Calendar</span>
                </a>

                <a href="{{ route('backend.appointments.index') }}"
                    class="flex justify-center items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition px-4 py-2 w-40">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Back',
                        'class' => 'w-4 h-4',
                    ])
                    <span class="text-center flex-1">Back to List</span>
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Appointment Info Card -->
            <div class="bg-white border rounded-xl shadow p-6 space-y-4">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 flex items-center justify-center mb-3">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Calendar',
                            'class' => 'w-8 h-8 text-blue-600',
                        ])
                    </div>

                    <h3 class="text-xl font-semibold">Appointment #{{ $appointment->appointment_code }}</h3>

                    <div class="flex items-center justify-center gap-2 mt-2">
                        {!! $appointment->status_badge !!}
                        {!! $appointment->priority_badge !!}
                    </div>
                </div>

                <table class="w-full text-sm text-gray-700 mt-4">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="py-2 text-left font-medium w-1/3">Date & Time:</th>
                            <td>
                                {{ $appointment->appointment_date->format('d/m/Y') }}
                                at {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                            </td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Duration:</th>
                            <td>{{ $appointment->duration_text }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Queue No:</th>
                            <td>{{ $appointment->queue_no ?? 'Not assigned' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Appointment Type:</th>
                            <td>{{ $appointment->appointment_type_text }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Schedule Type:</th>
                            <td>{{ ucfirst($appointment->schedule_type) }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Dental Chair:</th>
                            <td>{{ $appointment->chair->name ?? 'Not Assigned' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Created By:</th>
                            <td>{{ $appointment->creator->full_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Last Updated:</th>
                            <td>{{ $appointment->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Patient & Doctor Info -->
            <div class="space-y-6">
                <!-- Patient Card -->
                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Patient',
                            'class' => 'w-5 h-5 text-blue-600',
                        ])
                        Patient Information
                    </h4>

                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 font-semibold">
                                {{ substr($appointment->patient->full_name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('backend.patients.show', $appointment->patient_id) }}"
                                class="font-semibold text-gray-800 hover:text-blue-600 transition block">
                                {{ $appointment->patient->full_name }}
                            </a>
                            <p class="text-sm text-gray-500">{{ $appointment->patient->patient_code }}</p>
                        </div>
                    </div>

                    <table class="w-full text-sm text-gray-700">
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <th class="py-1 text-left font-medium w-1/2">Phone:</th>
                                <td>{{ $appointment->patient->phone }}</td>
                            </tr>
                            <tr>
                                <th class="py-1 text-left font-medium">Email:</th>
                                <td>{{ $appointment->patient->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="py-1 text-left font-medium">Age/Gender:</th>
                                <td>{{ $appointment->patient->age_text }} / {{ $appointment->patient->gender_text }}</td>
                            </tr>
                            <tr>
                                <th class="py-1 text-left font-medium">Emergency Contact:</th>
                                <td>{{ $appointment->patient->emergency_contact ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Doctor Card -->
                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Doctor',
                            'class' => 'w-5 h-5 text-green-600',
                        ])
                        Doctor Information
                    </h4>

                    @if ($appointment->doctor)
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-green-600 font-semibold">
                                    {{ substr($appointment->doctor->user->full_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $appointment->doctor->user->full_name }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $appointment->doctor->specialization ?? 'General Dentist' }}</p>
                            </div>
                        </div>

                        <div class="text-sm text-gray-700">
                            <div class="flex items-center gap-1 mb-1">
                                <span class="text-gray-500">License No:</span>
                                <span>{{ $appointment->doctor->license_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-400">No doctor assigned</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats & Timeline -->
            <div class="space-y-6">
                <!-- Status Stats -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-500 text-white rounded-xl shadow p-4 text-center">
                        <h6 class="text-xs font-medium">Duration</h6>
                        <h3 class="text-xl font-bold">{{ $appointment->duration_text }}</h3>
                    </div>
                    <div class="bg-green-500 text-white rounded-xl shadow p-4 text-center">
                        <h6 class="text-xs font-medium">Queue No</h6>
                        <h3 class="text-xl font-bold">{{ $appointment->queue_no ?? 'N/A' }}</h3>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4">Appointment Timeline</h4>

                    <div class="space-y-4">
                        @php
                            $timelineEvents = [
                                [
                                    'icon' => '📅',
                                    'title' => 'Scheduled',
                                    'time' => $appointment->created_at,
                                    'color' => 'gray',
                                    'completed' => true,
                                ],
                                [
                                    'icon' => '✅',
                                    'title' => 'Checked In',
                                    'time' => $appointment->checked_in_time,
                                    'color' => 'blue',
                                    'completed' => !is_null($appointment->checked_in_time),
                                ],
                                [
                                    'icon' => '⚡',
                                    'title' => 'Started',
                                    'time' => $appointment->started_time,
                                    'color' => 'yellow',
                                    'completed' => !is_null($appointment->started_time),
                                ],
                                [
                                    'icon' => '🎉',
                                    'title' => 'Completed',
                                    'time' => $appointment->completed_time,
                                    'color' => 'green',
                                    'completed' => !is_null($appointment->completed_time),
                                ],
                            ];
                        @endphp

                        @foreach ($timelineEvents as $event)
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full 
                                    {{ $event['completed'] ? "bg-{$event['color']}-100" : 'bg-gray-100' }} 
                                    flex items-center justify-center">
                                    {{ $event['icon'] }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <span
                                            class="font-medium {{ $event['completed'] ? 'text-gray-800' : 'text-gray-400' }}">
                                            {{ $event['title'] }}
                                        </span>
                                        @if ($event['time'])
                                            <span class="text-xs text-gray-500">
                                                {{ $event['time']->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if (!$event['completed'])
                                        <p class="text-xs text-gray-400 mt-1">Pending</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Notes & Complaints -->
                @if ($appointment->chief_complaint || $appointment->notes)
                    <div class="bg-white border rounded-xl shadow p-6">
                        <h4 class="text-lg font-semibold mb-4">Notes & Complaints</h4>

                        @if ($appointment->chief_complaint)
                            <div class="mb-4">
                                <h5 class="font-medium text-gray-700 mb-1 flex items-center gap-1">
                                    <span class="text-red-500">•</span>
                                    Chief Complaint
                                </h5>
                                <p class="text-sm text-gray-600 bg-red-50 p-3 rounded">
                                    {{ $appointment->chief_complaint }}
                                </p>
                            </div>
                        @endif

                        @if ($appointment->notes)
                            <div>
                                <h5 class="font-medium text-gray-700 mb-1 flex items-center gap-1">
                                    <span class="text-blue-500">•</span>
                                    Additional Notes
                                </h5>
                                <p class="text-sm text-gray-600 bg-blue-50 p-3 rounded">
                                    {{ $appointment->notes }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Cancellation Reason -->
        @if ($appointment->reason_cancellation)
            <div class="bg-red-50 border border-red-200 rounded-xl shadow p-6">
                <h4 class="text-lg font-semibold text-red-800 mb-2">Cancellation Reason</h4>
                <p class="text-red-700">{{ $appointment->reason_cancellation }}</p>
                @if ($appointment->cancelled_at)
                    <p class="text-sm text-red-600 mt-2">
                        Cancelled on {{ $appointment->cancelled_at->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>
        @endif
    </div>

    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Calendar',
                        'class' => 'w-5 h-5 text-yellow-500',
                    ])
                    Reschedule Appointment
                </h3>
            </div>

            <form action="{{ route('backend.appointments.reschedule', $appointment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Date</label>
                        <input type="date" name="appointment_date"
                            value="{{ $appointment->appointment_date->format('Y-m-d') }}" required
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Time</label>
                        <input type="time" name="appointment_time" value="{{ $appointment->appointment_time }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-2">
                    <button type="button" data-modal-hide="rescheduleModal"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Modal functionality
                const rescheduleBtn = document.querySelector('[data-modal-target="rescheduleModal"]');
                const rescheduleModal = document.getElementById('rescheduleModal');
                const closeBtns = document.querySelectorAll('[data-modal-hide="rescheduleModal"]');

                if (rescheduleBtn) {
                    rescheduleBtn.addEventListener('click', () => {
                        rescheduleModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    });

                    closeBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            rescheduleModal.classList.add('hidden');
                            document.body.style.overflow = '';
                        });
                    });

                    // Close when clicking outside
                    rescheduleModal.addEventListener('click', (e) => {
                        if (e.target === rescheduleModal) {
                            rescheduleModal.classList.add('hidden');
                            document.body.style.overflow = '';
                        }
                    });

                    // Close with Escape key
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && !rescheduleModal.classList.contains('hidden')) {
                            rescheduleModal.classList.add('hidden');
                            document.body.style.overflow = '';
                        }
                    });
                }

                // Set minimum date for rescheduling
                const dateInput = document.querySelector('input[name="appointment_date"]');
                if (dateInput) {
                    const today = new Date().toISOString().split('T')[0];
                    dateInput.min = today;
                }
            });
        </script>
    
@endsection
