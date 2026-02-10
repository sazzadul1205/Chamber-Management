@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Dental Procedure</h1>
                <p class="text-gray-600 mt-1">
                    Update procedure details and settings
                </p>
            </div>

            <a href="{{ route('backend.procedure-catalog.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                @include('partials.sidebar-icon', [
                    'name' => 'B_Back',
                    'class' => 'w-4 h-4',
                ])
                Back to Procedures
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
            <form action="{{ route('backend.procedure-catalog.update', $procedureCatalog->id) }}" method="POST"
                class="space-y-6" id="procedureForm">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    <!-- PROCEDURE BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Procedure Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Procedure Code *
                                </label>
                                <input type="text" name="procedure_code"
                                    value="{{ old('procedure_code', $procedureCatalog->procedure_code) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required maxlength="50" placeholder="Enter procedure code">
                                <p class="text-xs text-gray-500 mt-1">Unique identifier for the procedure</p>
                            </div>

                            <!-- Procedure Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Procedure Name *
                                </label>
                                <input type="text" name="procedure_name"
                                    value="{{ old('procedure_name', $procedureCatalog->procedure_name) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required maxlength="200" placeholder="Enter procedure name">
                            </div>

                        </div>
                    </div>

                    <!-- CATEGORY & DETAILS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Category & Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

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
                                            {{ old('category', $procedureCatalog->category) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active"
                                        {{ old('status', $procedureCatalog->status) == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $procedureCatalog->status) == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                                <div class="mt-2 space-y-1 text-xs text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        <span>Active: Available for scheduling and billing</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                        <span>Inactive: Hidden from selection</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- TIME & COST INFORMATION -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Time & Cost Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Standard Duration -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Standard Duration (minutes) *
                                    </label>
                                    <span class="text-xs text-gray-500">
                                        Appointment time needed
                                    </span>
                                </div>
                                <input type="number" name="standard_duration"
                                    value="{{ old('standard_duration', $procedureCatalog->standard_duration) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required min="1" max="480" placeholder="e.g., 30">
                                <p class="text-xs text-gray-500 mt-1">Minimum 1 minute, maximum 8 hours (480 minutes)</p>
                            </div>

                            <!-- Standard Cost -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Standard Cost *
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="standard_cost"
                                        value="{{ old('standard_cost', $procedureCatalog->standard_cost) }}"
                                        class="pl-7 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        required step="0.01" min="0" placeholder="0.00">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Base price for this procedure</p>
                            </div>

                        </div>
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Procedure Description
                            </label>
                            <textarea name="description" rows="4"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" maxlength="1000"
                                placeholder="Describe the procedure, steps involved, materials used, etc.">{{ old('description', $procedureCatalog->description) }}</textarea>
                            <div class="flex justify-between mt-1">
                                <p class="text-xs text-gray-500">Optional detailed description</p>
                                <span id="charCount" class="text-xs text-gray-500">0/1000 characters</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons back-url="{{ route('backend.procedure-catalog.index') }}"
                        submit-text="Update Procedure" />
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const procedureForm = document.getElementById('procedureForm');
            const descriptionTextarea = document.querySelector('textarea[name="description"]');
            const charCount = document.getElementById('charCount');
            const durationInput = document.querySelector('input[name="standard_duration"]');
            const costInput = document.querySelector('input[name="standard_cost"]');

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

            // Duration validation
            if (durationInput) {
                durationInput.addEventListener('blur', () => {
                    let value = parseInt(durationInput.value);
                    if (value < 1) {
                        durationInput.value = 1;
                        showToast('Duration cannot be less than 1 minute');
                    } else if (value > 480) {
                        durationInput.value = 480;
                        showToast('Duration cannot exceed 480 minutes (8 hours)');
                    }
                });
            }

            // Cost validation
            if (costInput) {
                costInput.addEventListener('blur', () => {
                    let value = parseFloat(costInput.value);
                    if (value < 0) {
                        costInput.value = 0;
                        showToast('Cost cannot be negative');
                    }

                    // Format to 2 decimal places
                    if (!isNaN(value)) {
                        costInput.value = value.toFixed(2);
                    }
                });

                // Auto-format on input
                costInput.addEventListener('input', () => {
                    let value = costInput.value;
                    // Remove any non-numeric except decimal point
                    value = value.replace(/[^\d.]/g, '');
                    // Ensure only one decimal point
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    // Limit to 2 decimal places
                    if (parts.length === 2 && parts[1].length > 2) {
                        value = parts[0] + '.' + parts[1].substring(0, 2);
                    }
                    costInput.value = value;
                });
            }

            // Form validation
            procedureForm?.addEventListener('submit', function(e) {
                const duration = parseInt(durationInput?.value);
                const cost = parseFloat(costInput?.value);

                // Validate duration
                if (isNaN(duration) || duration < 1 || duration > 480) {
                    e.preventDefault();
                    alert('Please enter a valid duration between 1 and 480 minutes.');
                    durationInput?.focus();
                    return;
                }

                // Validate cost
                if (isNaN(cost) || cost < 0) {
                    e.preventDefault();
                    alert('Please enter a valid cost (non-negative number).');
                    costInput?.focus();
                    return;
                }

                // Check for duplicate procedure code (client-side basic check)
                const procedureCode = document.querySelector('input[name="procedure_code"]')?.value.trim();
                if (procedureCode && procedureCode.length < 2) {
                    e.preventDefault();
                    alert('Procedure code must be at least 2 characters long.');
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

            // Auto-format cost on page load
            if (costInput?.value) {
                const value = parseFloat(costInput.value);
                if (!isNaN(value)) {
                    costInput.value = value.toFixed(2);
                }
            }
        });
    </script>
@endsection
