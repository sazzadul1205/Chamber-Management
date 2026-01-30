@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- ==============================================
                HEADER SECTION
            ============================================== -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Edit Test Request: {{ $medicalFile->file_code }}
                </h1>
                <p class="text-gray-600 mt-1">
                    Update test request details for {{ $medicalFile->patient->full_name ?? 'patient' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-sm bg-blue-50 px-4 py-2 rounded-lg border border-blue-200">
                    <span class="font-medium">Current Status:</span>
                    <span
                        class="ml-2 px-2 py-1 text-xs font-semibold rounded-full 
                        @switch($medicalFile->status)
                            @case('requested') bg-blue-100 text-blue-800 @break
                            @case('pending') bg-yellow-100 text-yellow-800 @break
                            @case('completed') bg-green-100 text-green-800 @break
                            @case('cancelled') bg-red-100 text-red-800 @break
                        @endswitch">
                        {{ $medicalFile->status_text }}
                    </span>
                </div>
                @if ($medicalFile->treatment)
                    <div class="text-sm bg-green-50 px-4 py-2 rounded-lg border border-green-200">
                        <span class="font-medium">Treatment:</span> {{ $medicalFile->treatment->treatment_code }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ==============================================
                UPLOAD RESULTS SECTION (If not already uploaded)
            ============================================== -->
        @if (!$medicalFile->isUploaded && $medicalFile->status != 'cancelled')
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-green-50 to-emerald-50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-upload text-green-600"></i>
                        Upload Test Results
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('backend.medical-files.upload-result', $medicalFile) }}" method="POST"
                        enctype="multipart/form-data" id="uploadResultForm" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- File Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Test Result File *
                                </label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" name="medical_file" id="medical_file" required
                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Accepted: JPG, PNG, PDF, DOC (Max: 10MB)
                                </p>
                                @error('medical_file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Result Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Result Description / Notes
                                </label>
                                <textarea id="description" name="description" rows="2"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Add notes about the test results..."></textarea>
                            </div>
                        </div>

                        <!-- File Preview Area -->
                        <div id="filePreview" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div id="fileIcon"
                                        class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-file text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p id="fileName" class="font-medium text-gray-900"></p>
                                        <p id="fileSize" class="text-sm text-gray-500"></p>
                                    </div>
                                </div>
                                <button type="button" onclick="clearFile()" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Upload Button -->
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-150 flex items-center gap-2">
                                <i class="fas fa-upload"></i>
                                Upload Test Result
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- ==============================================
                CURRENT TEST RESULT (If already uploaded)
            ============================================== -->
        @if ($medicalFile->isUploaded)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-emerald-50 to-teal-50">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-medical text-emerald-600"></i>
                            Test Results
                        </h3>
                        <a href="{{ route('backend.medical-files.download', $medicalFile) }}"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-download"></i>
                            Download Results
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center">
                            @switch($medicalFile->file_type)
                                @case('xray')
                                    <i class="fas fa-x-ray text-emerald-600 text-2xl"></i>
                                @break

                                @case('lab_report')
                                    <i class="fas fa-flask text-emerald-600 text-2xl"></i>
                                @break

                                @case('ct_scan')
                                    <i class="fas fa-brain text-emerald-600 text-2xl"></i>
                                @break

                                @default
                                    <i class="fas fa-file-pdf text-emerald-600 text-2xl"></i>
                            @endswitch
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $medicalFile->file_name }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded">
                                            Uploaded
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            {{ $medicalFile->uploaded_at->format('M d, Y - h:i A') }}
                                        </span>
                                    </div>
                                    @if ($medicalFile->description)
                                        <p class="mt-2 text-gray-700">{{ $medicalFile->description }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">{{ $medicalFile->file_size_formatted }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Uploaded by: {{ $medicalFile->uploadedBy->full_name ?? 'System' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- ==============================================
                EDIT TEST REQUEST FORM CARD
            ============================================== -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.medical-files.update', $medicalFile) }}" method="POST" id="editTestForm">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">
                    <!-- ==============================================
                            PATIENT & TREATMENT INFO (READ-ONLY)
                        ============================================== -->
                    @if ($medicalFile->patient)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $medicalFile->patient->full_name }}
                                            </h3>
                                            <p class="text-sm text-gray-600">{{ $medicalFile->patient->patient_code }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-700">
                                                {{ $medicalFile->patient->phone ?? 'No phone' }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $medicalFile->patient->email ?? 'No email' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="patient_id" value="{{ $medicalFile->patient_id }}">
                        </div>
                    @endif

                    <!-- ==============================================
                            TEST DETAILS SECTION
                        ============================================== -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- LEFT COLUMN: Test type and instructions -->
                        <div class="space-y-6">
                            <!-- TEST TYPE -->
                            <div>
                                <label for="file_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Test Type *
                                </label>
                                <select id="file_type" name="file_type" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach (\App\Models\MedicalFile::fileTypes() as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ $medicalFile->file_type == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('file_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- TEST INSTRUCTIONS -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Test Instructions</h3>
                                <div>
                                    <label for="requested_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                        Instructions / Notes *
                                    </label>
                                    <textarea id="requested_notes" name="requested_notes" required rows="6"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter detailed instructions for the test...">{{ old('requested_notes', $medicalFile->requested_notes) }}</textarea>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p>Instructions can be updated to provide better guidance for the test.</p>
                                    </div>
                                    @error('requested_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Status, dates and additional info -->
                        <div class="space-y-6">
                            <!-- STATUS -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select id="status" name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    {{ $medicalFile->isUploaded ? 'disabled' : '' }}>
                                    @foreach (\App\Models\MedicalFile::statuses() as $value => $label)
                                        @if ($value !== 'completed' || $medicalFile->status === 'completed')
                                            <option value="{{ $value }}"
                                                {{ $medicalFile->status == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @if ($medicalFile->isUploaded)
                                    <input type="hidden" name="status" value="{{ $medicalFile->status }}">
                                    <p class="mt-2 text-sm text-yellow-600">
                                        <i class="fas fa-lock mr-1"></i> Status cannot be changed after results are
                                        uploaded
                                    </p>
                                @endif
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- EXPECTED DATES -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
                                <div class="space-y-4">
                                    <!-- Expected Delivery Date -->
                                    <div>
                                        <label for="expected_delivery_date"
                                            class="block text-sm font-medium text-gray-700 mb-1">
                                            Expected Delivery Date
                                        </label>
                                        <input type="date" id="expected_delivery_date" name="expected_delivery_date"
                                            value="{{ old('expected_delivery_date', $medicalFile->expected_delivery_date ? \Carbon\Carbon::parse($medicalFile->expected_delivery_date)->format('Y-m-d') : '') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <p class="mt-1 text-xs text-gray-500">
                                            When do you expect to receive the test results?
                                        </p>
                                        @error('expected_delivery_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Request Date (Read-only) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Request Date
                                        </label>
                                        <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                                            <span
                                                class="text-sm text-gray-700">{{ $medicalFile->requested_date->format('F d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TREATMENT SELECTION -->
                            <div>
                                <label for="treatment_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Associated Treatment
                                </label>
                                <select id="treatment_id" name="treatment_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">No Treatment (General Test)</option>
                                    @foreach ($treatments as $treatment)
                                        <option value="{{ $treatment->id }}"
                                            {{ $medicalFile->treatment_id == $treatment->id ? 'selected' : '' }}>
                                            {{ $treatment->treatment_code }} - {{ Str::limit($treatment->diagnosis, 40) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Link this test to a specific treatment</p>
                                @error('treatment_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==============================================
                        FORM ACTIONS
                    ============================================== -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            @if ($medicalFile->treatment)
                                <a href="{{ route('backend.treatments.show', $medicalFile->treatment) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-150">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Treatment
                                </a>
                            @else
                                <a href="{{ route('backend.medical-files.show', $medicalFile) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-150">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Test Details
                                </a>
                            @endif
                        </div>
                        <div class="space-x-3">
                            @if (!$medicalFile->isUploaded && $medicalFile->status != 'cancelled')
                                <button type="button" onclick="cancelRequest()"
                                    class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-150">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel Request
                                </button>
                            @endif
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-150">
                                <i class="fas fa-save mr-2"></i>
                                Update Test Request
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================
            JAVASCRIPT SECTION
        ============================================== -->
    <script>
        // ==============================================
        // FILE UPLOAD PREVIEW
        // ==============================================
        const fileInput = document.getElementById('medical_file');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileIcon = document.getElementById('fileIcon');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Show preview
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);

                    // Set appropriate icon
                    const fileType = file.type;
                    if (fileType.includes('image')) {
                        fileIcon.innerHTML = '<i class="fas fa-image text-blue-600"></i>';
                    } else if (fileType.includes('pdf')) {
                        fileIcon.innerHTML = '<i class="fas fa-file-pdf text-red-600"></i>';
                    } else if (fileType.includes('word') || fileType.includes('document')) {
                        fileIcon.innerHTML = '<i class="fas fa-file-word text-blue-600"></i>';
                    } else {
                        fileIcon.innerHTML = '<i class="fas fa-file text-blue-600"></i>';
                    }

                    filePreview.classList.remove('hidden');
                }
            });
        }

        function clearFile() {
            if (fileInput) {
                fileInput.value = '';
                filePreview.classList.add('hidden');
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // ==============================================
        // FORM VALIDATION
        // ==============================================
        function validateEditForm() {
            const testType = document.getElementById('file_type').value;
            const instructions = document.getElementById('requested_notes').value.trim();
            const status = document.getElementById('status').value;

            if (!testType) {
                showError('Please select a test type');
                return false;
            }

            if (!instructions || instructions.length < 10) {
                showError('Please provide detailed instructions (at least 10 characters)');
                return false;
            }

            if (!status) {
                showError('Please select a status');
                return false;
            }

            return true;
        }

        function validateUploadForm() {
            const fileInput = document.getElementById('medical_file');
            if (!fileInput || !fileInput.files[0]) {
                showError('Please select a file to upload');
                return false;
            }

            const file = fileInput.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB

            if (file.size > maxSize) {
                showError('File size exceeds 10MB limit');
                return false;
            }

            return true;
        }

        // ==============================================
        // CANCEL REQUEST FUNCTION
        // ==============================================
        function cancelRequest() {
            if (confirm('Are you sure you want to cancel this test request? This action cannot be undone.')) {
                // Create a form to submit cancel request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('backend.medical-files.cancel', $medicalFile) }}';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                // Add method spoofing
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // ==============================================
        // UI HELPER FUNCTIONS
        // ==============================================
        function showError(message) {
            // Remove existing error
            const existingError = document.querySelector('.form-error');
            if (existingError) existingError.remove();

            // Create new error
            const errorDiv = document.createElement('div');
            errorDiv.className =
                'form-error fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-300 text-red-800 px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(errorDiv);
            setTimeout(() => {
                errorDiv.classList.add('animate-fade-out');
                setTimeout(() => errorDiv.remove(), 300);
            }, 5000);
        }

        function showSuccess(message) {
            const successDiv = document.createElement('div');
            successDiv.className =
                'form-success fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-300 text-green-800 px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(successDiv);
            setTimeout(() => {
                successDiv.classList.add('animate-fade-out');
                setTimeout(() => successDiv.remove(), 300);
            }, 3000);
        }

        // ==============================================
        // FORM SUBMISSION HANDLING
        // ==============================================
        const editTestForm = document.getElementById('editTestForm');
        if (editTestForm) {
            editTestForm.addEventListener('submit', function(e) {
                if (!validateEditForm()) {
                    e.preventDefault();
                } else {
                    showLoading(this, 'Updating...');
                }
            });
        }

        const uploadResultForm = document.getElementById('uploadResultForm');
        if (uploadResultForm) {
            uploadResultForm.addEventListener('submit', function(e) {
                if (!validateUploadForm()) {
                    e.preventDefault();
                } else {
                    showLoading(this, 'Uploading...');
                    // Show success message
                    setTimeout(() => {
                        showSuccess('Test results uploaded successfully!');
                    }, 1000);
                }
            });
        }

        function showLoading(form, text) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> ${text}`;
                submitBtn.disabled = true;

                // Re-enable after 10 seconds in case submission fails
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 10000);
            }
        }
    </script>

    <!-- Animation Styles -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        .animate-fade-out {
            animation: fadeOut 0.3s ease-out;
        }

        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection
