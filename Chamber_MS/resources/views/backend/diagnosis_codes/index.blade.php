@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Diagnosis Codes (ICD-10)</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.diagnosis-codes.export') }}"
                    class="flex items-center gap-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Export CSV
                </a>
                <a href="{{ route('backend.diagnosis-codes.create') }}"
                    class="flex items-center gap-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Code
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded shadow-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-700 rounded shadow-sm">{{ session('error') }}</div>
        @endif

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.diagnosis-codes.index') }}"
            class="mb-4 grid grid-cols-1 md:grid-cols-8 gap-3">
            <div class="md:col-span-4">
                <input type="text" name="search" placeholder="Search by code, description, or category"
                    value="{{ request('search') }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="md:col-span-2">
                <select name="category"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="all">All Categories</option>
                    @foreach ($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-1">
                <select name="status"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="all">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="md:col-span-1">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-3 py-2 font-medium transition">
                    Filter
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded-md shadow-sm border">
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
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-2">
                                {{ ($diagnosisCodes->currentPage() - 1) * $diagnosisCodes->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-2">
                                <span
                                    class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">{{ $code->code }}</span>
                            </td>
                            <td class="px-3 py-2 text-gray-700">{{ $code->description }}</td>
                            <td class="px-3 py-2">
                                <span
                                    class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs font-medium">{{ $code->category_name }}</span>
                            </td>
                            <td class="px-3 py-2">
                                <span
                                    class="px-2 py-1 rounded text-xs font-medium {{ $code->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($code->status) }}
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    {{-- View --}}
                                    <a href="{{ route('backend.diagnosis-codes.show', $code->id) }}"
                                        class="flex items-center justify-center w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('backend.diagnosis-codes.edit', $code->id) }}"
                                        class="flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    {{-- Delete --}}
                                    <button type="button" data-modal-target="deleteModal"
                                        data-route="{{ route('backend.diagnosis-codes.destroy', $code->id) }}"
                                        data-name="{{ $code->name }}"
                                        class="flex items-center justify-center w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded-md transition">
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
                            <td colspan="6" class="px-3 py-6 text-center text-gray-400">No diagnosis codes found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-3">
                <x-pagination :paginator="$diagnosisCodes" />
            </div>
        </div>
    </div>
@endsection
