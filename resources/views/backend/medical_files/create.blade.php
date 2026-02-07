@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- ==============================================
                HEADER SECTION
                Shows different titles based on context
            ============================================== -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    @if ($currentTreatment)
                        Request Test for {{ $currentTreatment->treatment_code }}
                    @else
                        Request New Test
                    @endif
                </h1>
                <p class="text-gray-600 mt-1">
                    @if ($currentTreatment)
                        Request a medical test for {{ $currentTreatment->patient->full_name ?? 'patient' }}'s treatment
                    @else
                        Request a medical test for patient
                    @endif
                </p>
            </div>
            @if ($currentTreatment)
                <div class="text-sm bg-blue-50 px-4 py-2 rounded-lg border border-blue-200">
                    <span class="font-medium">Patient:</span> {{ $currentTreatment->patient->full_name ?? 'N/A' }}
                    <span class="mx-2">|</span>
                    <span class="font-medium">Treatment:</span> {{ $currentTreatment->treatment_code }}
                </div>
            @endif
        </div>

        <!-- ==============================================
                FORM CARD
                Main form for requesting tests
            ============================================== -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.medical-files.store') }}" method="POST">
                @csrf

                <div class="p-6 space-y-6">
                    <!-- ==============================================
                            PATIENT & TREATMENT SECTION
                        ============================================== -->

                    <!-- SCENARIO 1: From treatment page - show info boxes -->
                    @if ($currentTreatment)
                        <!-- Patient Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900">
                                                {{ $currentTreatment->patient->full_name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $currentTreatment->patient->patient_code }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-700">
                                                {{ $currentTreatment->patient->phone ?? 'No phone' }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $currentTreatment->patient->email ?? 'No email' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="patient_id" value="{{ $currentTreatment->patient_id }}">
                        </div>

                        <!-- Treatment Info Box -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-stethoscope text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $currentTreatment->treatment_code }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{ Str::limit($currentTreatment->diagnosis, 80) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-700">
                                                {{ $currentTreatment->doctor->user->full_name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ $currentTreatment->status_text }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="treatment_id" value="{{ $currentTreatment->id }}">
                        </div>

                        <!-- SCENARIO 2: General page - show dropdowns -->
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Patient Selection -->
                            <div>
                                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Patient *
                                </label>
                                <select id="patient_id" name="patient_id" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}"
                                            {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->patient_code }} - {{ $patient->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Treatment Selection -->
                            @if ($currentPatient)
                                <div>
                                    <label for="treatment_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Associated Treatment
                                    </label>
                                    <select id="treatment_id" name="treatment_id"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">No Treatment (General Test)</option>
                                        @foreach ($patientTreatments as $treatment)
                                            <option value="{{ $treatment->id }}"
                                                {{ old('treatment_id') == $treatment->id ? 'selected' : '' }}>
                                                {{ $treatment->treatment_code }} -
                                                {{ Str::limit($treatment->diagnosis, 40) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">If this test is part of a specific treatment</p>
                                    @error('treatment_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- ==============================================
                            TEST DETAILS SECTION
                            Two-column layout for form fields
                        ============================================== -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- LEFT COLUMN: Test type and instructions -->
                        <div class="space-y-6">
                            <!-- TEST TYPE SELECTION -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Test Type *</label>
                                <div class="space-y-4">
                                    <!-- Test Type Selection -->
                                    <div>
                                        <select id="file_type" name="file_type" required
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Test Type</option>
                                            @foreach (\App\Models\MedicalFile::fileTypes() as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('file_type') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('file_type')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Common Test Types Quick Select -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Common Tests:</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button"
                                                class="text-left p-3 border border-gray-200 rounded-lg hover:bg-blue-50 transition-colors duration-150 test-quick-select"
                                                data-test-type="xray" data-test-name="X-Ray">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-x-ray text-blue-600 text-sm"></i>
                                                    </div>
                                                    <span class="font-medium">X-Ray</span>
                                                </div>
                                            </button>
                                            <button type="button"
                                                class="text-left p-3 border border-gray-200 rounded-lg hover:bg-green-50 transition-colors duration-150 test-quick-select"
                                                data-test-type="lab_report" data-test-name="Lab Report">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-flask text-green-600 text-sm"></i>
                                                    </div>
                                                    <span class="font-medium">Lab Report</span>
                                                </div>
                                            </button>
                                            <button type="button"
                                                class="text-left p-3 border border-gray-200 rounded-lg hover:bg-purple-50 transition-colors duration-150 test-quick-select"
                                                data-test-type="ct_scan" data-test-name="CT Scan">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-brain text-purple-600 text-sm"></i>
                                                    </div>
                                                    <span class="font-medium">CT Scan</span>
                                                </div>
                                            </button>
                                            <button type="button"
                                                class="text-left p-3 border border-gray-200 rounded-lg hover:bg-yellow-50 transition-colors duration-150 test-quick-select"
                                                data-test-type="photo" data-test-name="Clinical Photo">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-camera text-yellow-600 text-sm"></i>
                                                    </div>
                                                    <span class="font-medium">Clinical Photo</span>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                                        placeholder="Enter detailed instructions for the test. What needs to be tested? Any special requirements?">{{ old('requested_notes') }}</textarea>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p>Provide clear instructions for:</p>
                                        <ul class="list-disc list-inside mt-1">
                                            <li>What specific tests are needed</li>
                                            <li>Special requirements or preparation</li>
                                            <li>Areas of focus or concern</li>
                                            <li>Any clinical notes</li>
                                        </ul>
                                    </div>
                                    @error('requested_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Dates and additional info -->
                        <div class="space-y-6">
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
                                            value="{{ old('expected_delivery_date') }}" min="{{ date('Y-m-d') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <p class="mt-1 text-xs text-gray-500">
                                            When do you expect to receive the test results?
                                        </p>
                                        @error('expected_delivery_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Requested By (Auto-filled) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Requested By
                                        </label>
                                        <div
                                            class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-md">
                                            <div
                                                class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user-md text-gray-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ auth()->user()->full_name }}</p>
                                                <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="requested_by" value="{{ auth()->id() }}">
                                    </div>

                                    <!-- Test Status Info -->
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                                            <div>
                                                <h4 class="font-medium text-yellow-800">Test Request Process</h4>
                                                <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                                                    <li>Test will be marked as "Requested" initially</li>
                                                    <li>Patient can go for the test with these instructions</li>
                                                    <li>Staff will upload results when received</li>
                                                    <li>Status automatically updates to "Completed" after upload</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ADDITIONAL INFO -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Test Code (Auto-generated)
                                            </label>
                                            <div class="px-3 py-2 bg-white border border-gray-300 rounded-md">
                                                <code
                                                    class="text-sm font-mono text-gray-700">MF-{{ \App\Models\MedicalFile::generateFileCode() }}</code>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Request Date
                                            </label>
                                            <div class="px-3 py-2 bg-white border border-gray-300 rounded-md">
                                                <span class="text-sm text-gray-700">{{ now()->format('F d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==============================================
                        FORM ACTIONS
                        Submit and cancel buttons
                    ============================================== -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            @if ($currentTreatment)
                                <a href="{{ route('backend.treatments.show', $currentTreatment) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-150">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Treatment
                                </a>
                            @elseif($currentPatient)
                                <a href="{{ route('backend.patients.show', $currentPatient) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-150">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Patient
                                </a>
                            @else
                                <a href="{{ route('backend.medical-files.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-150">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Tests
                                </a>
                            @endif
                        </div>
                        <div class="space-x-3">
                            <button type="button" onclick="clearForm()"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors duration-150">
                                <i class="fas fa-times mr-2"></i>
                                Clear Form
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-150">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Request Test
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================
            JAVASCRIPT SECTION
            Handles test selection and form interactions
        ============================================== -->
    <script>
        // ==============================================
        // QUICK TEST SELECTION
        // ==============================================
        document.querySelectorAll('.test-quick-select').forEach(button => {
            button.addEventListener('click', function() {
                const testType = this.getAttribute('data-test-type');
                const testName = this.getAttribute('data-test-name');

                // Set the test type
                document.getElementById('file_type').value = testType;

                // Visual feedback
                this.classList.add('ring-2', 'ring-blue-300');
                setTimeout(() => {
                    this.classList.remove('ring-2', 'ring-blue-300');
                }, 500);

                // Show toast notification
                showSelectionFeedback(testName);
            });
        });

        // ==============================================
        // FORM VALIDATION & ENHANCEMENT
        // ==============================================
        function validateForm() {
            const testType = document.getElementById('file_type').value;
            const instructions = document.getElementById('requested_notes').value.trim();

            if (!testType) {
                showError('Please select a test type');
                return false;
            }

            if (!instructions || instructions.length < 10) {
                showError('Please provide detailed instructions (at least 10 characters)');
                return false;
            }

            return true;
        }

        // ==============================================
        // UI HELPER FUNCTIONS
        // ==============================================
        function showSelectionFeedback(testName) {
            const toast = document.createElement('div');
            toast.className =
                'fixed top-4 right-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
            toast.innerHTML = `
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>${testName} selected</span>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('animate-fade-out');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

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

        function clearForm() {
            if (confirm('Are you sure you want to clear the form? All entered data will be lost.')) {
                document.querySelector('form').reset();
                document.getElementById('file_type').selectedIndex = 0;

                // Show confirmation
                const toast = document.createElement('div');
                toast.className =
                    'fixed top-4 right-4 bg-blue-100 border border-blue-300 text-blue-800 px-4 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                toast.innerHTML = `
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span>Form cleared</span>
                    </div>
                `;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.classList.add('animate-fade-out');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        }

        // ==============================================
        // FORM SUBMISSION HANDLING
        // ==============================================
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            } else {
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Requesting...';
                submitBtn.disabled = true;

                // Re-enable after 5 seconds in case submission fails
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            }
        });

        // ==============================================
        // PATIENT SELECTION HANDLING
        // ==============================================
        @if (!$currentPatient)
            document.getElementById('patient_id').addEventListener('change', function() {
                const patientId = this.value;
                if (patientId) {
                    // Redirect to same page with patient ID
                    window.location.href = `{{ route('backend.medical-files.create') }}?patient_id=${patientId}`;
                }
            });
        @endif
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
    </style>
@endsection
