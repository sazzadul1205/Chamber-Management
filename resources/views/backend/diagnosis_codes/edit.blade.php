@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Diagnosis Code: {{ $diagnosisCode->code }}</h1>
                <p class="text-gray-600 mt-1">
                    Update the diagnosis code details and status
                </p>
            </div>

            <a href="{{ route('backend.diagnosis-codes.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                @include('partials.sidebar-icon', [
                    'name' => 'B_Back',
                    'class' => 'w-4 h-4',
                ])
                Back to Diagnosis Codes
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
            <form action="{{ route('backend.diagnosis-codes.update', $diagnosisCode->id) }}" method="POST"
                class="space-y-6" id="diagnosisForm">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    <!-- DIAGNOSIS BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- ICD-10 Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    ICD-10 Code *
                                </label>
                                <input type="text" name="code" value="{{ old('code', $diagnosisCode->code) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required maxlength="20" placeholder="Enter ICD-10 code">
                                <p class="text-xs text-gray-500 mt-1">Standard diagnosis code format (e.g., K02.9)</p>
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Category *
                                </label>
                                <select name="category" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('category', $diagnosisCode->category) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Diagnosis category for organization</p>
                            </div>

                        </div>
                    </div>

                    <!-- STATUS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active"
                                        {{ old('status', $diagnosisCode->status) == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $diagnosisCode->status) == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                                <div class="mt-2 space-y-1 text-xs text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        <span>Active: Available for patient diagnosis</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                        <span>Inactive: Hidden from selection</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Diagnosis Description
                            </label>
                            <textarea name="description" rows="4"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" maxlength="1000"
                                placeholder="Enter full diagnosis description, symptoms, or clinical notes">{{ old('description', $diagnosisCode->description) }}</textarea>
                            <div class="flex justify-between mt-1">
                                <p class="text-xs text-gray-500">Detailed description of the diagnosis</p>
                                <span id="charCount" class="text-xs text-gray-500">0/1000 characters</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-edit-page-buttons back-url="{{ route('backend.diagnosis-codes.index') }}" submit-text="Update"
                        delete-modal-id="deleteModal" submit-color="blue" />
                </div>
            </form>
        </div>
    </div>

    <x-delete-modal id="deleteModal" title="Delete Diagnosis Code"
        message="Are you sure you want to delete diagnosis code '{{ $diagnosisCode->code }}'? This action cannot be undone."
        :route="route('backend.diagnosis-codes.destroy', $diagnosisCode->id)" />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const diagnosisForm = document.getElementById('diagnosisForm');
            const descriptionTextarea = document.querySelector('textarea[name="description"]');
            const charCount = document.getElementById('charCount');
            const codeInput = document.querySelector('input[name="code"]');

            // Character count for description
            if (descriptionTextarea && charCount) {
                const updateCharCount = () => {
                    const length = descriptionTextarea.value.length;
                    charCount.textContent = `${length}/1000 characters`;

                    if (length > 900) {
                        charCount.classList.add('text-yellow-600');
                        charCount.classList.remove('text-gray-500', 'text-red-500');
                    } else if (length >= 1000) {
                        charCount.classList.add('text-red-500');
                        charCount.classList.remove('text-gray-500', 'text-yellow-600');
                    } else {
                        charCount.classList.add('text-gray-500');
                        charCount.classList.remove('text-yellow-600', 'text-red-500');
                    }
                };

                descriptionTextarea.addEventListener('input', updateCharCount);
                updateCharCount(); // Initial update
            }

            // Code input validation (basic ICD-10 format)
            if (codeInput) {
                codeInput.addEventListener('blur', () => {
                    const value = codeInput.value.trim().toUpperCase();
                    if (value && !/^[A-Z]\d{2}(\.\d{1,4})?$/.test(value) && value.length < 3) {
                        showToast('Please enter a valid ICD-10 format (e.g., K02.9)', 'warning');
                    } else {
                        codeInput.value = value;
                    }
                });

                // Auto-uppercase on input
                codeInput.addEventListener('input', () => {
                    codeInput.value = codeInput.value.toUpperCase();
                });
            }

            // Form validation
            diagnosisForm?.addEventListener('submit', function(e) {
                const code = document.querySelector('input[name="code"]')?.value.trim();
                const category = document.querySelector('select[name="category"]')?.value;
                const status = document.querySelector('select[name="status"]')?.value;

                // Validate code
                if (!code || code.length < 2) {
                    e.preventDefault();
                    alert('Please enter a valid diagnosis code (minimum 2 characters).');
                    codeInput?.focus();
                    return;
                }

                // Validate category
                if (!category) {
                    e.preventDefault();
                    alert('Please select a category.');
                    document.querySelector('select[name="category"]')?.focus();
                    return;
                }

                // Validate status
                if (!status) {
                    e.preventDefault();
                    alert('Please select a status.');
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
