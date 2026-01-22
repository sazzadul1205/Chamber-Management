@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Add Medicine</h2>

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
            <div class="p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('backend.medicines.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Medicine Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medicine Code</label>
                    <div class="flex gap-2">
                        <input type="text" name="medicine_code" id="medicine_code" value="{{ old('medicine_code') }}"
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>

                        <button type="button" id="generateCodeBtn"
                            class="px-3 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                            Generate
                        </button>
                    </div>
                </div>

                <!-- Brand Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label>
                    <input type="text" name="brand_name" value="{{ old('brand_name') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Generic Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Generic Name</label>
                    <input type="text" name="generic_name" id="generic_name" value="{{ old('generic_name') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Strength -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Strength</label>
                    <input type="text" name="strength" id="strength" value="{{ old('strength') }}"
                        placeholder="e.g. 500mg, 2%"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Dosage Form -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage Form</label>
                    <select name="dosage_form" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        required>
                        <option value="">Select Dosage Form</option>
                        @foreach ($dosageForms as $key => $label)
                            <option value="{{ $key }}" {{ old('dosage_form') == $key ? 'selected' : '' }}>
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
                            <option value="{{ $key }}" {{ old('unit') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Manufacturer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                    <input type="text" name="manufacturer" value="{{ old('manufacturer') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>
                            Discontinued
                        </option>
                    </select>
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
                    Save Medicine
                </button>
            </div>

        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('generateCodeBtn').addEventListener('click', function() {
            const generic = document.getElementById('generic_name').value;
            const strength = document.getElementById('strength').value;

            if (!generic) {
                alert('Enter generic name first');
                return;
            }

            fetch(
                    `{{ route('backend.medicines.generate-code') }}?generic_name=${encodeURIComponent(generic)}&strength=${encodeURIComponent(strength)}`
                    )
                .then(res => res.json())
                .then(data => {
                    document.getElementById('medicine_code').value = data.code;
                })
                .catch(() => alert('Failed to generate medicine code'));
        });
    </script>
@endsection
