@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Patient</h1>
                <p class="text-gray-600 mt-1">
                    Update patient profile for <span class="font-medium">{{ $patient->full_name }}</span>
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

        <!-- PATIENT INFO CARD -->
        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 space-y-2">
            <div class="flex flex-wrap gap-4">
                <div>
                    <span class="text-sm font-medium text-blue-800">Patient Code:</span>
                    <span class="ml-2 text-blue-900 font-semibold">{{ $patient->patient_code }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-blue-800">Phone:</span>
                    <span class="ml-2 text-blue-900">{{ $patient->phone }}</span>
                </div>
                @if ($patient->email)
                    <div>
                        <span class="text-sm font-medium text-blue-800">Email:</span>
                        <span class="ml-2 text-blue-900">{{ $patient->email }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.patients.update', $patient->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    <!-- BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Name *
                                </label>
                                <input type="text" name="full_name" value="{{ old('full_name', $patient->full_name) }}"
                                    required placeholder="John Doe"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Phone Number *
                                </label>

                                <div class="flex">
                                    <!-- Fixed country code -->
                                    <span
                                        class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-100 text-gray-700 text-sm select-none">
                                        +880
                                    </span>

                                    <!-- User input -->
                                    @php
                                        $phoneLocal = old('phone_local');
                                        if (!$phoneLocal && $patient->phone) {
                                            $phoneLocal = substr($patient->phone, 4); // Remove +880
                                        }
                                    @endphp
                                    <input type="text" name="phone_local" value="{{ $phoneLocal }}" required
                                        placeholder="1XXXXXXXXX"
                                        class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Hidden full phone value -->
                            <input type="hidden" name="phone" id="phone_full"
                                value="{{ old('phone', $patient->phone) }}">

                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Gender
                                </label>
                                <select name="gender"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Gender</option>
                                    <option value="male" @selected(old('gender', $patient->gender) == 'male')>Male</option>
                                    <option value="female" @selected(old('gender', $patient->gender) == 'female')>Female</option>
                                    <option value="other" @selected(old('gender', $patient->gender) == 'other')>Other</option>
                                </select>
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Date of Birth
                                </label>
                                <input type="date" name="date_of_birth"
                                    value="{{ old('date_of_birth', optional($patient->date_of_birth)->format('Y-m-d')) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email
                                </label>
                                <input type="email" name="email" value="{{ old('email', $patient->email) }}"
                                    placeholder="example@gmail.com"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Emergency Contact -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Emergency Contact
                                </label>

                                <div class="flex">
                                    <!-- Fixed country code -->
                                    <span
                                        class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-100 text-gray-700 text-sm select-none">
                                        +880
                                    </span>

                                    <!-- User input -->
                                    @php
                                        $emergencyLocal = old('emergency_contact_local');
                                        if (!$emergencyLocal && $patient->emergency_contact) {
                                            $emergencyLocal = substr($patient->emergency_contact, 4); // Remove +880
                                        }
                                    @endphp
                                    <input type="text" name="emergency_contact_local" value="{{ $emergencyLocal }}"
                                        placeholder="1XXXXXXXXX"
                                        class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Hidden full phone value -->
                                <input type="hidden" name="emergency_contact" id="emergency_contact_full"
                                    value="{{ old('emergency_contact', $patient->emergency_contact) }}">
                            </div>


                            <!-- Referred By -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Referred By
                                </label>

                                <input type="text" id="referred_by_search"
                                    placeholder="Search by patient name, code, or phone" autocomplete="off"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    value="{{ optional($patients->firstWhere('id', old('referred_by', $patient->referred_by)))->patient_code
                                        ? optional($patients->firstWhere('id', old('referred_by', $patient->referred_by)))->patient_code .
                                            ' - ' .
                                            optional($patients->firstWhere('id', old('referred_by', $patient->referred_by)))->full_name
                                        : '' }}">

                                <ul id="referred_by_results"
                                    class="absolute z-50 w-full border border-gray-300 rounded-md mt-1 bg-white max-h-60 overflow-auto hidden">
                                </ul>

                                <input type="hidden" name="referred_by" id="referred_by_id"
                                    value="{{ old('referred_by', $patient->referred_by) }}">
                            </div>


                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active" @selected(old('status', $patient->status) == 'active')>Active</option>
                                    <option value="inactive" @selected(old('status', $patient->status) == 'inactive')>Inactive</option>
                                    <option value="deceased" @selected(old('status', $patient->status) == 'deceased')>Deceased</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- ADDITIONAL DETAILS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Address
                                </label>
                                <textarea name="address" rows="2"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('address', $patient->address) }}</textarea>
                            </div>

                            <!-- Medical History -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Medical History
                                </label>
                                <textarea name="medical_history" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('medical_history', $patient->medical_history) }}</textarea>
                            </div>

                            <!-- Allergies -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Allergies
                                </label>
                                <textarea name="allergies" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('allergies', $patient->allergies) }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons back-url="{{ route('backend.patients.index') }}"
                        submit-text="Update Patient" />
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        // Generate phone numbers
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.querySelector('[name="phone_local"]');
            const phoneFull = document.getElementById('phone_full');

            const emergencyInput = document.querySelector('[name="emergency_contact_local"]');
            const emergencyFull = document.getElementById('emergency_contact_full');

            function updatePhone() {
                phoneFull.value = '+880' + phoneInput.value.replace(/\D/g, '');
            }

            function updateEmergency() {
                emergencyFull.value = '+880' + emergencyInput.value.replace(/\D/g, '');
            }

            phoneInput.addEventListener('input', updatePhone);
            emergencyInput.addEventListener('input', updateEmergency);

            // Initialize values
            updatePhone();
            updateEmergency();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('referred_by_search');
            const results = document.getElementById('referred_by_results');
            const hiddenInput = document.getElementById('referred_by_id');
            let debounceTimer = null;

            function showMessage(message) {
                results.innerHTML = `
            <li class="px-3 py-2 text-sm text-gray-500">
                ${message}
            </li>
        `;
                results.classList.remove('hidden');
            }

            input.addEventListener('focus', () => {
                if (!input.value.trim()) {
                    showMessage('Start typing to search for a patient');
                }
            });

            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = input.value.trim();

                if (!query) {
                    hiddenInput.value = '';
                    showMessage('Please enter a patient name, code, or phone');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`/api/patients?search=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            results.innerHTML = '';

                            if (!data.length) {
                                showMessage('No matching patients found');
                                return;
                            }

                            data.forEach(patient => {
                                const li = document.createElement('li');
                                li.className =
                                    'px-3 py-2 cursor-pointer text-sm hover:bg-blue-100';
                                li.textContent =
                                    `${patient.patient_code} - ${patient.full_name} (${patient.phone})`;

                                li.addEventListener('click', () => {
                                    input.value =
                                        `${patient.patient_code} - ${patient.full_name}`;
                                    hiddenInput.value = patient.id;
                                    results.classList.add('hidden');
                                });

                                results.appendChild(li);
                            });

                            results.classList.remove('hidden');
                        })
                        .catch(() => {
                            showMessage('Unable to load patients. Please try again.');
                        });
                }, 300);
            });

            document.addEventListener('click', function(e) {
                if (!results.contains(e.target) && e.target !== input) {
                    results.classList.add('hidden');
                }
            });
        });
    </script>

@endsection
