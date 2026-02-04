@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Schedule New Appointment</h1>
                <p class="text-gray-600 mt-1">
                    Create a new appointment with patient and doctor details
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
            <form action="{{ route('backend.appointments.store') }}" method="POST" id="appointment-form">
                @csrf

                <div class="p-6 space-y-6">

                    <!-- PATIENT & DOCTOR INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Patient & Doctor Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Patient Search -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Patient *
                                </label>
                                <input type="text" id="patient_search" placeholder="Search patient by name or code..."
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    autocomplete="off">

                                <!-- Dropdown -->
                                <ul id="patient_results"
                                    class="absolute left-0 right-0 mt-1 border border-gray-300 rounded-md max-h-60 overflow-auto bg-white shadow-lg hidden z-50">
                                </ul>

                                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                            </div>

                            <!-- Doctor Search -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Doctor *
                                </label>
                                <input type="text" id="doctor_search" placeholder="Search doctor by name..."
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    autocomplete="off">

                                <!-- Dropdown -->
                                <ul id="doctor_results"
                                    class="absolute left-0 right-0 mt-1 border border-gray-300 rounded-md max-h-60 overflow-auto bg-white shadow-lg hidden z-50">
                                </ul>

                                <input type="hidden" name="doctor_id" id="doctor_id" value="{{ old('doctor_id') }}">
                            </div>

                        </div>
                    </div>

                    <!-- APPOINTMENT DETAILS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Appointment Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Appointment Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Appointment Date *
                                </label>
                                <input type="date" name="appointment_date" id="appointment-date"
                                    value="{{ old('appointment_date', date('Y-m-d')) }}" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Expected Arrival Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Expected Arrival Time * (<span class="text-xs text-gray-500 mb-1">
                                        Time is approximate. Patients are served in arrival order.
                                    </span>)
                                </label>

                                <input type="time" name="appointment_time" value="{{ old('appointment_time') }}" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Duration (minutes) *
                                </label>
                                <input type="number" name="expected_duration" min="5" max="240" step="5"
                                    required value="{{ old('expected_duration', 30) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Dental Chair -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Dental Chair
                                </label>
                                <select name="chair_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Chair (Optional)</option>
                                    @foreach ($chairs as $chair)
                                        <option value="{{ $chair->id }}"
                                            {{ old('chair_id') == $chair->id ? 'selected' : '' }}>
                                            {{ $chair->chair_code }} - {{ $chair->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Appointment Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Appointment Type *
                                </label>
                                <select name="appointment_type" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach (App\Models\Appointment::appointmentTypes() as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('appointment_type') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Priority *
                                </label>
                                <select name="priority" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach (App\Models\Appointment::priorities() as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('priority') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- ADDITIONAL INFORMATION -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>

                        <div class="space-y-6">

                            <!-- Chief Complaint -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chief Complaint / Reason
                                </label>
                                <textarea name="chief_complaint" rows="2"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('chief_complaint') }}</textarea>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Notes
                                </label>
                                <textarea name="notes" rows="2"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons back-url="{{ route('backend.appointments.index') }}"
                        submit-text="Schedule Appointment" />
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
            let patientTimeout = null;

            // Doctor Search
            const doctorInput = document.getElementById('doctor_search');
            const doctorResults = document.getElementById('doctor_results');
            const doctorHidden = document.getElementById('doctor_id');
            let doctorTimeout = null;

            // Initialize patient search
            initializeSearch({
                input: patientInput,
                results: patientResults,
                hidden: patientHidden,
                timeoutVar: patientTimeout,
                endpoint: 'patients',
                displayFormat: (item) => `${item.patient_code} - ${item.full_name} (${item.phone})`,
                inputFormat: (item) => `${item.patient_code} - ${item.full_name}`
            });

            // Initialize doctor search
            initializeSearch({
                input: doctorInput,
                results: doctorResults,
                hidden: doctorHidden,
                timeoutVar: doctorTimeout,
                endpoint: 'doctors',
                displayFormat: (item) => `${item.full_name} - ${item.specialization}`,
                inputFormat: (item) => `${item.full_name} - ${item.specialization}`
            });

            // Pre-populate patient if already selected
            @if (old('patient_id'))
                fetchData({{ old('patient_id') }}, 'patients', patientInput, (item) =>
                    `${item.patient_code} - ${item.full_name}`
                );
            @endif

            // Pre-populate doctor if already selected
            @if (old('doctor_id'))
                fetchData({{ old('doctor_id') }}, 'doctors', doctorInput, (item) =>
                    `${item.full_name} - ${item.specialization}`
                );
            @endif

            // Form validation
            document.getElementById('appointment-form').addEventListener('submit', function(e) {
                if (!patientHidden.value) {
                    e.preventDefault();
                    alert('Please select a patient from the search results');
                    patientInput.focus();
                    return;
                }

                if (!doctorHidden.value) {
                    e.preventDefault();
                    alert('Please select a doctor from the search results');
                    doctorInput.focus();
                }
            });

            function initializeSearch(config) {
                const {
                    input,
                    results,
                    hidden,
                    endpoint,
                    displayFormat,
                    inputFormat
                } = config;

                function searchData(query) {
                    fetch(`/api/${endpoint}?search=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            results.innerHTML = '';

                            if (data.length === 0) {
                                results.innerHTML =
                                    `<li class="px-3 py-2 text-gray-500 cursor-default">No ${endpoint} found</li>`;
                            } else {
                                data.forEach(item => {
                                    const li = document.createElement('li');
                                    li.className =
                                        "px-3 py-2 cursor-pointer hover:bg-blue-100 border-b last:border-b-0";
                                    li.textContent = displayFormat(item);

                                    li.addEventListener('click', () => {
                                        input.value = inputFormat(item);
                                        hidden.value = item.id;
                                        results.classList.add('hidden');
                                    });
                                    results.appendChild(li);
                                });
                            }

                            results.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error(`Error searching ${endpoint}:`, error);
                            results.innerHTML =
                                `<li class="px-3 py-2 text-red-500 cursor-default">Error loading ${endpoint}</li>`;
                            results.classList.remove('hidden');
                        });
                }

                function showPlaceholder() {
                    results.innerHTML =
                        `<li class="px-3 py-2 text-gray-400 italic cursor-default">Start typing to search ${endpoint}...</li>`;
                    results.classList.remove('hidden');
                    hidden.value = '';
                }

                input.addEventListener('input', function() {
                    clearTimeout(config.timeoutVar);
                    const query = input.value.trim();

                    // Clear hidden input if search is cleared
                    if (query.length === 0) {
                        hidden.value = '';
                        showPlaceholder();
                        return;
                    }

                    config.timeoutVar = setTimeout(() => {
                        searchData(query);
                    }, 300); // debounce
                });

                input.addEventListener('focus', function() {
                    if (results.innerHTML === '' && !hidden.value) {
                        showPlaceholder();
                    } else {
                        results.classList.remove('hidden');
                    }
                });

                // Hide dropdown if click outside
                document.addEventListener('click', function(e) {
                    if (!results.contains(e.target) && e.target !== input) {
                        results.classList.add('hidden');
                    }
                });
            }

            function fetchData(id, endpoint, inputElement, formatFunction) {
                fetch(`/api/${endpoint}/${id}`)
                    .then(response => {
                        if (!response.ok) throw new Error(`${endpoint} not found`);
                        return response.json();
                    })
                    .then(data => {
                        inputElement.value = formatFunction(data);
                    })
                    .catch(error => {
                        console.error(`Error fetching ${endpoint}:`, error);
                    });
            }
        });
    </script>
@endsection
