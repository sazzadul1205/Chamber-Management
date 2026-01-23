@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Add Dental Chair</h2>
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
        <form action="{{ route('backend.dental-chairs.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Chair Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chair Code *</label>
                    <div class="flex gap-2">
                        <input type="text" name="chair_code" id="chair_code" value="{{ old('chair_code') }}"
                            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('chair_code') border-red-500 @enderror"
                            required maxlength="20">
                        <button type="button" id="generateCode"
                            class="px-3 py-2 bg-gray-200 hover:bg-gray-300 rounded flex items-center gap-1 text-sm">
                            <i class="fas fa-sync"></i> Generate
                        </button>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">Unique code for the chair (e.g., CHAIR-01)</p>
                </div>

                <!-- Chair Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chair Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror"
                        required maxlength="50">
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                        placeholder="e.g., Room A, Left side"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('location') border-red-500 @enderror"
                        maxlength="100">
                </div>

                <!-- Initial Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Initial Status *</label>
                    <select name="status"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('status') border-red-500 @enderror"
                        required>
                        <option value="">Select Status</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    <p class="text-gray-500 text-xs mt-1">Any special notes about this chair</p>
                </div>
            </div>

            <!-- Submit -->
            <x-back-submit-buttons back-url="{{ route('backend.dental-chairs.index') }}" submit-text="Save Chair" />

        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Generate chair code
            document.getElementById('generateCode')?.addEventListener('click', () => {
                fetch("{{ route('backend.dental-chairs.generate-code') }}")
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('chair_code').value = data.code;
                    })
                    .catch(() => alert('Error generating code'));
            });
        });
    </script>
@endsection
