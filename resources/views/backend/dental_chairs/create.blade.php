@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add Dental Chair</h1>
                <p class="text-gray-600 mt-1">
                    Create a new dental chair with location and status information
                </p>
            </div>
        </div>

        <!-- VALIDATION ERRORS -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM CARD -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.dental-chairs.store') }}" method="POST">
                @csrf

                <div class="p-6 space-y-6">

                    <!-- BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Chair Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Chair Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chair Code *
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" name="chair_code" id="chair_code" value="{{ old('chair_code') }}"
                                        required maxlength="20" placeholder="e.g., CHAIR-01"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" id="generateCode"
                                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Generate
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Unique code for the chair</p>
                            </div>

                            <!-- Chair Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chair Name *
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required maxlength="50"
                                    placeholder="e.g., Main Chair, Emergency Chair"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Location
                                </label>
                                <input type="text" name="location" value="{{ old('location') }}" maxlength="100"
                                    placeholder="e.g., Room A, Left side, 2nd Floor"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Initial Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Initial Status *
                                </label>
                                <select name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Status</option>
                                    @foreach ($statuses as $key => $label)
                                        <option value="{{ $key }}" @selected(old('status') == $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Notes - Full Width -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Notes
                                </label>
                                <textarea name="notes" rows="4"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Any special notes about this chair...">{{ old('notes') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Additional information about the chair's location,
                                    equipment, or special considerations</p>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <x-back-submit-buttons back-url="{{ route('backend.dental-chairs.index') }}" submit-text="Save Chair" />
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate chair code
            const generateBtn = document.getElementById('generateCode');
            const chairCodeInput = document.getElementById('chair_code');

            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    fetch("{{ route('backend.dental-chairs.generate-code') }}")
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.code) {
                                chairCodeInput.value = data.code;
                            }
                        })
                        .catch(error => {
                            console.error('Error generating code:', error);
                            alert('Error generating code. Please try again.');
                        });
                });
            }

            // Auto-generate code if field is empty
            if (chairCodeInput && !chairCodeInput.value.trim()) {
                generateBtn?.click();
            }

            // Format chair code to uppercase on blur
            if (chairCodeInput) {
                chairCodeInput.addEventListener('blur', function() {
                    if (this.value) {
                        this.value = this.value.toUpperCase().trim();
                    }
                });
            }

            // Capitalize first letter of chair name
            const chairNameInput = document.querySelector('[name="name"]');
            if (chairNameInput) {
                chairNameInput.addEventListener('blur', function() {
                    if (this.value) {
                        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                    }
                });
            }
        });
    </script>
@endsection
