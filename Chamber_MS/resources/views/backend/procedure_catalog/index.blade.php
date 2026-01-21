@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Dental Procedure Catalog</h2>
            <div class="flex space-x-2">
                <a href="{{ route('backend.procedure-catalog.import') }}"
                    class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm flex items-center">
                    <i class="fas fa-file-import mr-1"></i> Import CSV
                </a>
                <a href="{{ route('backend.procedure-catalog.create') }}"
                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm flex items-center">
                    <i class="fas fa-plus mr-1"></i> Add Procedure
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.procedure-catalog.index') }}"
            class="mb-4 grid grid-cols-1 md:grid-cols-8 gap-3">
            <div class="md:col-span-3">
                <input type="text" name="search" placeholder="Search by code, name or category"
                    value="{{ request('search') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="md:col-span-2">
                <select name="category"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="all">All Categories</option>
                    @foreach ($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <select name="status"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="all">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                        <th class="px-3 py-2 text-left text-sm font-medium">#</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Code</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Procedure Name</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Category</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Duration</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Standard Cost</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Status</th>
                        <th class="px-3 py-2 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($procedures as $procedure)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ ($procedures->currentPage() - 1) * $procedures->perPage() + $loop->iteration }}</td>
                            <td class="px-3 py-2">
                                <span
                                    class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">{{ $procedure->procedure_code }}</span>
                            </td>
                            <td class="px-3 py-2">
                                <strong>{{ $procedure->procedure_name }}</strong>
                                @if ($procedure->description)
                                    <br><small class="text-gray-500">{{ Str::limit($procedure->description, 50) }}</small>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <span
                                    class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">{{ $procedure->category_name }}</span>
                            </td>
                            <td class="px-3 py-2">{{ $procedure->formatted_duration }}</td>
                            <td class="px-3 py-2">{{ $procedure->formatted_cost }}</td>
                            <td class="px-3 py-2">
                                <span
                                    class="px-2 py-1 rounded text-xs font-medium bg-{{ $procedure->status == 'active' ? 'green-100 text-green-800' : 'red-100 text-red-800' }}">
                                    {{ ucfirst($procedure->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center space-x-1">
                                <a href="{{ route('backend.procedure-catalog.show', $procedure->id) }}"
                                    class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('backend.procedure-catalog.edit', $procedure->id) }}"
                                    class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                    class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs delete-procedure"
                                    data-id="{{ $procedure->id }}" data-name="{{ $procedure->procedure_name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-6 text-center text-gray-500">No procedures found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
            {{ $procedures->withQueryString()->links() }}
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteProcedureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-4">Delete Procedure</h3>
            <p class="mb-2">Are you sure you want to delete procedure "<span id="deleteProcedureName"
                    class="font-medium"></span>"?</p>
            <p class="text-red-600 text-sm mb-4">This action cannot be undone!</p>
            <div class="flex justify-end space-x-2">
                <button type="button" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                    onclick="closeDeleteModal()">Cancel</button>
                <form id="deleteProcedureForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete procedure modal
            document.querySelectorAll('.delete-procedure').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    document.getElementById('deleteProcedureName').textContent = name;
                    document.getElementById('deleteProcedureForm').action =
                        `/procedure-catalog/${id}`;
                    document.getElementById('deleteProcedureModal').classList.remove('hidden');
                });
            });
        });

        function closeDeleteModal() {
            document.getElementById('deleteProcedureModal').classList.add('hidden');
        }
    </script>
@endsection
