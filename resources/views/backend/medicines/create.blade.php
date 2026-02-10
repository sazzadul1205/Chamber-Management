@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Medicine</h1>
                <p class="text-gray-600 mt-1">
                    Add a new medicine to the catalog with appropriate details and specifications
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
            <form action="{{ route('backend.medicines.store') }}" method="POST" class="space-y-6" id="medicineForm">
                @csrf

                <div class="p-6 space-y-6">

                    <!-- MEDICINE BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Medicine Code -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Medicine Code *
                                    </label>
                                    <button type="button" id="generateCodeBtn"
                                        class="text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-md">
                                        Generate Code
                                    </button>
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" name="medicine_code" id="medicine_code"
                                        value="{{ old('medicine_code') }}"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        required maxlength="50" placeholder="e.g., MED-001">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Unique identifier for the medicine</p>
                            </div>

                            <!-- Brand Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Brand Name *
                                </label>
                                <input type="text" name="brand_name" value="{{ old('brand_name') }}"
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
                                <input type="text" name="generic_name" id="generic_name"
                                    value="{{ old('generic_name') }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required placeholder="Enter generic/chemical name">
                                <p class="text-xs text-gray-500 mt-1">Scientific name of the active ingredient</p>
                            </div>

                            <!-- Strength -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Strength
                                </label>
                                <input type="text" name="strength" id="strength" value="{{ old('strength') }}"
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
                                    <option value="">Select Dosage Form</option>
                                    @foreach ($dosageForms as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('dosage_form') == $key ? 'selected' : '' }}>
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
                                        <option value="{{ $key }}" {{ old('unit') == $key ? 'selected' : '' }}>
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
                                <input type="text" name="manufacturer" value="{{ old('manufacturer') }}"
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
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                    <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>
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

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons back-url="{{ route('backend.medicines.index') }}" submit-text="Save Medicine" />
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const medicineForm = document.getElementById('medicineForm');
            const generateCodeBtn = document.getElementById('generateCodeBtn');
            const medicineCodeInput = document.getElementById('medicine_code');
            const genericNameInput = document.getElementById('generic_name');
            const strengthInput = document.getElementById('strength');

            // Generate medicine code
            if (generateCodeBtn) {
                generateCodeBtn.addEventListener('click', function() {
                    const generic = genericNameInput?.value.trim();
                    const strength = strengthInput?.value.trim();

                    if (!generic) {
                        showToast('Please enter generic name first', 'warning');
                        genericNameInput?.focus();
                        return;
                    }

                    // Show loading state
                    const originalText = this.textContent;
                    this.textContent = 'Generating...';
                    this.disabled = true;

                    fetch(
                            `{{ route('backend.medicines.generate-code') }}?generic_name=${encodeURIComponent(generic)}&strength=${encodeURIComponent(strength || '')}`
                        )
                        .then(res => {
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(data => {
                            if (data.code) {
                                medicineCodeInput.value = data.code;
                                showToast('Medicine code generated successfully', 'success');
                            } else {
                                throw new Error('No code received');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Failed to generate medicine code. Please try again.', 'error');
                        })
                        .finally(() => {
                            this.textContent = originalText;
                            this.disabled = false;
                        });
                });
            }

            // Auto-uppercase for medicine code
            if (medicineCodeInput) {
                medicineCodeInput.addEventListener('input', () => {
                    medicineCodeInput.value = medicineCodeInput.value.toUpperCase();
                });

                medicineCodeInput.addEventListener('blur', () => {
                    const value = medicineCodeInput.value.trim();
                    if (value && !/^[A-Z0-9\-]+$/.test(value)) {
                        showToast('Medicine code should contain only letters, numbers, and hyphens',
                            'warning');
                    }
                });
            }

            // Form validation
            medicineForm?.addEventListener('submit', function(e) {
                const medicineCode = medicineCodeInput?.value.trim();
                const brandName = document.querySelector('input[name="brand_name"]')?.value.trim();
                const genericName = genericNameInput?.value.trim();
                const dosageForm = document.querySelector('select[name="dosage_form"]')?.value;
                const unit = document.querySelector('select[name="unit"]')?.value;
                const status = document.querySelector('select[name="status"]')?.value;

                // Validate required fields
                if (!medicineCode || medicineCode.length < 3) {
                    e.preventDefault();
                    showToast('Please enter a valid medicine code (minimum 3 characters)', 'error');
                    medicineCodeInput?.focus();
                    return;
                }

                if (!brandName || brandName.length < 2) {
                    e.preventDefault();
                    showToast('Please enter a valid brand name', 'error');
                    document.querySelector('input[name="brand_name"]')?.focus();
                    return;
                }

                if (!genericName || genericName.length < 2) {
                    e.preventDefault();
                    showToast('Please enter a valid generic name', 'error');
                    genericNameInput?.focus();
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
