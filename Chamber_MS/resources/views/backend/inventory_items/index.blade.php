@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Inventory Items Management</h2>
            <div class="flex flex-wrap gap-2">
                <!-- Export -->
                <a href="{{ route('backend.inventory-items.export') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-200 text-gray-800 rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Export',
                        'class' => 'w-4 h-4',
                    ])
                    <span>Export</span>
                </a>

                <!-- Add Item -->
                <a href="{{ route('backend.inventory-items.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Add',
                        'class' => 'w-4 h-4',
                    ])
                    <span>Add Item</span>
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
                <h4 class="text-xl font-semibold">{{ $totalItems }}</h4>
                <p class="text-sm">Total Items</p>
            </div>
            <div class="bg-green-600 text-white rounded p-4 text-center">
                <h4 class="text-xl font-semibold">{{ $activeItems }}</h4>
                <p class="text-sm">Active Items</p>
            </div>
            <div class="bg-yellow-500 text-white rounded p-4 text-center">
                <h4 class="text-xl font-semibold">{{ $lowStockItems }}</h4>
                <p class="text-sm">Low Stock</p>
            </div>
            <div class="bg-cyan-600 text-white rounded p-4 text-center">
                <h4 class="text-xl font-semibold">{{ count($categories) }}</h4>
                <p class="text-sm">Categories</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.inventory-items.index') }}"
            class="grid grid-cols-1 md:grid-cols-10 gap-3">
            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by code, name, manufacturer"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2">
                <select name="category" class="w-full border rounded px-3 py-2">
                    <option value="all">All Categories</option>
                    @foreach ($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="stock_status" class="w-full border rounded px-3 py-2">
                    <option value="all">All Stock</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>
                        Low Stock
                    </option>
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="all">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>
                        Discontinued
                    </option>
                </select>
            </div>

            <div class="md:col-span-1">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
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
                        <th class="px-3 py-2 text-left text-sm">Item</th>
                        <th class="px-3 py-2 text-left text-sm">Category</th>
                        <th class="px-3 py-2 text-center text-sm">Stock</th>
                        <th class="px-3 py-2 text-center text-sm">Reorder</th>
                        <th class="px-3 py-2 text-center text-sm">Stock Status</th>
                        <th class="px-3 py-2 text-center text-sm">Status</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inventoryItems as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                {{ ($inventoryItems->currentPage() - 1) * $inventoryItems->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-3 py-2">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                    {{ $item->item_code }}
                                </span>
                            </td>

                            <td class="px-3 py-2">
                                <strong>{{ $item->name }}</strong>
                                @if ($item->manufacturer)
                                    <br><small class="text-gray-500">{{ $item->manufacturer }}</small>
                                @endif
                                @if ($item->description)
                                    <br><small class="text-gray-400">{{ Str::limit($item->description, 50) }}</small>
                                @endif
                            </td>

                            <td class="px-3 py-2">
                                <span class="bg-gray-200 px-2 py-1 rounded text-xs">
                                    {{ $item->category_name }}
                                </span>
                                @if ($item->subcategory)
                                    <br><small>{{ $item->subcategory }}</small>
                                @endif
                            </td>

                            <td class="px-3 py-2 text-center">
                                <strong>{{ $item->current_stock ?? 0 }}</strong>
                                <br><small class="text-gray-500">{{ $item->unit_name }}</small>
                            </td>

                            <td class="px-3 py-2 text-center">
                                {{ $item->reorder_level ?? 0 }}
                            </td>

                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-1 rounded text-xs bg-{{ $item->stock_status_color }}">
                                    {{ $item->stock_status_text }}
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center">
                                <span
                                    class="px-2 py-1 rounded text-xs
                                {{ $item->status == 'active'
                                    ? 'bg-green-100 text-green-800'
                                    : ($item->status == 'inactive'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.inventory-items.show', $item->id) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <a href="{{ route('backend.inventory-items.edit', $item->id) }}"
                                        class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <!-- Delete Button using component -->
                                    <button type="button" data-modal-target="deleteModal"
                                        data-route="{{ route('backend.inventory-items.destroy', $item->id) }}"
                                        data-name="{{ $item->name }}"
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
                            <td colspan="9" class="px-3 py-6 text-center text-gray-500">
                                No inventory items found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <x-pagination :paginator="$inventoryItems" class="mt-3" />

        </div>
    </div>

    <!-- Delete Modal Component -->
    <x-delete-modal id="deleteModal" title="Delete Dental Chair" message="Are you sure?" :route="null" />
@endsection
