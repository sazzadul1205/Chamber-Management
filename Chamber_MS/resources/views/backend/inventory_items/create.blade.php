@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Add Inventory Item</h2>

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
            <div class="p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('backend.inventory-items.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Item Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Code</label>
                    <input type="text" name="item_code" id="item_code" value="{{ old('item_code') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Item Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
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
                                <option value="{{ $sub }}" {{ old('subcategory') == $sub ? 'selected' : '' }}>
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
                            <option value="{{ $key }}" {{ old('unit') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Reorder Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                    <input type="number" name="reorder_level" value="{{ old('reorder_level', 0) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Optimum Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Optimum Level</label>
                    <input type="number" name="optimum_level" value="{{ old('optimum_level', 0) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Manufacturer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                    <input type="text" name="manufacturer" value="{{ old('manufacturer') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <input type="text" name="supplier" value="{{ old('supplier') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="discontinued">Discontinued</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <x-back-submit-buttons back-url="{{ route('backend.inventory-items.index') }}" submit-text="Save Item" />
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
