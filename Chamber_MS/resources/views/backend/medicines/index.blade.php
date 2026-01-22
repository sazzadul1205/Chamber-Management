@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Medicine Catalog</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.medicines.import') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-200 text-gray-800 rounded-md text-sm font-medium">
                    Import
                </a>

                <a href="{{ route('backend.medicines.export') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-200 text-gray-800 rounded-md text-sm font-medium">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Export',
                        'class' => 'w-4 h-4',
                    ])
                    <span>Export</span>
                </a>

                <a href="{{ route('backend.medicines.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Add',
                        'class' => 'w-4 h-4',
                    ])
                    <span>Add Medicine</span>
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

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.medicines.index') }}"
            class="grid grid-cols-1 md:grid-cols-9 gap-3 mb-4">

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by brand, generic, code"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2">
                <select name="dosage_form" class="w-full border rounded px-3 py-2">
                    <option value="all">All Forms</option>
                    @foreach ($dosageForms as $key => $label)
                        <option value="{{ $key }}" {{ request('dosage_form') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="all">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued
                    </option>
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
                    Filter
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">#</th>
                        <th class="px-3 py-2 text-left text-sm">Code</th>
                        <th class="px-3 py-2 text-left text-sm">Brand</th>
                        <th class="px-3 py-2 text-left text-sm">Generic</th>
                        <th class="px-3 py-2 text-left text-sm">Strength</th>
                        <th class="px-3 py-2 text-left text-sm">Form</th>
                        <th class="px-3 py-2 text-left text-sm">Category</th>
                        <th class="px-3 py-2 text-left text-sm">Status</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($medicines as $medicine)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ ($medicines->currentPage() - 1) * $medicines->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-3 py-2">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                    {{ $medicine->medicine_code }}
                                </span>
                            </td>

                            <td class="px-3 py-2 font-medium">
                                {{ $medicine->brand_name }}
                                @if ($medicine->manufacturer)
                                    <br><small class="text-gray-500">{{ $medicine->manufacturer }}</small>
                                @endif
                            </td>

                            <td class="px-3 py-2">{{ $medicine->generic_name }}</td>
                            <td class="px-3 py-2">{{ $medicine->strength ?? '-' }}</td>

                            <td class="px-3 py-2">
                                <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">
                                    {{ $medicine->dosage_form_name }}
                                </span>
                            </td>

                            <td class="px-3 py-2">
                                <span class="bg-gray-100 px-2 py-1 rounded text-xs">
                                    {{ $medicine->category_name }}
                                </span>
                            </td>

                            <td class="px-3 py-2">
                                <span
                                    class="px-2 py-1 rounded text-xs font-medium
                            {{ $medicine->status === 'active'
                                ? 'bg-green-100 text-green-800'
                                : ($medicine->status === 'inactive'
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($medicine->status) }}
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.medicines.show', $medicine->id) }}"
                                        class="p-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <a href="{{ route('backend.medicines.edit', $medicine->id) }}"
                                        class="p-1.5 bg-yellow-400 hover:bg-yellow-500 text-white rounded">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <button type="button"
                                        class="p-1.5 bg-red-600 hover:bg-red-700 text-white rounded delete-medicine"
                                        data-id="{{ $medicine->id }}" data-name="{{ $medicine->brand_name }}">
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
                            <td colspan="9" class="px-3 py-6 text-center text-gray-500">
                                No medicines found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if ($medicines->lastPage() > 1)
                <div class="mt-3 flex justify-center items-center space-x-4 px-4 py-3 bg-white border rounded shadow-sm">

                    @if ($medicines->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <a href="{{ $medicines->previousPageUrl() }}"
                            class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">
                            Previous
                        </a>
                    @endif

                    <span class="text-sm font-medium text-gray-700">
                        Page {{ $medicines->currentPage() }} of {{ $medicines->lastPage() }}
                    </span>

                    @if ($medicines->hasMorePages())
                        <a href="{{ $medicines->nextPageUrl() }}" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">
                            Next
                        </a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                            Next
                        </span>
                    @endif

                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteMedicineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-4">Delete Medicine</h3>
            <p class="mb-2">
                Are you sure you want to delete
                <span id="deleteMedicineName" class="font-medium"></span>?
            </p>
            <p class="text-red-600 text-sm mb-4">This action cannot be undone.</p>

            <div class="flex justify-end gap-2">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>

                <form id="deleteMedicineForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-medicine').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('deleteMedicineName').textContent = btn.dataset.name;
                document.getElementById('deleteMedicineForm').action = `/medicines/${btn.dataset.id}`;
                document.getElementById('deleteMedicineModal').classList.remove('hidden');
            });
        });

        function closeDeleteModal() {
            document.getElementById('deleteMedicineModal').classList.add('hidden');
        }
    </script>
@endsection
