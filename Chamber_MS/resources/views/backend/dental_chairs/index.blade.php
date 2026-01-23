@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Dental Chairs Management</h2>
            <div class="flex flex-wrap gap-2">
                <!-- TV Display -->
                <a href="{{ route('backend.dental-chairs.dashboard') }}" target="_blank"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-200 text-gray-800 rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_TV', 'class' => 'w-4 h-4'])
                    <span>TV Display</span>
                </a>

                <!-- Add Chair -->
                <a href="{{ route('backend.dental-chairs.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    <span>Add Chair</span>
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-600 text-white rounded p-4 text-center">
                <h4 class="text-xl font-semibold">{{ $totalChairs }}</h4>
                <p class="text-sm">Total Chairs</p>
            </div>
            <div class="bg-green-600 text-white rounded p-4 text-center">
                <h4 class="text-xl font-semibold">{{ $availableChairs }}</h4>
                <p class="text-sm">Available</p>
            </div>
            <div class="bg-yellow-500 text-white rounded p-4 text-center">
                <h4 class="text-xl font-semibold">{{ $occupiedChairs }}</h4>
                <p class="text-sm">Occupied</p>
            </div>
            <div class="bg-red-600 text-white rounded p-4 text-center">
                <h4 class="text-xl font-semibold">{{ $maintenanceChairs }}</h4>
                <p class="text-sm">Maintenance</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.dental-chairs.index') }}"
            class="grid grid-cols-1 md:grid-cols-8 gap-3 mt-4">
            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by code, name or location"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-3">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="all">All Status</option>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">Filter</button>
            </div>

            <div class="md:col-span-1">
                <a href="{{ route('backend.dental-chairs.schedule') }}"
                    class="w-full flex justify-center items-center bg-gray-300 hover:bg-gray-200 text-gray-800 rounded px-3 py-2">
                    @include('partials.sidebar-icon', ['name' => 'B_Calendar', 'class' => 'w-4 h-4'])
                    <span>Schedule</span>
                </a>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">#</th>
                        <th class="px-3 py-2 text-left text-sm">Chair Code</th>
                        <th class="px-3 py-2 text-left text-sm">Name</th>
                        <th class="px-3 py-2 text-left text-sm">Location</th>
                        <th class="px-3 py-2 text-center text-sm">Status</th>
                        <th class="px-3 py-2 text-center text-sm">Last Used</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($dentalChairs as $chair)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ ($dentalChairs->currentPage() - 1) * $dentalChairs->perPage() + $loop->iteration }}</td>
                            <td class="px-3 py-2"><span
                                    class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $chair->chair_code }}</span>
                            </td>
                            <td class="px-3 py-2">
                                <strong>{{ $chair->name }}</strong>
                                @if ($chair->notes)
                                    <br><small class="text-gray-400">{{ Str::limit($chair->notes, 50) }}</small>
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $chair->location ?? 'Not specified' }}</td>
                            <td class="px-3 py-2 text-center">
                                <span
                                    class="px-2 py-1 rounded text-xs bg-{{ $chair->status_color }}">{{ $chair->status_name }}</span>
                            </td>
                            <td class="px-3 py-2 text-center">{{ $chair->formatted_last_used }}</td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.dental-chairs.show', $chair->id) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>
                                    <a href="{{ route('backend.dental-chairs.edit', $chair->id) }}"
                                        class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>
                                    <!-- Delete Button using component -->
                                    <button type="button" data-modal-target="deleteModal"
                                        data-route="{{ route('backend.dental-chairs.destroy', $chair->id) }}"
                                        data-name="{{ $chair->name }}"
                                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Delete',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-gray-500">No dental chairs found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$dentalChairs" class="mt-3" />
    </div>

    <!-- Delete Modal Component -->
    <x-delete-modal id="deleteModal" title="Delete Dental Chair" message="Are you sure?" :route="null" />
@endsection
