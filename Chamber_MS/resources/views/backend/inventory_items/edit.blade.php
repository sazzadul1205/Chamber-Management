@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Edit Inventory Item</h2>

            <a href="{{ route('backend.inventory-items.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-3 bg-red-100 text-red-700 rounded mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('backend.inventory-items.update', $inventoryItem->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Item Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Code</label>
                    <input type="text" name="item_code" id="item_code"
                        value="{{ old('item_code', $inventoryItem->item_code) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Item Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                    <input type="text" name="name" value="{{ old('name', $inventoryItem->name) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('category', $inventoryItem->category) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subcategory -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subcategory</label>
                    <select name="subcategory" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="">Select Subcategory</option>
                        @foreach ($subcategories as $cat => $subs)
                            @foreach ($subs as $sub)
                                <option value="{{ $sub }}"
                                    {{ old('subcategory', $inventoryItem->subcategory) == $sub ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $sub)) }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <select name="unit" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        required>
                        <option value="">Select Unit</option>
                        @foreach ($units as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('unit', $inventoryItem->unit) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Reorder Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                    <input type="number" name="reorder_level"
                        value="{{ old('reorder_level', $inventoryItem->reorder_level ?? 0) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Optimum Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Optimum Level</label>
                    <input type="number" name="optimum_level"
                        value="{{ old('optimum_level', $inventoryItem->optimum_level ?? 0) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Manufacturer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                    <input type="text" name="manufacturer"
                        value="{{ old('manufacturer', $inventoryItem->manufacturer) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <input type="text" name="supplier" value="{{ old('supplier', $inventoryItem->supplier) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="active" {{ old('status', $inventoryItem->status) == 'active' ? 'selected' : '' }}>
                            Active</option>
                        <option value="inactive"
                            {{ old('status', $inventoryItem->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="discontinued"
                            {{ old('status', $inventoryItem->status) == 'discontinued' ? 'selected' : '' }}>Discontinued
                        </option>
                    </select>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('description', $inventoryItem->description) }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-start">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Item
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('category')?.addEventListener('change', function() {
            if (!this.value) return;
            fetch(`{{ route('backend.inventory-items.generate-code') }}?category=${this.value}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('item_code').value = data.code;
                });
        });
    </script>
@endsection
