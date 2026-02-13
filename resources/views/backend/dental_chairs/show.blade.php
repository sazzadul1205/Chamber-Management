@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dental Chair Details</h1>
                <p class="text-gray-600 mt-1">
                    Complete details and statistics for {{ $dentalChair->name }}
                </p>
                <div class="mt-2 flex items-center gap-2 text-sm">
                    <span class="px-2 py-1 rounded-full text-white"
                        style="background-color: {{ $dentalChair->status_color == 'success' ? '#10b981' : ($dentalChair->status_color == 'warning' ? '#f59e0b' : '#ef4444') }}">
                        {{ $dentalChair->status_name }}
                    </span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">Code: {{ $dentalChair->chair_code }}</span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">{{ $dentalChair->location ?? 'No location set' }}</span>
                </div>
            </div>
            <div class="flex space-x-2">
                <!-- Quick Status Change Button -->
                @if ($dentalChair->is_available)
                    <form action="{{ route('backend.dental-chairs.quick-status-change', $dentalChair->id) }}" method="POST"
                        class="inline">
                        @csrf
                        <input type="hidden" name="status" value="occupied">
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 border border-transparent rounded-md text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Mark Occupied
                        </button>
                    </form>
                @elseif($dentalChair->is_occupied)
                    <form action="{{ route('backend.dental-chairs.quick-status-change', $dentalChair->id) }}" method="POST"
                        class="inline">
                        @csrf
                        <input type="hidden" name="status" value="available">
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 border border-transparent rounded-md text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Mark Available
                        </button>
                    </form>
                @endif

                <!-- Edit Button -->
                <a href="{{ route('backend.dental-chairs.edit', $dentalChair->id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 border border-transparent rounded-md text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Chair
                </a>

                <!-- Back to List Button -->
                <a href="{{ route('backend.dental-chairs.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Appointments</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $dentalChair->appointments->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Completed Appointments</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ $dentalChair->appointments->where('status', 'completed')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Today's Appointments</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $todayAppointments ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Usage Rate</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">
                            {{ $usageRate }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEFT COLUMN: CHAIR DETAILS -->
            <div class="lg:col-span-2 space-y-6">
                <!-- CHAIR INFO CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Chair Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Chair Name</p>
                                    <p class="text-base text-gray-900">{{ $dentalChair->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Chair Code</p>
                                    <p class="text-base text-gray-900">{{ $dentalChair->chair_code }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Location</p>
                                    <p class="text-base text-gray-900">{{ $dentalChair->location ?? 'Not specified' }}</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <p class="mt-1">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium text-white"
                                            style="background-color: {{ $dentalChair->status_color == 'success' ? '#10b981' : ($dentalChair->status_color == 'warning' ? '#f59e0b' : '#ef4444') }}">
                                            {{ $dentalChair->status_name }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Last Used</p>
                                    <p class="text-base text-gray-900">{{ $dentalChair->formatted_last_used }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Appointments</p>
                                    <p class="text-base text-gray-900">{{ $dentalChair->appointments->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CURRENT STATUS CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Current Status Details</h3>
                    </div>
                    <div class="p-6">
                        @if ($dentalChair->currentAppointment)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-medium text-blue-800 mb-3">Currently Occupied</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-blue-600">Patient</p>
                                        <p class="text-base text-blue-900">
                                            {{ $dentalChair->currentAppointment->patient->full_name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-600">Doctor</p>
                                        <p class="text-base text-blue-900">
                                            {{ $dentalChair->currentAppointment->doctor->user->full_name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-600">Appointment Time</p>
                                        <p class="text-base text-blue-900">
                                            {{ \Carbon\Carbon::parse($dentalChair->currentAppointment->appointment_time)->format('h:i A') }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-600">Status</p>
                                        <p class="text-base">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-medium
                                                {{ $dentalChair->currentAppointment->status == 'completed'
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($dentalChair->currentAppointment->status == 'cancelled'
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-blue-100 text-blue-800') }}">
                                                {{ ucfirst($dentalChair->currentAppointment->status) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Chair Available</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    No patient currently assigned to this chair.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- NOTES CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Notes</h3>
                    </div>
                    <div class="p-6">
                        @if ($dentalChair->notes)
                            <p class="text-gray-700">{{ $dentalChair->notes }}</p>
                        @else
                            <p class="text-gray-400 text-center py-4">No notes provided for this chair</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: QUICK INFO & ACTIONS -->
            <div class="space-y-6">
                <!-- QUICK ACTIONS -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('backend.appointments.create', ['chair_id' => $dentalChair->id]) }}"
                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="text-sm font-medium text-gray-900">New Appointment</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a href="{{ route('backend.dental-chairs.schedule', ['chair_id' => $dentalChair->id]) }}"
                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-900">View Schedule</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a href="{{ route('backend.dental-chairs.edit', $dentalChair->id) }}"
                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Edit Details</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- TIMELINE STATS -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Creation Details</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Created At</p>
                                <p class="text-base text-gray-900">{{ $dentalChair->created_at->format('d M Y, h:i A') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Last Updated</p>
                                <p class="text-base text-gray-900">{{ $dentalChair->updated_at->format('d M Y, h:i A') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Days in Service</p>
                                <p class="text-base text-gray-900">{{ $dentalChair->created_at->diffInDays(now()) }} days
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT APPOINTMENTS -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Recent Appointments</h3>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Doctor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Service
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dentalChair->appointments->take(5) as $appointment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $appointment->patient->full_name ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $appointment->patient->phone ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $appointment->doctor->user->full_name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->service_type ?? 'Consultation' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $appointment->status == 'completed'
                                        ? 'bg-green-100 text-green-800'
                                        : ($appointment->status == 'cancelled'
                                            ? 'bg-red-100 text-red-800'
                                            : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No recent appointments found for this chair.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
