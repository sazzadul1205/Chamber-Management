@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- ==============================================
                            HEADER SECTION
                        ============================================== -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Test Request: {{ $medicalFile->file_code }}
                </h1>
                <p class="text-gray-600 mt-1">
                    View test request details for {{ $medicalFile->patient->full_name ?? 'patient' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-sm bg-blue-50 px-4 py-2 rounded-lg border border-blue-200">
                    <span class="font-medium">Status:</span>
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
                    ACTION BUTTONS SECTION
                ============================================== -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('backend.medical-files.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-150">
                        @include('partials.sidebar-icon', ['name' => 'list', 'class' => 'w-4 h-4'])
                        All Tests
                    </a>

                    @if (!$medicalFile->isUploaded && $medicalFile->status != 'cancelled')
                        <a href="{{ route('backend.medical-files.edit', $medicalFile) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-150">
                            @include('partials.sidebar-icon', ['name' => 'Edit', 'class' => 'w-4 h-4'])
                            Edit Request
                        </a>

                        <button onclick="showUploadModal()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-150">
                            @include('partials.sidebar-icon', ['name' => 'Upload', 'class' => 'w-4 h-4'])
                            Upload Result
                        </button>
                    @endif

                    @if ($medicalFile->isUploaded)
                        <a href="{{ route('backend.medical-files.download', $medicalFile) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors duration-150">
                            @include('partials.sidebar-icon', ['name' => 'B_Print', 'class' => 'w-4 h-4'])
                            Download Results
                        </a>
                    @endif

                    @if ($medicalFile->treatment)
                        <a href="{{ route('backend.treatments.show', $medicalFile->treatment) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-150">
                            @include('partials.sidebar-icon', [
                                'name' => 'B_Back',
                                'class' => 'w-4 h-4',
                            ])
                            View Treatment
                        </a>
                    @endif
                </div>
            </div>
        </div>


        <!-- ==============================================
                            MAIN CONTENT SECTION
                        ============================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEFT COLUMN: Patient & Test Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- PATIENT & TREATMENT CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-cyan-50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            Patient & Treatment Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Patient Info -->
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Patient
                                    </h4>
                                    @if ($medicalFile->patient)
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600 text-xl"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('backend.patients.show', $medicalFile->patient_id) }}"
                                                    class="text-lg font-semibold text-gray-800 hover:text-blue-700 transition-colors">
                                                    {{ $medicalFile->patient->full_name }}
                                                </a>
                                                <p class="text-sm text-gray-500">{{ $medicalFile->patient->patient_code }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-gray-500">No patient associated</p>
                                    @endif
                                </div>

                                <!-- Requested By -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Requested
                                        By</h4>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-md text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">
                                                {{ $medicalFile->requestedBy->full_name ?? 'N/A' }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $medicalFile->requested_date->format('d F, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Treatment Info -->
                            <div class="space-y-4">
                                @if ($medicalFile->treatment)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                            Treatment</h4>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-stethoscope text-purple-600 text-xl"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('backend.treatments.show', $medicalFile->treatment_id) }}"
                                                    class="text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors">
                                                    {{ $medicalFile->treatment->treatment_code }}
                                                </a>
                                                <p class="text-sm text-gray-500">{{ $medicalFile->treatment->diagnosis }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Uploaded By -->
                                @if ($medicalFile->uploadedBy)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                            Uploaded By</h4>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user-edit text-orange-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">
                                                    {{ $medicalFile->uploadedBy->full_name }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $medicalFile->uploaded_at->format('d F, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TEST DETAILS CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-purple-50 to-pink-50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-purple-600"></i>
                            Test Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Test Instructions -->
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-blue-500"></i>
                                Test Instructions
                            </h4>
                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                                <p class="text-gray-800 whitespace-pre-line">{{ $medicalFile->requested_notes }}</p>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-700 mb-2">Request Date</h4>
                                <p class="text-lg font-medium text-gray-800">
                                    {{ $medicalFile->requested_date->format('d F, Y') }}</p>
                            </div>
                            @if ($medicalFile->expected_delivery_date)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-700 mb-2">Expected Delivery</h4>
                                    <p class="text-lg font-medium text-gray-800">
                                        {{ \Carbon\Carbon::parse($medicalFile->expected_delivery_date)->format('d F, Y') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Test Result (if uploaded) -->
                        @if ($medicalFile->isUploaded)
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-file-alt text-green-500"></i>
                                    Test Result
                                </h4>
                                <div class="bg-green-50 border border-green-100 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-file-pdf text-green-600 text-xl"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $medicalFile->file_name }}</p>
                                                <p class="text-sm text-gray-500">{{ $medicalFile->file_size_formatted }}
                                                </p>
                                                @if ($medicalFile->description)
                                                    <p class="text-sm text-gray-600 mt-2">{{ $medicalFile->description }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('backend.medical-files.download', $medicalFile) }}"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center gap-2">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Actions & Timeline -->
            <div class="space-y-6">
                <!-- STATUS MANAGEMENT CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-blue-50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-tasks text-indigo-600"></i>
                            Status Management
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <!-- Current Status -->
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Current Status</p>
                            <p
                                class="text-2xl font-bold mt-2
                                @switch($medicalFile->status)
                                    @case('requested') text-blue-600 @break
                                    @case('pending') text-yellow-600 @break
                                    @case('completed') text-green-600 @break
                                    @case('cancelled') text-red-600 @break
                                @endswitch">
                                {{ $medicalFile->status_text }}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        @if (!$medicalFile->isUploaded)
                            <div class="space-y-3">
                                @if ($medicalFile->status != 'cancelled')
                                    <!-- Mark as Pending -->
                                    @if ($medicalFile->status != 'pending')
                                        <form action="{{ route('backend.medical-files.mark-pending', $medicalFile) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="w-full px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition-colors duration-150 flex items-center justify-center gap-2">
                                                <i class="fas fa-clock"></i>
                                                Mark as Pending
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Cancel Button -->
                                    <form action="{{ route('backend.medical-files.cancel', $medicalFile) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to cancel this test request?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors duration-150 flex items-center justify-center gap-2">
                                            <i class="fas fa-times"></i>
                                            Cancel Test
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        <!-- Delete Button -->
                        <form action="{{ route('backend.medical-files.destroy', $medicalFile) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this test request? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition-colors duration-150 flex items-center justify-center gap-2">
                                <i class="fas fa-trash"></i>
                                Delete Request
                            </button>
                        </form>
                    </div>
                </div>

                <!-- TIMELINE CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-history text-gray-600"></i>
                            Timeline
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-4">
                            <!-- Requested -->
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-paper-plane text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Test Requested</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $medicalFile->requested_date->format('d M Y, h:i A') }}</p>
                                    <p class="text-sm text-gray-500">by
                                        {{ $medicalFile->requestedBy->full_name ?? 'System' }}</p>
                                </div>
                            </div>

                            <!-- Expected Delivery -->
                            @if ($medicalFile->expected_delivery_date)
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-calendar-alt text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Expected Delivery</p>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($medicalFile->expected_delivery_date)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Status Updates -->
                            @if ($medicalFile->status == 'pending')
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-hourglass-half text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Marked as Pending</p>
                                        <p class="text-sm text-gray-500">Awaiting test results</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Result Upload -->
                            @if ($medicalFile->isUploaded)
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Test Result Uploaded</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $medicalFile->uploaded_at->format('d M Y, h:i A') }}</p>
                                        <p class="text-sm text-gray-500">by
                                            {{ $medicalFile->uploadedBy->full_name ?? 'System' }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Cancelled -->
                            @if ($medicalFile->status == 'cancelled')
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-times-circle text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Test Cancelled</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $medicalFile->updated_at->format('d M Y, h:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================
                        UPLOAD MODAL
                    ============================================== -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-upload text-green-600"></i>
                    Upload Test Result
                </h3>
            </div>

            <form action="{{ route('backend.medical-files.upload-result', $medicalFile) }}" method="POST"
                enctype="multipart/form-data" id="uploadResultForm" class="p-6 space-y-4">
                @csrf

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Test Result File *</label>
                    <div class="mt-1 flex items-center">
                        <input type="file" name="medical_file" id="medical_file" required
                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Accepted: JPG, PNG, PDF, DOC (Max: 10MB)</p>
                </div>

                <!-- File Preview -->
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

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description / Notes
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Add notes about the test results..."></textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="hideUploadModal()"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded transition-colors flex items-center gap-2">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Result
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================
                        JAVASCRIPT SECTION
                    ============================================== -->
    <script>
        // ==============================================
        // UPLOAD MODAL FUNCTIONS
        // ==============================================
        function showUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function hideUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close modal on outside click
        document.getElementById('uploadModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                hideUploadModal();
            }
        });

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
