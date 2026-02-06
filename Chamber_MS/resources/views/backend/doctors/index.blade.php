@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Doctors Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.doctors.leave-requests') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'Leave', 'class' => 'w-4 h-4 text-white'])
                    <span>Leave Requests</span>
                </a>

                <a href="{{ route('backend.doctors.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4 text-white'])
                    <span>New Doctor</span>
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

        <!-- STATS SUMMARY -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Doctors</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $doctors->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Doctor',
                            'class' => 'w-6 h-6 text-blue-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Doctors</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ \App\Models\Doctor::active()->count() }}
                        </p>
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
                        <p class="text-sm font-medium text-gray-600">On Leave</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">
                            {{ \App\Models\Doctor::where('status', 'on_leave')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Leave',
                            'class' => 'w-6 h-6 text-yellow-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Leaves</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            {{ \App\Models\DoctorLeave::pending()->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Leave',
                            'class' => 'w-6 h-6 text-red-600',
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.doctors.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end mt-6">

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search name, code, or specialization"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-3">
                <select name="status"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') == 'active')>Active</option>
                    <option value="inactive" @selected(request('status') == 'inactive')>Inactive</option>
                    <option value="on_leave" @selected(request('status') == 'on_leave')>On Leave</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <select name="specialization"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Specializations</option>
                    @foreach ($specializations as $spec)
                        <option value="{{ $spec }}" @selected(request('specialization') == $spec)>
                            {{ $spec }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <div class="flex gap-2">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                        Filter
                    </button>
                    <a href="{{ route('backend.doctors.index') }}"
                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md px-4 py-2 font-medium text-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Doctor</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Code & Specialization</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Contact</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Schedule</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Stats</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($doctors as $doctor)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ ($doctors->currentPage() - 1) * $doctors->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-700 font-semibold">
                                            {{ substr($doctor->full_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $doctor->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $doctor->qualification ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $doctor->doctor_code }}
                                </span>
                                <div class="mt-1 text-gray-700">{{ $doctor->specialization ?? 'N/A' }}</div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="text-gray-700">{{ $doctor->phone }}</div>
                                <div class="text-xs text-gray-500">{{ $doctor->email }}</div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if ($doctor->activeSchedules && $doctor->activeSchedules->count() > 0)
                                    <div class="flex flex-wrap gap-1 mb-1">
                                        @foreach ($doctor->activeSchedules as $schedule)
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                                {{ substr(ucfirst($schedule->day_of_week), 0, 3) }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ $doctor->activeSchedules->first()->formatted_start_time }} -
                                        {{ $doctor->activeSchedules->first()->formatted_end_time }}
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">No schedule</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full {{ $doctor->status_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $doctor->status)) }}
                                </span>
                                <div class="mt-1 text-xs text-gray-500">
                                    Fee: {{ $doctor->formatted_consultation_fee }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="grid grid-cols-3 gap-1 text-center">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $doctor->total_appointments }}</div>
                                        <div class="text-xs text-gray-500">Apps</div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $doctor->total_patients }}</div>
                                        <div class="text-xs text-gray-500">Patients</div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $doctor->upcoming_appointments }}</div>
                                        <div class="text-xs text-gray-500">Upcoming</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-1">
                                    @php
                                        $btnBaseClasses =
                                            'relative flex items-center justify-center px-2 py-1 text-white rounded text-xs w-8 h-8 group';
                                    @endphp

                                    <!-- View -->
                                    <a href="{{ route('backend.doctors.show', $doctor) }}"
                                        class="{{ $btnBaseClasses }} bg-blue-500 hover:bg-blue-600"
                                        data-tooltip="View Profile">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            View Profile
                                        </span>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('backend.doctors.edit', $doctor) }}"
                                        class="{{ $btnBaseClasses }} bg-green-500 hover:bg-green-600"
                                        data-tooltip="Edit Profile">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            Edit Profile
                                        </span>
                                    </a>

                                    <!-- Schedule -->
                                    <a href="{{ route('backend.doctors.schedule-management', $doctor) }}"
                                        class="{{ $btnBaseClasses }} bg-yellow-500 hover:bg-yellow-600"
                                        data-tooltip="Manage Schedule">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'Schedule',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            Manage Schedule
                                        </span>
                                    </a>

                                    <!-- Calendar -->
                                    <a href="{{ route('backend.doctors.calendar', $doctor) }}"
                                        class="{{ $btnBaseClasses }} bg-purple-500 hover:bg-purple-600"
                                        data-tooltip="View Calendar">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'Calendar',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            View Calendar
                                        </span>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('backend.doctors.destroy', $doctor) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this doctor?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            onclick="openDeleteModal('{{ route('backend.doctors.destroy', $doctor) }}')"
                                            class="{{ $btnBaseClasses }} bg-red-500 hover:bg-red-600"
                                            data-tooltip="Delete Doctor">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Delete',
                                                'class' => 'w-4 h-4',
                                            ])
                                            <span
                                                class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                                Delete Doctor
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No doctors found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$doctors" />
        </div>

    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">

            <!-- Header -->
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-red-600">Delete Doctor</h3>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-gray-700 text-sm">
                    This action <strong>cannot be undone</strong>.
                    Are you sure you want to delete this doctor?
                </p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100">
                    Cancel
                </button>

                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium">
                        Yes, Delete
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- Script --}}
    <script>
        function openDeleteModal(actionUrl) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');

            form.action = actionUrl;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
    <script>
        // Tooltip functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Show tooltip on hover
            document.querySelectorAll('[data-tooltip]').forEach(button => {
                button.addEventListener('mouseenter', function(e) {
                    const tooltip = this.querySelector('.tooltip');
                    if (tooltip) {
                        tooltip.classList.remove('hidden');
                    }
                });

                button.addEventListener('mouseleave', function(e) {
                    const tooltip = this.querySelector('.tooltip');
                    if (tooltip) {
                        tooltip.classList.add('hidden');
                    }
                });
            });
        });
    </script>
@endsection
