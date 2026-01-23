@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Inventory Item Details</h2>
            <a href="{{ route('backend.inventory-items.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                <!-- Back Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>

        @php
            $stock = $inventoryItem->safe_stock ?? (object) ['current_stock' => 0];
            $currentStock = $stock->current_stock ?? 0;
            $unitName = $inventoryItem->unit_name ?? 'unit';
            $stockStatusText = $inventoryItem->stock_status_text ?? 'Unused';
            $stockStatusColor = $inventoryItem->stock_status_color ?? 'gray-400';
            $transactions = $inventoryItem->safe_transactions ?? collect();
            $usages = $inventoryItem->safe_usages ?? collect();
        @endphp

        <!-- Item Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 bg-white p-6 rounded-lg shadow">
            <div>
                <span class="font-medium">Item Code:</span>
                <div class="mt-1">{{ $inventoryItem->item_code }}</div>
            </div>

            <div>
                <span class="font-medium">Item Name:</span>
                <div class="mt-1">{{ $inventoryItem->name }}</div>
            </div>

            <div>
                <span class="font-medium">Category:</span>
                <div class="mt-1">{{ $inventoryItem->category_name }}</div>
            </div>

            <div>
                <span class="font-medium">Subcategory:</span>
                <div class="mt-1">{{ $inventoryItem->subcategory ?? '-' }}</div>
            </div>

            <div>
                <span class="font-medium">Unit:</span>
                <div class="mt-1">{{ $unitName }}</div>
            </div>

            <div>
                <span class="font-medium">Reorder Level:</span>
                <div class="mt-1">{{ $inventoryItem->reorder_level }}</div>
            </div>

            <div>
                <span class="font-medium">Optimum Level:</span>
                <div class="mt-1">{{ $inventoryItem->optimum_level ?? 0 }}</div>
            </div>

            <div>
                <span class="font-medium">Status:</span>
                <div class="mt-1">
                    <span
                        class="px-2 py-1 rounded text-xs font-medium {{ $inventoryItem->status == 'active' ? 'bg-green-100 text-green-800' : ($inventoryItem->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($inventoryItem->status) }}
                    </span>
                </div>
            </div>

            <div>
                <span class="font-medium">Current Stock:</span>
                <div class="mt-1">{{ $currentStock }} {{ $unitName }}</div>
            </div>

            <div>
                <span class="font-medium">Stock Status:</span>
                <div class="mt-1">
                    <span class="px-2 py-1 rounded text-xs font-medium bg-{{ $stockStatusColor }} text-white">
                        {{ $stockStatusText }}
                    </span>
                </div>
            </div>

            <div class="md:col-span-2">
                <span class="font-medium">Description:</span>
                <div class="mt-1 text-gray-600">{{ $inventoryItem->description ?? '-' }}</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex gap-2">
            <a href="{{ route('backend.inventory-items.edit', $inventoryItem->id) }}"
                class="flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md font-medium transition">
                <!-- Edit Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.232 5.232l3.536 3.536M9 11l6 6L21 9l-6-6-6 6z" />
                </svg>
                Edit Item
            </a>

            <form action="{{ route('backend.inventory-items.destroy', $inventoryItem->id) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this item?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium transition">
                    <!-- Trash Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Delete Item
                </button>
            </form>
        </div>

        <!-- Transactions -->
        <div class="mt-6 bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-lg mb-4 text-gray-700">Latest Transactions</h3>
            @if ($transactions->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Date</th>
                                <th class="px-3 py-2 text-left">Type</th>
                                <th class="px-3 py-2 text-center">Quantity</th>
                                <th class="px-3 py-2 text-left">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($transactions as $index => $tx)
                                <tr>
                                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2">{{ $tx->created_at->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                    <td class="px-3 py-2">{{ ucfirst($tx->type ?? 'Unused') }}</td>
                                    <td class="px-3 py-2 text-center">{{ $tx->quantity ?? 0 }} {{ $unitName }}</td>
                                    <td class="px-3 py-2">{{ $tx->remarks ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 italic">No transactions found for this item.</p>
            @endif
        </div>

        <!-- Usages -->
        <div class="mt-6 bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-lg mb-4 text-gray-700">Latest Usages</h3>
            @if ($usages->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Date</th>
                                <th class="px-3 py-2 text-center">Quantity Used</th>
                                <th class="px-3 py-2 text-left">Purpose</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($usages as $index => $usage)
                                <tr>
                                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2">{{ $usage->created_at->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-center">{{ $usage->quantity ?? 0 }} {{ $unitName }}</td>
                                    <td class="px-3 py-2">{{ $usage->purpose ?? 'Unused' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 italic">No usages found for this item.</p>
            @endif
        </div>

    </div>
@endsection
