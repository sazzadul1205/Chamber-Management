@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Diagnosis Codes (ICD-10)</h2>
            <div class="flex space-x-2">
                <a href="{{ route('backend.diagnosis-codes.export') }}"
                    class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Export CSV
                </a>
                <a href="{{ route('backend.diagnosis-codes.create') }}"
                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Code
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-3 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.diagnosis-codes.index') }}"
            class="mb-4 grid grid-cols-1 md:grid-cols-8 gap-3">
            <div class="md:col-span-4">
                <input type="text" name="search" placeholder="Search by code, description, or category"
                    value="{{ request('search') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="md:col-span-2">
                <select name="category"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="all">All Categories</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-1">
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
                        <th class="px-3 py-2 text-left text-sm font-medium">Description</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Category</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Status</th>
                        <th class="px-3 py-2 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($diagnosisCodes as $code)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ ($diagnosisCodes->currentPage() - 1) * $diagnosisCodes->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2">
                                <span
                                    class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">{{ $code->code }}</span>
                            </td>
                            <td class="px-3 py-2">{{ $code->description }}</td>
                            <td class="px-3 py-2">
                                <span
                                    class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">{{ $code->category_name }}</span>
                            </td>
                            <td class="px-3 py-2">
                                <span
                                    class="px-2 py-1 rounded text-xs font-medium bg-{{ $code->status == 'active' ? 'green-100 text-green-800' : 'red-100 text-red-800' }}">
                                    {{ ucfirst($code->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center flex justify-center gap-1">
                                {{-- View --}}
                                <a href="{{ route('backend.diagnosis-codes.show', $code->id) }}"
                                    class="p-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('backend.diagnosis-codes.edit', $code->id) }}"
                                    class="p-1.5 bg-yellow-400 hover:bg-yellow-500 text-white rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </a>

                                {{-- Delete --}}
                                <button type="button" class="p-1.5 bg-red-600 hover:bg-red-700 text-white rounded delete-code"
                                    data-id="{{ $code->id }}" data-code="{{ $code->code }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-gray-500">No diagnosis codes found</td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- Pagination -->
                @if ($diagnosisCodes->lastPage() > 1)
                    <tfoot colspan="6" class="px-4 py-3 bg-white border rounded shadow-sm">
                        <tr>
                            <td colspan="6" class="flex justify-center items-center gap-4 py-2">
                                {{-- Previous --}}
                                @if ($diagnosisCodes->onFirstPage())
                                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                                @else
                                    <a href="{{ $diagnosisCodes->previousPageUrl() }}"
                                        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">Previous</a>
                                @endif

                                {{-- Page Info --}}
                                <span class="text-sm font-medium text-gray-700">
                                    Page {{ $diagnosisCodes->currentPage() }} of {{ $diagnosisCodes->lastPage() }}
                                </span>

                                {{-- Next --}}
                                @if ($diagnosisCodes->hasMorePages())
                                    <a href="{{ $diagnosisCodes->nextPageUrl() }}"
                                        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">Next</a>
                                @else
                                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteCodeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-4">Delete Diagnosis Code</h3>
            <p class="mb-2">Are you sure you want to delete diagnosis code "<span id="deleteCodeName"></span>"?</p>
            <p class="text-red-600 text-sm mb-4">This action cannot be undone!</p>
            <div class="flex justify-end space-x-2">
                <button type="button" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                    onclick="closeDeleteModal()">Cancel</button>
                <form id="deleteCodeForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Delete code modal
            document.querySelectorAll('.delete-code').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const code = this.dataset.code;
                    document.getElementById('deleteCodeName').textContent = code;
                    document.getElementById('deleteCodeForm').action = `/diagnosis-codes/${id}`;
                    document.getElementById('deleteCodeModal').classList.remove('hidden');
                });
            });
        });

        function closeDeleteModal() {
            document.getElementById('deleteCodeModal').classList.add('hidden');
        }
    </script>

@endsection