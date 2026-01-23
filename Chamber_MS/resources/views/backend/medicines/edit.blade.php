@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Edit Medicine</h2>

            <a href="{{ route('backend.medicines.index') }}"
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
        <form action="{{ route('backend.medicines.update', $medicine->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Medicine Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medicine Code</label>
                    <input type="text" name="medicine_code" id="medicine_code"
                        value="{{ old('medicine_code', $medicine->medicine_code) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" readonly>
                    <p class="text-xs text-gray-500 mt-1">Medicine code cannot be changed</p>
                </div>

                <!-- Brand Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label>
                    <input type="text" name="brand_name" value="{{ old('brand_name', $medicine->brand_name) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Generic Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Generic Name</label>
                    <input type="text" name="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Strength -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Strength</label>
                    <input type="text" name="strength" value="{{ old('strength', $medicine->strength) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Dosage Form -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage Form</label>
                    <select name="dosage_form" id="dosage_form"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                        @foreach ($dosageForms as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('dosage_form', $medicine->dosage_form) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
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
                                {{ old('unit', $medicine->unit) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Manufacturer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                    <input type="text" name="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="active" {{ old('status', $medicine->status) == 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="inactive" {{ old('status', $medicine->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive</option>
                        <option value="discontinued"
                            {{ old('status', $medicine->status) == 'discontinued' ? 'selected' : '' }}>Discontinued
                        </option>
                    </select>
                </div>
            </div>

            <!-- Medicine Info -->
            <div class="border rounded-lg p-4 bg-gray-50">
                <h4 class="font-medium text-gray-700 mb-3">Medicine Information</h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
                    <div>
                        <span class="font-medium">Category:</span>
                        <div>{{ $medicine->category_name }}</div>
                    </div>

                    <div>
                        <span class="font-medium">Usage Count:</span>
                        <div>{{ $medicine->usage_count }} prescriptions</div>
                    </div>

                    <div>
                        <span class="font-medium">Full Name:</span>
                        <div>{{ $medicine->full_name }}</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-start gap-2">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Medicine
                </button>

                <form action="{{ route('backend.medicines.destroy', $medicine->id) }}" method="POST"
                    onsubmit="return confirm('Delete this medicine permanently?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium transition">
                        Delete
                    </button>
                </form>
            </div>
        </form>
    </div>
@endsection
