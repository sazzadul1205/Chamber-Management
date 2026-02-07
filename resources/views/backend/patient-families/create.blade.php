@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Family</h1>
                <p class="text-gray-600 mt-1">
                    Create a new family group and assign a head of family
                </p>
            </div>

            <!-- New Patient Button -->
            <button type="button" id="new-patient-btn"
                class="flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow transition">
                @include('partials.sidebar-icon', [
                    'name' => 'B_Add',
                    'class' => 'w-4 h-4',
                ])
                New Patient
            </button>
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
            <form action="{{ route('backend.patient-families.store') }}" method="POST" id="family-form">
                @csrf

                <div class="p-6 space-y-6">

                    <!-- FAMILY INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Family Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Family Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Family Name *
                                </label>
                                <input type="text" name="family_name" value="{{ old('family_name') }}" required
                                    placeholder="e.g., Smith Family, Johnson Family"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Patient Search -->
                            <div class="relative md:col-span-2">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Head of Family (Patient) *
                                    </label>
                                    <span class="text-xs text-gray-500">
                                        <a href="#" class="text-blue-600 hover:text-blue-800" id="quick-patient-link">
                                            Quick Create
                                        </a>
                                    </span>
                                </div>

                                <div class="flex gap-2">
                                    <input type="text" id="patient_search"
                                        placeholder="Search patient by name, code, or phone..."
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        autocomplete="off">

                                    <button type="button" id="clear-patient"
                                        class="px-3 py-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50 hidden">
                                        Clear
                                    </button>
                                </div>

                                <!-- Dropdown -->
                                <ul id="patient_results"
                                    class="absolute left-0 right-0 mt-1 border border-gray-300 rounded-md max-h-60 overflow-auto bg-white shadow-lg hidden z-50">
                                </ul>

                                <input type="hidden" name="head_patient_id" id="patient_id"
                                    value="{{ old('head_patient_id') }}">

                                <!-- Selected Patient Info -->
                                <div id="selected-patient-info" class="mt-2 hidden">
                                    <div
                                        class="flex items-center justify-between p-2 bg-blue-50 rounded border border-blue-200">
                                        <div>
                                            <span class="text-sm font-medium text-blue-700"
                                                id="selected-patient-name"></span>
                                            <span class="text-xs text-blue-600 ml-2" id="selected-patient-phone"></span>
                                            <span class="text-xs text-gray-500 ml-2" id="selected-patient-code"></span>
                                        </div>
                                        <button type="button" id="remove-patient"
                                            class="text-red-500 hover:text-red-700 text-sm font-medium">
                                            Remove
                                        </button>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-500 mt-2">
                                    This patient will be set as the family head and primary contact
                                </p>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons back-url="{{ route('backend.patient-families.index') }}"
                        submit-text="Create Family" />
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Create Patient Modal -->
    <div id="quick-create-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Patient',
                        'class' => 'w-5 h-5 text-green-600',
                    ])
                    Quick Create Patient
                </h3>
            </div>

            <form id="quick-patient-form" method="POST">
                <!-- CSRF Token for the quick create form -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="p-6 space-y-4">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name *
                        </label>
                        <input type="text" name="full_name" id="quick-full-name" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter patient's full name">
                        <p class="text-xs text-gray-500 mt-1">Required for patient identification</p>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number *
                        </label>
                        <input type="tel" name="phone" id="quick-phone" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="e.g., 017XXXXXXXX">
                        <p class="text-xs text-gray-500 mt-1">Will be used for appointment reminders</p>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Gender
                        </label>
                        <select name="gender" id="quick-gender"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Gender (Optional)</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth
                        </label>
                        <input type="date" name="date_of_birth" id="quick-dob"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Form Status Messages -->
                    <div id="quick-create-messages" class="hidden">
                        <!-- Success/Error messages will appear here -->
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" id="cancel-quick-create"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="submit-quick-create"
                        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 flex items-center gap-2">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Tick',
                            'class' => 'w-4 h-4',
                        ])
                        Create Patient
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Patient Search
            const patientInput = document.getElementById('patient_search');
            const patientResults = document.getElementById('patient_results');
            const patientHidden = document.getElementById('patient_id');
            const clearPatientBtn = document.getElementById('clear-patient');
            const selectedPatientInfo = document.getElementById('selected-patient-info');
            const selectedPatientName = document.getElementById('selected-patient-name');
            const selectedPatientPhone = document.getElementById('selected-patient-phone');
            const selectedPatientCode = document.getElementById('selected-patient-code');
            const removePatientBtn = document.getElementById('remove-patient');
            const quickPatientLink = document.getElementById('quick-patient-link');
            let patientTimeout = null;

            // Quick Create Modal Elements
            const newPatientBtn = document.getElementById('new-patient-btn');
            const quickCreateModal = document.getElementById('quick-create-modal');
            const cancelQuickCreateBtn = document.getElementById('cancel-quick-create');
            const quickPatientForm = document.getElementById('quick-patient-form');
            const quickCreateMessages = document.getElementById('quick-create-messages');
            const submitQuickCreateBtn = document.getElementById('submit-quick-create');

            // Initialize patient search
            initializePatientSearch();

            // Clear patient button
            clearPatientBtn.addEventListener('click', clearPatientSelection);

            // Remove patient button
            removePatientBtn.addEventListener('click', clearPatientSelection);

            // Quick patient link
            quickPatientLink.addEventListener('click', function(e) {
                e.preventDefault();
                openQuickCreateModal();
            });

            // New patient button
            newPatientBtn.addEventListener('click', openQuickCreateModal);

            // Cancel quick create
            cancelQuickCreateBtn.addEventListener('click', closeQuickCreateModal);

            // Quick patient form submission
            quickPatientForm.addEventListener('submit', handleQuickCreateSubmit);

            // Close modal when clicking outside
            quickCreateModal.addEventListener('click', function(e) {
                if (e.target === quickCreateModal) {
                    closeQuickCreateModal();
                }
            });

            // Close with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !quickCreateModal.classList.contains('hidden')) {
                    closeQuickCreateModal();
                }
            });

            // Pre-populate patient if already selected
            @if (old('head_patient_id'))
                fetchPatientData({{ old('head_patient_id') }});
            @endif

            // Form validation
            document.getElementById('family-form').addEventListener('submit', function(e) {
                if (!patientHidden.value) {
                    e.preventDefault();
                    alert('Please select a patient as head of family from the search results');
                    patientInput.focus();
                }
            });

            function initializePatientSearch() {
                function searchPatients(query) {
                    fetch(`/api/patients?search=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            patientResults.innerHTML = '';

                            if (data.length === 0) {
                                patientResults.innerHTML =
                                    `<li class="px-3 py-2 text-gray-500 cursor-default">No patients found</li>`;
                            } else {
                                data.forEach(patient => {
                                    const li = document.createElement('li');
                                    li.className =
                                        "px-3 py-2 cursor-pointer hover:bg-blue-100 border-b last:border-b-0";
                                    li.innerHTML = `
                                        <div class="font-medium">${patient.full_name}</div>
                                        <div class="text-xs text-gray-600">
                                            ${patient.patient_code} | ${patient.phone || 'No phone'} | ${patient.gender || 'N/A'}
                                        </div>
                                    `;

                                    li.addEventListener('click', () => {
                                        selectPatient(patient);
                                    });
                                    patientResults.appendChild(li);
                                });
                            }

                            patientResults.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error(`Error searching patients:`, error);
                            patientResults.innerHTML =
                                `<li class="px-3 py-2 text-red-500 cursor-default">Error loading patients</li>`;
                            patientResults.classList.remove('hidden');
                        });
                }

                function showPlaceholder() {
                    patientResults.innerHTML =
                        `<li class="px-3 py-2 text-gray-400 italic cursor-default">Type at least 2 characters to search...</li>`;
                    patientResults.classList.remove('hidden');
                    patientHidden.value = '';

                    clearPatientBtn.classList.add('hidden');
                    selectedPatientInfo.classList.add('hidden');
                }

                patientInput.addEventListener('input', function() {
                    clearTimeout(patientTimeout);
                    const query = patientInput.value.trim();

                    // Clear hidden input if search is cleared
                    if (query.length === 0) {
                        patientHidden.value = '';
                        showPlaceholder();
                        return;
                    }

                    patientTimeout = setTimeout(() => {
                        searchPatients(query);
                    }, 300);
                });

                patientInput.addEventListener('focus', function() {
                    if (patientResults.innerHTML === '' && !patientHidden.value) {
                        showPlaceholder();
                    } else {
                        patientResults.classList.remove('hidden');
                    }
                });

                // Hide dropdown if click outside
                document.addEventListener('click', function(e) {
                    if (!patientResults.contains(e.target) && e.target !== patientInput) {
                        patientResults.classList.add('hidden');
                    }
                });
            }

            function fetchPatientData(id) {
                fetch(`/api/patients/${id}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Patient not found');
                        return response.json();
                    })
                    .then(patient => {
                        selectPatient(patient);
                    })
                    .catch(error => {
                        console.error(`Error fetching patient:`, error);
                        clearPatientSelection();
                    });
            }

            function selectPatient(patient) {
                patientHidden.value = patient.id;
                patientInput.value = `${patient.patient_code} - ${patient.full_name}`;

                // Update selected patient info
                selectedPatientName.textContent = patient.full_name;
                selectedPatientPhone.textContent = patient.phone ? `• ${patient.phone}` : '';
                selectedPatientCode.textContent = `• ${patient.patient_code}`;

                selectedPatientInfo.classList.remove('hidden');
                clearPatientBtn.classList.remove('hidden');
                patientResults.classList.add('hidden');

                // Disable input to prevent changes
                patientInput.disabled = true;
                patientInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            }

            function clearPatientSelection() {
                patientInput.value = '';
                patientHidden.value = '';
                patientInput.disabled = false;
                patientInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                patientResults.innerHTML = '';
                patientResults.classList.add('hidden');
                clearPatientBtn.classList.add('hidden');
                selectedPatientInfo.classList.add('hidden');
                patientInput.focus();
            }

            function openQuickCreateModal() {
                // Reset form
                quickPatientForm.reset();
                quickCreateMessages.innerHTML = '';
                quickCreateMessages.classList.add('hidden');

                // Set today as max date for DOB
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('quick-dob').max = today;

                // Show modal
                quickCreateModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                document.getElementById('quick-full-name').focus();
            }

            function closeQuickCreateModal() {
                quickCreateModal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            function handleQuickCreateSubmit(e) {
                e.preventDefault();

                // Clear previous messages
                quickCreateMessages.innerHTML = '';
                quickCreateMessages.classList.add('hidden');

                // Disable submit button and show loading
                submitQuickCreateBtn.disabled = true;
                submitQuickCreateBtn.innerHTML = `
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        Creating...
                    </div>
                `;

                // Get CSRF token from the hidden input in the quick create form
                const csrfToken = document.querySelector('#quick-patient-form input[name="_token"]').value;

                // Get form data
                const formData = new FormData(quickPatientForm);
                const data = {
                    full_name: formData.get('full_name'),
                    phone: formData.get('phone'),
                    gender: formData.get('gender') || null,
                    date_of_birth: formData.get('date_of_birth') || null
                };

                // Remove empty fields
                Object.keys(data).forEach(key => {
                    if (data[key] === null || data[key] === '') {
                        delete data[key];
                    }
                });

                // Send AJAX request to quick add endpoint
                fetch('{{ route('backend.patients.quick-add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Try to parse error response
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Server error occurred');
                            }).catch(() => {
                                throw new Error('Network response was not ok');
                            });
                        }
                        return response.json();
                    })
                    .then(result => {
                        if (result.success) {
                            // Auto-select the newly created patient
                            patientHidden.value = result.patient.id;
                            patientInput.value = `${result.patient.code} - ${result.patient.name}`;
                            selectPatient({
                                id: result.patient.id,
                                patient_code: result.patient.code,
                                full_name: result.patient.name,
                                phone: result.patient.phone
                            });

                            // Show success message
                            quickCreateMessages.innerHTML = `
                            <div class="p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-center gap-2 text-green-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-medium">${result.message || 'Patient created successfully!'}</span>
                                </div>
                                <p class="text-green-700 text-sm mt-1">
                                    Patient will be auto-selected. You can close this window.
                                </p>
                            </div>
                        `;
                            quickCreateMessages.classList.remove('hidden');

                            // Close modal after 2 seconds
                            setTimeout(() => {
                                closeQuickCreateModal();
                            }, 2000);
                        } else {
                            throw new Error(result.message || 'Failed to create patient');
                        }
                    })
                    .catch(error => {
                        console.error('Quick create error:', error);

                        // Show error message
                        quickCreateMessages.innerHTML = `
                        <div class="p-3 bg-red-50 border border-red-200 rounded-md">
                            <div class="flex items-center gap-2 text-red-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">Error creating patient</span>
                            </div>
                            <p class="text-red-700 text-sm mt-1">${error.message || 'Please check your network connection and try again.'}</p>
                        </div>
                    `;
                        quickCreateMessages.classList.remove('hidden');
                    })
                    .finally(() => {
                        // Reset submit button
                        submitQuickCreateBtn.disabled = false;
                        submitQuickCreateBtn.innerHTML = `
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Tick',
                            'class' => 'w-4 h-4',
                        ])
                        Create Patient
                    `;
                    });
            }
        });
    </script>
@endsection
