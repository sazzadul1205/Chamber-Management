@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Medicine: {{ $medicine->brand_name }}</h1>
                <p class="text-gray-600 mt-1">
                    Update the medicine details and specifications
                </p>
            </div>

            <a href="{{ route('backend.medicines.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                @include('partials.sidebar-icon', [
                    'name' => 'B_Back',
                    'class' => 'w-4 h-4',
                ])
                Back to Medicines
            </a>
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
            <form action="{{ route('backend.medicines.update', $medicine->id) }}" method="POST" class="space-y-6"
                id="medicineForm">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    <!-- MEDICINE BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Medicine Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Medicine Code
                                </label>
                                <input type="text" name="medicine_code"
                                    value="{{ old('medicine_code', $medicine->medicine_code) }}"
                                    class="w-full border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    readonly>
                                <p class="text-xs text-gray-500 mt-1">Unique identifier (cannot be changed)</p>
                            </div>

                            <!-- Brand Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Brand Name *
                                </label>
                                <input type="text" name="brand_name"
                                    value="{{ old('brand_name', $medicine->brand_name) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required maxlength="200" placeholder="Enter brand/trade name">
                                <p class="text-xs text-gray-500 mt-1">Commercial name of the medicine</p>
                            </div>

                        </div>
                    </div>

                    <!-- MEDICINE SPECIFICATIONS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Specifications</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Generic Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Generic Name *
                                </label>
                                <input type="text" name="generic_name"
                                    value="{{ old('generic_name', $medicine->generic_name) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required placeholder="Enter generic/chemical name">
                                <p class="text-xs text-gray-500 mt-1">Scientific name of the active ingredient</p>
                            </div>

                            <!-- Strength -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Strength
                                </label>
                                <input type="text" name="strength" value="{{ old('strength', $medicine->strength) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., 500mg, 10ml, 2%">
                                <p class="text-xs text-gray-500 mt-1">Potency or concentration</p>
                            </div>

                            <!-- Dosage Form -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Dosage Form *
                                </label>
                                <select name="dosage_form" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach ($dosageForms as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('dosage_form', $medicine->dosage_form) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Form in which medicine is administered</p>
                            </div>

                            <!-- Unit -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Unit *
                                </label>
                                <select name="unit" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Unit</option>
                                    @foreach ($units as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('unit', $medicine->unit) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Measurement unit for dispensing</p>
                            </div>

                        </div>
                    </div>

                    <!-- MANUFACTURER & STATUS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Manufacturer & Status</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Manufacturer -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Manufacturer
                                </label>
                                <input type="text" name="manufacturer"
                                    value="{{ old('manufacturer', $medicine->manufacturer) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter manufacturer company name">
                                <p class="text-xs text-gray-500 mt-1">Pharmaceutical company</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active"
                                        {{ old('status', $medicine->status) == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $medicine->status) == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                    <option value="discontinued"
                                        {{ old('status', $medicine->status) == 'discontinued' ? 'selected' : '' }}>
                                        Discontinued
                                    </option>
                                </select>
                                <div class="mt-2 space-y-1 text-xs text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        <span>Active: Available for prescription</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                        <span>Inactive: Temporarily unavailable</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                        <span>Discontinued: No longer manufactured</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- MEDICINE INFORMATION CARD -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Medicine Information</h3>

                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Category Information -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Category</span>
                                    </div>
                                    <div class="text-gray-900 font-medium">
                                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                                            {{ $medicine->category_name }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Usage Information -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Usage Count</span>
                                    </div>
                                    <div class="text-lg font-semibold text-green-600">
                                        {{ $medicine->usage_count }}
                                    </div>
                                    <p class="text-xs text-gray-500">Total prescriptions issued</p>
                                </div>

                                <!-- Full Name -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Complete Name</span>
                                    </div>
                                    <div class="text-gray-900 font-medium">
                                        {{ $medicine->full_name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Timestamps -->
                            <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="text-sm">
                                    <span class="text-gray-500">Created:</span>
                                    <span class="font-medium text-gray-700 ml-2">
                                        {{ $medicine->created_at->format('M d, Y') }} at
                                        {{ $medicine->created_at->format('h:i A') }}
                                    </span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-500">Last Updated:</span>
                                    <span class="font-medium text-gray-700 ml-2">
                                        {{ $medicine->updated_at->format('M d, Y') }} at
                                        {{ $medicine->updated_at->format('h:i A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons back-url="{{ route('backend.medicines.index') }}"
                        submit-text="Update Medicine" />
                </div>
            </form>
        </div>
    </div>

    <x-delete-modal id="deleteModal" title="Delete Medicine"
        message="Are you sure you want to delete medicine '{{ $medicine->brand_name }}'? This action cannot be undone."
        :route="route('backend.medicines.destroy', $medicine->id)" />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const medicineForm = document.getElementById('medicineForm');

            // Form validation
            medicineForm?.addEventListener('submit', function(e) {
                const brandName = document.querySelector('input[name="brand_name"]')?.value.trim();
                const genericName = document.querySelector('input[name="generic_name"]')?.value.trim();
                const dosageForm = document.querySelector('select[name="dosage_form"]')?.value;
                const unit = document.querySelector('select[name="unit"]')?.value;
                const status = document.querySelector('select[name="status"]')?.value;

                // Validate required fields
                if (!brandName || brandName.length < 2) {
                    e.preventDefault();
                    showToast('Please enter a valid brand name', 'error');
                    document.querySelector('input[name="brand_name"]')?.focus();
                    return;
                }

                if (!genericName || genericName.length < 2) {
                    e.preventDefault();
                    showToast('Please enter a valid generic name', 'error');
                    document.querySelector('input[name="generic_name"]')?.focus();
                    return;
                }

                if (!dosageForm) {
                    e.preventDefault();
                    showToast('Please select a dosage form', 'error');
                    document.querySelector('select[name="dosage_form"]')?.focus();
                    return;
                }

                if (!unit) {
                    e.preventDefault();
                    showToast('Please select a unit', 'error');
                    document.querySelector('select[name="unit"]')?.focus();
                    return;
                }

                if (!status) {
                    e.preventDefault();
                    showToast('Please select a status', 'error');
                    document.querySelector('select[name="status"]')?.focus();
                    return;
                }
            });

            // Show toast notification
            function showToast(message, type = 'info') {
                // Remove existing toast
                const existingToast = document.getElementById('formToast');
                if (existingToast) {
                    existingToast.remove();
                }

                // Determine color based on type
                const colors = {
                    info: 'bg-blue-500',
                    success: 'bg-green-500',
                    warning: 'bg-yellow-500',
                    error: 'bg-red-500'
                };

                // Create toast element
                const toast = document.createElement('div');
                toast.id = 'formToast';
                toast.className =
                    `fixed top-4 right-4 ${colors[type] || colors.info} text-white px-4 py-2 rounded-lg shadow-lg transform transition-transform duration-300 translate-x-full z-50`;
                toast.textContent = message;

                // Add to DOM
                document.body.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                    toast.classList.add('translate-x-0');
                }, 10);

                // Remove after 3 seconds
                setTimeout(() => {
                    toast.classList.remove('translate-x-0');
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
@endsection
