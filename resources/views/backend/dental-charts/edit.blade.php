@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Dental Chart Record</h1>
                <p class="text-gray-600 mt-1">
                    Update tooth details, condition, and follow-up information
                </p>
            </div>

            <a href="{{ route('backend.dental-charts.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                @include('partials.sidebar-icon', [
                    'name' => 'B_Back',
                    'class' => 'w-4 h-4',
                ])
                Back to Dental Records
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
            <form action="{{ route('backend.dental-charts.update', $dentalChart->id) }}" method="POST"
                id="dental-chart-edit-form">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    <!-- CHART DETAILS -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Chart Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Patient *
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" id="patient_search"
                                        placeholder="Search patient by name or code..."
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        autocomplete="off">
                                    <button type="button" id="clear-patient"
                                        class="px-3 py-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50 hidden">
                                        Clear
                                    </button>
                                </div>

                                <ul id="patient_results"
                                    class="absolute left-0 right-0 mt-1 border border-gray-300 rounded-md max-h-60 overflow-auto bg-white shadow-lg hidden z-50">
                                </ul>

                                <input type="hidden" name="patient_id" id="patient_id"
                                    value="{{ old('patient_id', $dentalChart->patient_id) }}">

                                <div id="selected-patient-info" class="mt-2 hidden">
                                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                                        <div>
                                            <span class="text-sm font-medium text-blue-700"
                                                id="selected-patient-name"></span>
                                            <span class="text-xs text-blue-600 ml-2" id="selected-patient-phone"></span>
                                        </div>
                                        <button type="button" id="remove-patient"
                                            class="text-red-500 hover:text-red-700 text-sm">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chart Date *
                                </label>
                                <input type="date" name="chart_date"
                                    value="{{ old('chart_date', $dentalChart->chart_date->format('Y-m-d')) }}" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('chart_date') border-red-500 @enderror">
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tooth Number *
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" id="tooth_search"
                                        placeholder="Search tooth number (e.g., 11, 26, 51)..."
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        autocomplete="off">
                                    <button type="button" id="clear-tooth"
                                        class="px-3 py-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50 hidden">
                                        Clear
                                    </button>
                                </div>

                                <ul id="tooth_results"
                                    class="absolute left-0 right-0 mt-1 border border-gray-300 rounded-md max-h-60 overflow-auto bg-white shadow-lg hidden z-50">
                                </ul>

                                <input type="hidden" name="tooth_number" id="tooth_number"
                                    value="{{ old('tooth_number', $dentalChart->tooth_number) }}">

                                <div id="selected-tooth-info" class="mt-2 hidden">
                                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                                        <div>
                                            <span class="text-sm font-medium text-blue-700"
                                                id="selected-tooth-number"></span>
                                            <span class="text-xs text-blue-600 ml-2" id="selected-tooth-type"></span>
                                        </div>
                                        <button type="button" id="remove-tooth"
                                            class="text-red-500 hover:text-red-700 text-sm">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Surface
                                </label>
                                <select name="surface"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('surface') border-red-500 @enderror">
                                    <option value="">Select Surface</option>
                                    @foreach (App\Models\DentalChart::surfaces() as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('surface', $dentalChart->surface) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Condition *
                                </label>
                                <select name="condition" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('condition') border-red-500 @enderror">
                                    <option value="">Select Condition</option>
                                    @foreach (App\Models\DentalChart::conditions() as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('condition', $dentalChart->condition) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Procedure Done
                                </label>
                                <select name="procedure_done"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('procedure_done') border-red-500 @enderror">
                                    <option value="">Select Procedure</option>
                                    @foreach (App\Models\DentalChart::procedures() as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('procedure_done', $dentalChart->procedure_done) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Next Checkup
                                </label>
                                <input type="date" name="next_checkup"
                                    value="{{ old('next_checkup', optional($dentalChart->next_checkup)->format('Y-m-d')) }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('next_checkup') border-red-500 @enderror">
                            </div>

                        </div>
                    </div>

                    <!-- ADDITIONAL INFORMATION -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Remarks
                            </label>
                            <textarea name="remarks" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('remarks') border-red-500 @enderror">{{ old('remarks', $dentalChart->remarks) }}</textarea>
                        </div>
                    </div>

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-edit-page-buttons back-url="{{ route('backend.dental-charts.index') }}" submit-text="Update Record"
                        delete-modal-id="deleteModal" submit-color="blue" />
                </div>
            </form>
        </div>
    </div>

    <x-delete-modal id="deleteModal" title="Delete Dental Chart"
        message="Are you sure you want to delete this dental chart record?" :route="route('backend.dental-charts.destroy', $dentalChart->id)" />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('dental-chart-edit-form');

            const patientInput = document.getElementById('patient_search');
            const patientResults = document.getElementById('patient_results');
            const patientHidden = document.getElementById('patient_id');
            const clearPatientBtn = document.getElementById('clear-patient');
            const selectedPatientInfo = document.getElementById('selected-patient-info');
            const selectedPatientName = document.getElementById('selected-patient-name');
            const selectedPatientPhone = document.getElementById('selected-patient-phone');
            const removePatientBtn = document.getElementById('remove-patient');

            const toothInput = document.getElementById('tooth_search');
            const toothResults = document.getElementById('tooth_results');
            const toothHidden = document.getElementById('tooth_number');
            const clearToothBtn = document.getElementById('clear-tooth');
            const selectedToothInfo = document.getElementById('selected-tooth-info');
            const selectedToothNumber = document.getElementById('selected-tooth-number');
            const selectedToothType = document.getElementById('selected-tooth-type');
            const removeToothBtn = document.getElementById('remove-tooth');

            const toothOptions = [
                @foreach (App\Models\DentalChart::adultTeeth() as $tooth)
                    { number: '{{ $tooth }}', type: 'Permanent', name: '{{ App\Models\DentalChart::toothNames()[$tooth] ?? "Tooth $tooth" }}' },
                @endforeach
                @foreach (App\Models\DentalChart::childTeeth() as $tooth)
                    { number: '{{ $tooth }}', type: 'Primary', name: '{{ App\Models\DentalChart::toothNames()[$tooth] ?? "Tooth $tooth" }}' },
                @endforeach
            ];

            let patientTimeout = null;
            let toothTimeout = null;

            function renderPatientPlaceholder() {
                patientResults.innerHTML =
                    '<li class="px-3 py-2 text-gray-400 italic cursor-default">Start typing to search patients...</li>';
                patientResults.classList.remove('hidden');
            }

            function renderNoPatientResults() {
                patientResults.innerHTML =
                    '<li class="px-3 py-2 text-gray-500 cursor-default">No patients found</li>';
                patientResults.classList.remove('hidden');
            }

            function updateSelectedPatient(patient) {
                selectedPatientName.textContent = `${patient.patient_code} - ${patient.full_name}`;
                selectedPatientPhone.textContent = patient.phone ?? '';
                selectedPatientInfo.classList.remove('hidden');
                clearPatientBtn.classList.add('hidden');
            }

            function clearPatientSelection() {
                patientInput.value = '';
                patientHidden.value = '';
                patientResults.innerHTML = '';
                patientResults.classList.add('hidden');
                clearPatientBtn.classList.add('hidden');
                selectedPatientInfo.classList.add('hidden');
                patientInput.focus();
            }

            function searchPatients(query) {
                fetch(`/api/patients?search=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        patientResults.innerHTML = '';
                        if (!Array.isArray(data) || data.length === 0) {
                            renderNoPatientResults();
                            return;
                        }

                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'px-3 py-2 cursor-pointer hover:bg-blue-100 border-b last:border-b-0';
                            li.textContent = `${item.patient_code} - ${item.full_name} (${item.phone})`;
                            li.addEventListener('click', () => {
                                patientInput.value = `${item.patient_code} - ${item.full_name}`;
                                patientHidden.value = item.id;
                                patientResults.classList.add('hidden');
                                updateSelectedPatient(item);
                                patientInput.blur();
                            });
                            patientResults.appendChild(li);
                        });

                        patientResults.classList.remove('hidden');
                    })
                    .catch(() => {
                        patientResults.innerHTML =
                            '<li class="px-3 py-2 text-red-500 cursor-default">Error loading patients</li>';
                        patientResults.classList.remove('hidden');
                    });
            }

            function fetchPatientById(id) {
                fetch(`/api/patients/${id}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Patient not found');
                        return response.json();
                    })
                    .then(patient => {
                        patientInput.value = `${patient.patient_code} - ${patient.full_name}`;
                        patientHidden.value = patient.id;
                        updateSelectedPatient(patient);
                    })
                    .catch(() => {
                        clearPatientSelection();
                    });
            }

            function renderToothPlaceholder() {
                toothResults.innerHTML =
                    '<li class="px-3 py-2 text-gray-400 italic cursor-default">Start typing to search tooth numbers...</li>';
                toothResults.classList.remove('hidden');
            }

            function renderNoToothResults() {
                toothResults.innerHTML =
                    '<li class="px-3 py-2 text-gray-500 cursor-default">No matching tooth number found</li>';
                toothResults.classList.remove('hidden');
            }

            function updateSelectedTooth(tooth) {
                selectedToothNumber.textContent = `Tooth ${tooth.number}`;
                selectedToothType.textContent = `${tooth.type}: ${tooth.name}`;
                selectedToothInfo.classList.remove('hidden');
                clearToothBtn.classList.add('hidden');
            }

            function clearToothSelection() {
                toothInput.value = '';
                toothHidden.value = '';
                toothResults.innerHTML = '';
                toothResults.classList.add('hidden');
                clearToothBtn.classList.add('hidden');
                selectedToothInfo.classList.add('hidden');
                toothInput.focus();
            }

            function searchTeeth(query) {
                const normalized = query.trim().toLowerCase();
                const filtered = toothOptions.filter(item =>
                    item.number.toLowerCase().includes(normalized) ||
                    item.name.toLowerCase().includes(normalized)
                );
                toothResults.innerHTML = '';

                if (!filtered.length) {
                    renderNoToothResults();
                    return;
                }

                filtered.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-2 cursor-pointer hover:bg-blue-100 border-b last:border-b-0';
                    li.textContent = `Tooth ${item.number} - ${item.name} (${item.type})`;
                    li.addEventListener('click', () => {
                        toothInput.value = `Tooth ${item.number}`;
                        toothHidden.value = item.number;
                        toothResults.classList.add('hidden');
                        updateSelectedTooth(item);
                        toothInput.blur();
                    });
                    toothResults.appendChild(li);
                });

                toothResults.classList.remove('hidden');
            }

            patientInput.addEventListener('input', function() {
                clearTimeout(patientTimeout);
                const query = this.value.trim();

                if (!query.length) {
                    patientHidden.value = '';
                    selectedPatientInfo.classList.add('hidden');
                    clearPatientBtn.classList.add('hidden');
                    renderPatientPlaceholder();
                    return;
                }

                patientTimeout = setTimeout(() => {
                    searchPatients(query);
                }, 300);
            });

            patientInput.addEventListener('focus', function() {
                if (!this.value.trim() || !patientHidden.value) {
                    renderPatientPlaceholder();
                } else if (patientResults.innerHTML !== '') {
                    patientResults.classList.remove('hidden');
                }
            });

            toothInput.addEventListener('input', function() {
                clearTimeout(toothTimeout);
                const query = this.value.trim();

                if (!query.length) {
                    toothHidden.value = '';
                    selectedToothInfo.classList.add('hidden');
                    clearToothBtn.classList.add('hidden');
                    renderToothPlaceholder();
                    return;
                }

                toothTimeout = setTimeout(() => {
                    searchTeeth(query);
                }, 150);
            });

            toothInput.addEventListener('focus', function() {
                if (!this.value.trim() || !toothHidden.value) {
                    renderToothPlaceholder();
                } else if (toothResults.innerHTML !== '') {
                    toothResults.classList.remove('hidden');
                }
            });

            clearPatientBtn.addEventListener('click', clearPatientSelection);
            removePatientBtn.addEventListener('click', clearPatientSelection);
            clearToothBtn.addEventListener('click', clearToothSelection);
            removeToothBtn.addEventListener('click', clearToothSelection);

            document.addEventListener('click', function(e) {
                if (!patientResults.contains(e.target) && e.target !== patientInput) {
                    patientResults.classList.add('hidden');
                }
                if (!toothResults.contains(e.target) && e.target !== toothInput) {
                    toothResults.classList.add('hidden');
                }
            });

            form.addEventListener('submit', function(e) {
                if (!patientHidden.value) {
                    e.preventDefault();
                    alert('Please select a patient from the search results');
                    patientInput.focus();
                    return;
                }

                if (!toothHidden.value) {
                    e.preventDefault();
                    alert('Please select a tooth number from the search results');
                    toothInput.focus();
                }
            });

            fetchPatientById({{ old('patient_id', $dentalChart->patient_id) }});

            const initialTooth = toothOptions.find(item => item.number === '{{ old('tooth_number', $dentalChart->tooth_number) }}');
            if (initialTooth) {
                toothHidden.value = initialTooth.number;
                toothInput.value = `Tooth ${initialTooth.number}`;
                updateSelectedTooth(initialTooth);
            }
        });
    </script>
@endsection
