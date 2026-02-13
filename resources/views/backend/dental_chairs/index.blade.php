@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Dental Chairs Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.dental-chairs.dashboard') }}" target="_blank"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'TV', 'class' => 'w-4 h-4 text-white'])
                    <span>TV Display</span>
                </a>

                <a href="{{ route('backend.dental-chairs.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4 text-white'])
                    <span>Add Chair</span>
                </a>
            </div>
        </div>

        <!-- ALERTS -->
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
                        <p class="text-sm font-medium text-gray-600">Total Chairs</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalChairs }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        @include('partials.sidebar-icon', [
                            'name' => 'Chair',
                            'class' => 'w-6 h-6 text-blue-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Available</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $availableChairs }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Occupied</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $occupiedChairs }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Maintenance</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $maintenanceChairs }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.dental-chairs.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end mt-6">

            <div class="md:col-span-4">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by code, name or location"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-3">
                <select name="status"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="all">All Status</option>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') == $key)>
                            {{ $label }}
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
                    <a href="{{ route('backend.dental-chairs.index') }}"
                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md px-4 py-2 font-medium text-center">
                        Reset
                    </a>
                </div>
            </div>

            <div class="md:col-span-2">
                <a href="{{ route('backend.dental-chairs.schedule') }}"
                    class="w-full flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-900 text-white rounded-md px-4 py-2 font-medium transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'Schedule',
                        'class' => 'w-4 h-4 text-white',
                    ])
                    <span>Schedule</span>
                </a>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Chair Code</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Name & Details</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Location</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Last Used</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dentalChairs as $chair)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ ($dentalChairs->currentPage() - 1) * $dentalChairs->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $chair->chair_code }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-gray-700 font-semibold text-lg">
                                            {{ substr($chair->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $chair->name }}</div>
                                        @if ($chair->notes)
                                            <div class="text-xs text-gray-500">{{ Str::limit($chair->notes, 30) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="text-gray-700">{{ $chair->location ?? 'Not specified' }}</div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full"
                                    style="background-color: {{ $chair->status_color == 'success' ? '#d1fae5' : ($chair->status_color == 'warning' ? '#fef3c7' : '#fee2e2') }};
                                           color: {{ $chair->status_color == 'success' ? '#065f46' : ($chair->status_color == 'warning' ? '#92400e' : '#991b1b') }}">
                                    {{ $chair->status_name }}
                                </span>
                                <div class="mt-1 text-xs text-gray-500">
                                    Appointments: {{ $chair->appointments->count() }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="text-gray-700">{{ $chair->formatted_last_used }}</span>
                            </td>

                            <td class="px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-1">
                                    @php
                                        $btnBaseClasses =
                                            'relative flex items-center justify-center px-2 py-1 text-white rounded text-xs w-8 h-8 group';
                                    @endphp

                                    <!-- View -->
                                    <a href="{{ route('backend.dental-chairs.show', $chair->id) }}"
                                        class="{{ $btnBaseClasses }} bg-blue-500 hover:bg-blue-600"
                                        data-tooltip="View Details">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            View Details
                                        </span>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('backend.dental-chairs.edit', $chair->id) }}"
                                        class="{{ $btnBaseClasses }} bg-green-500 hover:bg-green-600"
                                        data-tooltip="Edit Chair">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            Edit Chair
                                        </span>
                                    </a>

                                    <!-- Schedule -->
                                    <a href="{{ route('backend.dental-chairs.schedule', ['chair_id' => $chair->id]) }}"
                                        class="{{ $btnBaseClasses }} bg-yellow-500 hover:bg-yellow-600"
                                        data-tooltip="View Schedule">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'Schedule',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            View Schedule
                                        </span>
                                    </a>

                                    <!-- Delete -->
                                    <button type="button"
                                        onclick="openDeleteModal('{{ route('backend.dental-chairs.destroy', $chair->id) }}', '{{ $chair->name }}')"
                                        class="{{ $btnBaseClasses }} bg-red-500 hover:bg-red-600"
                                        data-tooltip="Delete Chair">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Delete',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            Delete Chair
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No dental chairs found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$dentalChairs" />
        </div>

    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">

            <!-- Header -->
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-red-600">Delete Dental Chair</h3>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-gray-700 text-sm">
                    This action <strong>cannot be undone</strong>.
                    Are you sure you want to delete dental chair "<span id="deleteChairName"></span>"?
                    All related appointments will be affected.
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

    {{-- Scripts --}}
    <script>
        function openDeleteModal(actionUrl, chairName) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const chairNameSpan = document.getElementById('deleteChairName');

            form.action = actionUrl;
            if (chairNameSpan) {
                chairNameSpan.textContent = chairName;
            }
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

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

            // Close modal on outside click
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDeleteModal();
                    }
                });
            }
        });
    </script>
@endsection
