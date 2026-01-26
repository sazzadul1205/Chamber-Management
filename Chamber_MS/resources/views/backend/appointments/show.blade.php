@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Appointment Details</h2>

            <div class="flex gap-2">
                <a href="{{ route('appointments.edit', $appointment) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Edit', 'class' => 'w-4 h-4'])
                    Edit
                </a>

                <a href="{{ route('appointments.calendar') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Calendar', 'class' => 'w-4 h-4'])
                    Calendar
                </a>
            </div>
        </div>

        <!-- Info Boxes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 bg-gray-50 rounded shadow">
                <p class="text-sm font-medium text-gray-600">Appointment Code</p>
                <p class="text-lg font-semibold">{{ $appointment->appointment_code }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded shadow">
                <p class="text-sm font-medium text-gray-600">Status</p>
                <p class="text-lg">{!! $appointment->status_badge !!} {!! $appointment->priority_badge !!}</p>
            </div>
        </div>

        <!-- Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Left Column -->
            <div class="bg-white rounded shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 w-40">Patient</th>
                            <td class="px-4 py-3">
                                <a href="{{ route('patients.show', $appointment->patient_id) }}"
                                    class="text-blue-600 hover:underline">
                                    {{ $appointment->patient->full_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Doctor</th>
                            <td class="px-4 py-3">{{ $appointment->doctor->user->full_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Dental Chair</th>
                            <td class="px-4 py-3">{{ $appointment->chair->name ?? 'Not Assigned' }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Appointment Type</th>
                            <td class="px-4 py-3">{{ $appointment->appointment_type_text }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Schedule Type</th>
                            <td class="px-4 py-3">{{ ucfirst($appointment->schedule_type) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Right Column -->
            <div class="bg-white rounded shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 w-40">Date & Time</th>
                            <td class="px-4 py-3">
                                {{ $appointment->appointment_date->format('d/m/Y') }}
                                {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                            </td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Duration</th>
                            <td class="px-4 py-3">{{ $appointment->duration_text }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Queue No</th>
                            <td class="px-4 py-3">{{ $appointment->queue_no ?? 'Not assigned' }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Created By</th>
                            <td class="px-4 py-3">{{ $appointment->creator->full_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Last Updated By</th>
                            <td class="px-4 py-3">{{ $appointment->updater->full_name ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Chief Complaint -->
        @if($appointment->chief_complaint)
            <div class="bg-white rounded shadow">
                <div class="px-4 py-3 border-b">
                    <h3 class="text-lg font-semibold">Chief Complaint</h3>
                </div>
                <div class="px-4 py-4 text-gray-700">
                    {{ $appointment->chief_complaint }}
                </div>
            </div>
        @endif

        <!-- Notes -->
        @if($appointment->notes)
            <div class="bg-white rounded shadow">
                <div class="px-4 py-3 border-b">
                    <h3 class="text-lg font-semibold">Notes</h3>
                </div>
                <div class="px-4 py-4 text-gray-700">
                    {{ $appointment->notes }}
                </div>
            </div>
        @endif

        <!-- Cancellation Reason -->
        @if($appointment->reason_cancellation)
            <div class="bg-red-500 text-white rounded shadow">
                <div class="px-4 py-3 border-b">
                    <h3 class="text-lg font-semibold">Cancellation Reason</h3>
                </div>
                <div class="px-4 py-4">
                    {{ $appointment->reason_cancellation }}
                </div>
            </div>
        @endif

        <!-- Timeline -->
        <div class="bg-white rounded shadow">
            <div class="px-4 py-3 border-b">
                <h3 class="text-lg font-semibold">Appointment Timeline</h3>
            </div>
            <div class="px-4 py-4">
                <div class="relative pl-8">
                    <!-- Scheduled -->
                    <div class="relative mb-6">
                        <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-gray-500"></div>
                        <div class="ml-6">
                            <h6 class="font-semibold">Scheduled</h6>
                            <p class="text-gray-500 text-sm">{{ $appointment->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Checked In -->
                    @if($appointment->checked_in_time)
                        <div class="relative mb-6">
                            <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-blue-500"></div>
                            <div class="ml-6">
                                <h6 class="font-semibold">Checked In</h6>
                                <p class="text-gray-500 text-sm">{{ $appointment->checked_in_time->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Started -->
                    @if($appointment->started_time)
                        <div class="relative mb-6">
                            <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-yellow-400"></div>
                            <div class="ml-6">
                                <h6 class="font-semibold">Started</h6>
                                <p class="text-gray-500 text-sm">{{ $appointment->started_time->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Completed -->
                    @if($appointment->completed_time)
                        <div class="relative mb-6">
                            <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-green-500"></div>
                            <div class="ml-6">
                                <h6 class="font-semibold">Completed</h6>
                                <p class="text-gray-500 text-sm">{{ $appointment->completed_time->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection