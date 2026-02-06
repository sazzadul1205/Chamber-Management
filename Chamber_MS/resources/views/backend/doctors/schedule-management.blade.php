@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Doctor Schedule Management</h1>
                <p class="text-gray-600 mt-1">
                    Manage working hours and availability for Dr. {{ $doctor->full_name }}
                </p>
                <div class="mt-2 flex items-center gap-2 text-sm">
                    <span class="px-2 py-1 rounded-full {{ $doctor->status_color }}">
                        {{ ucfirst(str_replace('_', ' ', $doctor->status)) }}
                    </span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">Code: {{ $doctor->doctor_code }}</span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">{{ $doctor->specialization }}</span>
                </div>
            </div>
            <a href="{{ route('backend.doctors.show', $doctor) }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back to Profile
            </a>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.doctors.update-schedule', $doctor) }}" method="POST">
                @csrf
                @method('POST')

                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Weekly Schedule Configuration</h3>
                        <p class="text-sm text-gray-600">
                            Set working days, hours, and appointment limits for each day of the week.
                        </p>
                    </div>

                    <!-- SCHEDULE TABLE -->
                    <div class="overflow-hidden border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Day
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Active
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Working Hours
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Max Appointments
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Slot Duration
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($daysOfWeek as $day)
                                    @php
                                        $schedule = $schedules[$day];
                                        $dayName = ucfirst($day);
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900">{{ $dayName }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="schedules[{{ $day }}][is_active]"
                                                value="1" @checked($schedule->is_active ?? false)
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <input type="hidden" name="schedules[{{ $day }}][day_of_week]"
                                                value="{{ $day }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <input type="time" name="schedules[{{ $day }}][start_time]"
                                                    value="{{ $schedule->start_time ?? '09:00' }}"
                                                    class="w-28 border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-gray-400">to</span>
                                                <input type="time" name="schedules[{{ $day }}][end_time]"
                                                    value="{{ $schedule->end_time ?? '17:00' }}"
                                                    class="w-28 border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" name="schedules[{{ $day }}][max_appointments]"
                                                value="{{ $schedule->max_appointments ?? 20 }}" min="1"
                                                max="50"
                                                class="w-20 border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select name="schedules[{{ $day }}][slot_duration]"
                                                class="w-32 border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="15" @selected(($schedule->slot_duration ?? 30) == 15)>15 minutes</option>
                                                <option value="30" @selected(($schedule->slot_duration ?? 30) == 30)>30 minutes</option>
                                                <option value="45" @selected(($schedule->slot_duration ?? 30) == 45)>45 minutes</option>
                                                <option value="60" @selected(($schedule->slot_duration ?? 30) == 60)>60 minutes</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- CURRENT SCHEDULE SUMMARY -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Current Active Schedule</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $activeSchedules = $doctor->activeSchedules;
                                $workingDays = $activeSchedules
                                    ->pluck('day_of_week')
                                    ->map(fn($day) => ucfirst($day))
                                    ->toArray();
                            @endphp

                            @if ($activeSchedules->count() > 0)
                                <div>
                                    <p class="text-sm text-blue-700">
                                        <span class="font-medium">Working Days:</span>
                                        {{ implode(', ', $workingDays) }}
                                    </p>
                                    <p class="text-sm text-blue-700 mt-1">
                                        <span class="font-medium">Total Working Hours:</span>
                                        {{ $activeSchedules->sum(fn($s) => (strtotime($s->end_time) - strtotime($s->start_time)) / 3600) }}
                                        hours/week
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700">
                                        <span class="font-medium">Max Weekly Appointments:</span>
                                        {{ $activeSchedules->sum('max_appointments') }}
                                    </p>
                                    <p class="text-sm text-blue-700 mt-1">
                                        <span class="font-medium">Consultation Fee:</span>
                                        {{ $doctor->formatted_consultation_fee }}
                                    </p>
                                </div>
                            @else
                                <p class="text-sm text-blue-700">No active schedule configured yet.</p>
                            @endif
                        </div>
                    </div>

                    <!-- FORM ACTIONS -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('backend.doctors.show', $doctor) }}"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save Schedule
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('backend.doctors.calendar', $doctor) }}"
                class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:border-blue-300 hover:shadow-md transition">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">View Calendar</h3>
                        <p class="text-sm text-gray-500 mt-1">Check appointments and availability</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('backend.doctors.leave-requests', ['doctor_id' => $doctor->id]) }}"
                class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:border-yellow-300 hover:shadow-md transition">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Leave Requests</h3>
                        <p class="text-sm text-gray-500 mt-1">Manage leave applications</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('backend.doctors.show', $doctor) }}"
                class="bg-white p-6 rounded-lg shadow border border-gray-200 hover:border-green-300 hover:shadow-md transition">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Doctor Profile</h3>
                        <p class="text-sm text-gray-500 mt-1">View complete profile details</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle time fields based on active checkbox
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="schedules"]');

            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const inputs = row.querySelectorAll('input[type="time"], input[type="number"], select');

                function toggleInputs() {
                    const isChecked = checkbox.checked;
                    inputs.forEach(input => {
                        input.disabled = !isChecked;
                        input.required = isChecked;
                    });
                }

                // Initial state
                toggleInputs();

                // Change event
                checkbox.addEventListener('change', toggleInputs);
            });

            // Time validation
            const timeInputs = document.querySelectorAll('input[type="time"]');
            timeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const startInput = row.querySelector('input[name$="[start_time]"]');
                    const endInput = row.querySelector('input[name$="[end_time]"]');

                    if (startInput.value && endInput.value && startInput.value >= endInput.value) {
                        alert('End time must be after start time');
                        this.value = '';
                        this.focus();
                    }
                });
            });
        });
    </script>
@endsection
