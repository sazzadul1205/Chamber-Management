@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Doctor Calendar</h1>
                <p class="text-gray-600 mt-1">
                    View appointments and availability for {{ $doctor->full_name }}
                </p>
                <div class="mt-2 flex items-center gap-2 text-sm">
                    <span class="px-2 py-1 rounded-full {{ $doctor->status_color }}">
                        {{ ucfirst(str_replace('_', ' ', $doctor->status)) }}
                    </span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">{{ $doctor->specialization }}</span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">{{ $startDate->format('F Y') }}</span>
                </div>
            </div>
            <div class="flex space-x-2">
                <div class="flex rounded-md shadow-sm">
                    <a href="{{ route('backend.doctors.calendar', ['doctor' => $doctor, 'month' => $startDate->copy()->subMonth()->month, 'year' => $startDate->copy()->subMonth()->year]) }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-l-md text-sm font-medium text-gray-700 hover:text-gray-900">
                        Previous
                    </a>
                    <span class="px-4 py-2 bg-white border-t border-b border-gray-300 text-sm font-medium text-gray-700">
                        {{ $startDate->format('F Y') }}
                    </span>
                    <a href="{{ route('backend.doctors.calendar', ['doctor' => $doctor, 'month' => $startDate->copy()->addMonth()->month, 'year' => $startDate->copy()->addMonth()->year]) }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-r-md text-sm font-medium text-gray-700 hover:text-gray-900">
                        Next
                    </a>
                </div>
                <a href="{{ route('backend.doctors.show', $doctor) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Back to Profile
                </a>
            </div>
        </div>

        <!-- CALENDAR LEGEND -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-100 border border-blue-300 rounded-sm mr-2"></div>
                    <span class="text-xs text-gray-600">Working Day</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-100 border border-green-300 rounded-sm mr-2"></div>
                    <span class="text-xs text-gray-600">Appointments</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-100 border border-red-300 rounded-sm mr-2"></div>
                    <span class="text-xs text-gray-600">Leave / Day Off</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-100 border border-yellow-300 rounded-sm mr-2"></div>
                    <span class="text-xs text-gray-600">Today</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-gray-100 border border-gray-300 rounded-sm mr-2"></div>
                    <span class="text-xs text-gray-600">Non-working Day</span>
                </div>
            </div>
        </div>

        <!-- CALENDAR GRID -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Weekday Headers -->
            <div class="grid grid-cols-7 border-b border-gray-200">
                @php
                    $weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                @endphp
                @foreach ($weekdays as $weekday)
                    <div class="py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $weekday }}
                    </div>
                @endforeach
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 divide-x divide-y divide-gray-200">
                @php
                    $firstDayOfMonth = $startDate->copy()->startOfMonth();
                    $lastDayOfMonth = $startDate->copy()->endOfMonth();
                    $startDay = $firstDayOfMonth->copy()->startOfWeek();
                    $endDay = $lastDayOfMonth->copy()->endOfWeek();
                    $currentDay = $startDay->copy();
                @endphp

                @while ($currentDay <= $endDay)
                    @php
                        $dateStr = $currentDay->format('Y-m-d');
                        $dayData = $calendar[$dateStr] ?? null;
                        $isCurrentMonth = $currentDay->month == $startDate->month;
                        $isToday = $currentDay->isToday();
                        $appointments = $dayData['appointments'] ?? collect();
                        $leave = $dayData['leave'] ?? null;
                        $isWorkingDay = $dayData['is_working_day'] ?? false;
                        $isOnLeave = $dayData['is_on_leave'] ?? false;

                        // Determine cell background
                        $bgClass = 'bg-white';
                        if (!$isCurrentMonth) {
                            $bgClass = 'bg-gray-50';
                        } elseif ($isToday) {
                            $bgClass = 'bg-yellow-50';
                        } elseif ($isOnLeave) {
                            $bgClass = 'bg-red-50';
                        } elseif ($isWorkingDay) {
                            $bgClass = 'bg-blue-50';
                        }
                    @endphp

                    <div class="min-h-32 {{ $bgClass }} border-r border-b border-gray-200 p-2">
                        <!-- Day Header -->
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium {{ $isCurrentMonth ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $currentDay->format('j') }}
                            </span>
                            @if ($isToday)
                                <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">
                                    Today
                                </span>
                            @endif
                        </div>

                        <!-- Leave Status -->
                        @if ($leave)
                            <div class="mb-2">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                                {{ $leave->status == 'approved' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    @if ($leave->status == 'approved')
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Leave
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Pending
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- Appointments List -->
                        @if ($appointments->count() > 0)
                            <div class="space-y-1">
                                @foreach ($appointments->take(2) as $appointment)
                                    <div class="text-xs p-1 bg-green-100 border border-green-200 rounded">
                                        <div class="font-medium truncate">
                                            {{ $appointment->patient->full_name ?? 'N/A' }}
                                        </div>
                                        <div class="text-green-700">
                                            {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                        </div>
                                        <span
                                            class="text-xs {{ $appointment->status == 'scheduled' ? 'text-blue-600' : 'text-gray-600' }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                @endforeach

                                @if ($appointments->count() > 2)
                                    <div class="text-xs text-center text-gray-500">
                                        +{{ $appointments->count() - 2 }} more
                                    </div>
                                @endif
                            </div>
                        @elseif($isCurrentMonth && $isWorkingDay && !$isOnLeave)
                            <div class="text-xs text-gray-500 italic mt-2">
                                No appointments
                            </div>
                        @elseif($isCurrentMonth && !$isWorkingDay)
                            <div class="text-xs text-gray-400 italic mt-2">
                                Day off
                            </div>
                        @endif
                    </div>

                    @php
                        $currentDay->addDay();
                    @endphp
                @endwhile
            </div>
        </div>

        <!-- APPOINTMENTS SUMMARY -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Monthly Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Appointments</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ collect($calendar)->sum(fn($day) => $day['appointments']->count()) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Working Days</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ collect($calendar)->where('is_working_day', true)->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Leaves</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ collect($calendar)->where('is_on_leave', true)->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('backend.doctors.schedule-management', $doctor) }}"
                        class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Manage Schedule</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <a href="{{ route('backend.doctors.my-leaves') }}"
                        class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Apply for Leave</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Upcoming Events</h3>
                <div class="space-y-3">
                    @php
                        $upcomingLeaves = $doctor
                            ->leaves()
                            ->whereDate('leave_date', '>=', now())
                            ->where('status', 'approved')
                            ->orderBy('leave_date')
                            ->take(3)
                            ->get();
                    @endphp

                    @if ($upcomingLeaves->count() > 0)
                        @foreach ($upcomingLeaves as $leave)
                            <div class="flex items-center p-2 bg-red-50 border border-red-100 rounded">
                                <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <div class="text-xs font-medium text-red-800">
                                        {{ $leave->formatted_date }} - {{ $leave->formatted_type }}
                                    </div>
                                    <div class="text-xs text-red-600 truncate">{{ $leave->reason }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 italic">No upcoming leaves</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .min-h-32 {
            min-height: 8rem;
        }
    </style>
@endsection
