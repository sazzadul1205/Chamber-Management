@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Procedure Catalog Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.procedure-catalog.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4 text-white'])
                    <span>New Procedure</span>
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
                        <p class="text-sm font-medium text-gray-600">Total Procedures</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $procedures->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Procedures</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ \App\Models\ProcedureCatalog::where('status', 'active')->count() }}
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
                        <p class="text-sm font-medium text-gray-600">Average Duration</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">
                            {{ \App\Models\ProcedureCatalog::where('status', 'active')->avg('standard_duration') ? round(\App\Models\ProcedureCatalog::where('status', 'active')->avg('standard_duration')) . ' min' : 'N/A' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Avg Cost</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">
                            ${{ \App\Models\ProcedureCatalog::where('status', 'active')->avg('standard_cost') ? number_format(\App\Models\ProcedureCatalog::where('status', 'active')->avg('standard_cost'), 2) : '0.00' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.procedure-catalog.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end mt-6">

            <div class="md:col-span-4">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by code, name, or description"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-3">
                <select name="category"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Categories</option>
                    @foreach ($categories as $key => $label)
                        <option value="{{ $key }}" @selected(request('category') == $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <select name="status"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') == 'active')>Active</option>
                    <option value="inactive" @selected(request('status') == 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <div class="flex gap-2">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                        Filter
                    </button>
                    <a href="{{ route('backend.procedure-catalog.index') }}"
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
                        <th class="px-4 py-3 text-left text-sm font-medium">Procedure Code</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Procedure Details</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Category</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Time & Cost</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($procedures as $procedure)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ ($procedures->currentPage() - 1) * $procedures->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $procedure->procedure_code }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900">{{ $procedure->procedure_name }}</div>
                                @if ($procedure->description)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ Str::limit($procedure->description, 60) }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">
                                    {{ $procedure->category_name }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $procedure->formatted_duration }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $procedure->formatted_cost }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $procedure->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($procedure->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-1">
                                    @php
                                        $btnBaseClasses =
                                            'relative flex items-center justify-center px-2 py-1 text-white rounded text-xs w-8 h-8 group';
                                    @endphp

                                    <!-- View -->
                                    <a href="{{ route('backend.procedure-catalog.show', $procedure->id) }}"
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
                                    <a href="{{ route('backend.procedure-catalog.edit', $procedure->id) }}"
                                        class="{{ $btnBaseClasses }} bg-green-500 hover:bg-green-600"
                                        data-tooltip="Edit Procedure">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            Edit Procedure
                                        </span>
                                    </a>

                                    <!-- Delete -->
                                    <button type="button"
                                        onclick="openDeleteModal('{{ route('backend.procedure-catalog.destroy', $procedure->id) }}', '{{ $procedure->procedure_name }}')"
                                        class="{{ $btnBaseClasses }} bg-red-500 hover:bg-red-600"
                                        data-tooltip="Delete Procedure">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Delete',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50 tooltip">
                                            Delete Procedure
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No procedures found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$procedures" />
        </div>

    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">

            <!-- Header -->
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-red-600">Delete Procedure</h3>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-gray-700 text-sm mb-2">
                    Are you sure you want to delete the procedure "<span id="procedureName"
                        class="font-semibold"></span>"?
                </p>
                <p class="text-xs text-red-600">
                    <strong>Warning:</strong> This action cannot be undone. This procedure will be removed from the catalog.
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
        function openDeleteModal(actionUrl, procedureName) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const nameSpan = document.getElementById('procedureName');

            form.action = actionUrl;
            nameSpan.textContent = procedureName;
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
        });
    </script>
@endsection
