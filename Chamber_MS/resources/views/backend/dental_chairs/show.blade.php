@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">

        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Chair Details: {{ $dentalChair->name }}</h2>
            <div class="flex flex-wrap gap-2">
                @if ($dentalChair->is_available)
                    <form action="{{ route('backend.dental-chairs.quick-status-change', $dentalChair->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="occupied">
                        <button type="submit"
                            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg flex items-center gap-2 shadow">
                            <i class="fas fa-user"></i> Mark Occupied
                        </button>
                    </form>
                @elseif($dentalChair->is_occupied)
                    <form action="{{ route('backend.dental-chairs.quick-status-change', $dentalChair->id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="status" value="available">
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg flex items-center gap-2 shadow">
                            <i class="fas fa-check"></i> Mark Available
                        </button>
                    </form>
                @endif

                <a href="{{ route('backend.dental-chairs.edit', $dentalChair->id) }}"
                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex items-center gap-2 shadow">
                    <i class="fas fa-edit"></i> Edit
                </a>

                <a href="{{ route('backend.dental-chairs.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg flex items-center gap-2 shadow">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Chair Details Card -->
            <div class="bg-white border rounded-xl shadow p-6">
                <h4 class="text-lg font-semibold mb-4">Chair Details</h4>
                <table class="w-full text-sm text-gray-700">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="py-2 text-left font-medium w-1/3">Chair Code:</th>
                            <td><span
                                    class="px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $dentalChair->chair_code }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Chair Name:</th>
                            <td>{{ $dentalChair->name }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Location:</th>
                            <td>{{ $dentalChair->location ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Status:</th>
                            <td>
                                <span class="px-2 py-1 rounded text-white bg-{{ $dentalChair->status_color }}">
                                    {{ $dentalChair->status_name }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Last Used:</th>
                            <td>{{ $dentalChair->formatted_last_used }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Total Appointments:</th>
                            <td>{{ $dentalChair->appointments->count() }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Created At:</th>
                            <td>{{ $dentalChair->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Updated At:</th>
                            <td>{{ $dentalChair->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Status & Notes Cards -->
            <div class="space-y-4">

                <!-- Current Status Card -->
                <div class="border rounded-xl shadow overflow-hidden">
                    <div class="px-4 py-2 bg-{{ $dentalChair->status_color }} text-white font-medium">
                        Current Status
                    </div>
                    <div class="p-4">
                        @if ($dentalChair->currentAppointment)
                            <h5 class="font-semibold mb-3">Currently Occupied</h5>
                            <div class="bg-blue-50 p-4 rounded space-y-2 text-sm text-gray-800">
                                <p><strong>Patient:</strong>
                                    {{ $dentalChair->currentAppointment->patient->full_name ?? 'N/A' }}</p>
                                <p><strong>Doctor:</strong>
                                    {{ $dentalChair->currentAppointment->doctor->user->full_name ?? 'N/A' }}</p>
                                <p><strong>Appointment Time:</strong>
                                    {{ $dentalChair->currentAppointment->appointment_time }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($dentalChair->currentAppointment->status) }}</p>
                            </div>
                        @else
                            <h5 class="text-center text-gray-500 font-medium">Currently Available</h5>
                            <p class="text-center text-gray-400 text-sm">No patient currently assigned</p>
                        @endif
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="border rounded-xl shadow overflow-hidden">
                    <div class="px-4 py-2 bg-gray-100 font-medium">Notes</div>
                    <div class="p-4 text-sm text-gray-700">
                        @if ($dentalChair->notes)
                            <p>{{ $dentalChair->notes }}</p>
                        @else
                            <p class="text-gray-400">No notes provided</p>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        <!-- Recent Appointments -->
        <div class="bg-white border rounded-xl shadow p-6 mt-6">
            <h4 class="text-lg font-semibold mb-4">Recent Appointments</h4>
            @if ($dentalChair->appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-gray-700 border-collapse">
                        <thead class="bg-gray-100 text-gray-700 font-medium">
                            <tr>
                                <th class="py-2 px-3 text-left">Date</th>
                                <th class="py-2 px-3 text-left">Time</th>
                                <th class="py-2 px-3 text-left">Patient</th>
                                <th class="py-2 px-3 text-left">Doctor</th>
                                <th class="py-2 px-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($dentalChair->appointments as $appointment)
                                <tr>
                                    <td class="py-2 px-3">{{ $appointment->appointment_date->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3">{{ $appointment->appointment_time }}</td>
                                    <td class="py-2 px-3">{{ $appointment->patient->full_name ?? 'N/A' }}</td>
                                    <td class="py-2 px-3">{{ $appointment->doctor->user->full_name ?? 'N/A' }}</td>
                                    <td class="py-2 px-3">
                                        <span
                                            class="px-2 py-1 rounded text-white 
                                        {{ $appointment->status == 'completed' ? 'bg-green-500' : ($appointment->status == 'cancelled' ? 'bg-red-500' : 'bg-yellow-500') }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-400 text-sm text-center">No appointment history</p>
            @endif
        </div>

    </div>
@endsection
