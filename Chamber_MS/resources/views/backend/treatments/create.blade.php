@extends('backend.layout.structure')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Create New Treatment</h2>
        </div>

        <!-- Appointment Selection Section -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium mb-4">Select Appointment (Optional)</h3>

            <!-- Appointment Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Filter by Patient</label>
                    <select id="filterPatient" class="mt-1 block w-full border rounded px-3 py-2">
                        <option value="">All Patients</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->full_name }} ({{ $patient->patient_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Filter by Doctor</label>
                    <select id="filterDoctor" class="mt-1 block w-full border rounded px-3 py-2">
                        <option value="">All Doctors</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">
                                {{ $doctor->user->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Filter by Date</label>
                    <input type="date" id="filterDate" class="mt-1 block w-full border rounded px-3 py-2">
                </div>
            </div>

            <!-- Appointments Table -->
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Select</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chief Complaint</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="appointmentsTable">
                        @foreach($appointments as $appointment)
                            <tr class="appointment-row" 
                                data-patient="{{ $appointment->patient_id }}"
                                data-doctor="{{ $appointment->doctor_id }}"
                                data-date="{{ $appointment->appointment_date->format('Y-m-d') }}">
                                <td class="px-4 py-3">
                                    <input type="radio" 
                                           name="appointment_id" 
                                           value="{{ $appointment->id }}"
                                           class="appointment-radio"
                                           data-patient="{{ $appointment->patient_id }}"
                                           data-doctor="{{ $appointment->doctor_id }}"
                                           data-diagnosis="{{ $appointment->chief_complaint }}"
                                           {{ old('appointment_id') == $appointment->id ? 'checked' : '' }}>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $appointment->appointment_code }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $appointment->patient->full_name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $appointment->doctor->user->full_name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $appointment->appointment_date->format('d/m/Y') }} 
                                    {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $appointment->status_color }}">
                                        {{ $appointment->status_text }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ Str::limit($appointment->chief_complaint, 50) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-sm text-gray-500">
                Showing {{ $appointments->count() }} appointments available for treatment creation.
            </div>
        </div>

        <!-- Treatment Form -->
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h3 class="text-lg font-medium mb-4">Treatment Details</h3>

            <form action="{{ route('backend.treatments.store') }}" method="POST">
                @csrf

                <!-- Hidden appointment ID -->
                <input type="hidden" name="appointment_id" id="selectedAppointmentId" value="{{ old('appointment_id') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Patient -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Patient *</label>
                        <select name="patient_id" id="patientSelect" required
                            class="mt-1 block w-full border rounded px-3 py-2 @error('patient_id') border-red-500 @enderror">
                            <option value="">Select Patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" 
                                    {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->patient_code }} - {{ $patient->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Doctor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Doctor *</label>
                        <select name="doctor_id" id="doctorSelect" required
                            class="mt-1 block w-full border rounded px-3 py-2 @error('doctor_id') border-red-500 @enderror">
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" 
                                    {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->user->full_name }} ({{ $doctor->specialization ?? 'General' }})
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Treatment Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Treatment Type *</label>
                        <select name="treatment_type" required
                            class="mt-1 block w-full border rounded px-3 py-2 @error('treatment_type') border-red-500 @enderror">
                            @foreach(App\Models\Treatment::treatmentTypes() as $key => $value)
                                <option value="{{ $key }}" {{ old('treatment_type') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('treatment_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimated Sessions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estimated Sessions *</label>
                        <input type="number" name="estimated_sessions" min="1" required
                            value="{{ old('estimated_sessions', 1) }}"
                            class="mt-1 block w-full border rounded px-3 py-2 @error('estimated_sessions') border-red-500 @enderror">
                        @error('estimated_sessions')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Treatment Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Treatment Date *</label>
                        <input type="date" name="treatment_date" required 
                            value="{{ old('treatment_date', date('Y-m-d')) }}"
                            class="mt-1 block w-full border rounded px-3 py-2 @error('treatment_date') border-red-500 @enderror">
                        @error('treatment_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" required
                            class="mt-1 block w-full border rounded px-3 py-2 @error('status') border-red-500 @enderror">
                            @foreach(App\Models\Treatment::statuses() as $key => $value)
                                <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Diagnosis -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Diagnosis *</label>
                    <textarea name="diagnosis" id="diagnosisTextarea" rows="3" required
                        class="mt-1 block w-full border rounded px-3 py-2 @error('diagnosis') border-red-500 @enderror">{{ old('diagnosis') }}</textarea>
                    @error('diagnosis')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Footer Buttons -->
                <div class="mt-6 flex justify-end gap-2">
                    <a href="{{ route('backend.treatments.index') }}"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Create Treatment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const appointmentRadios = document.querySelectorAll('.appointment-radio');
        const patientSelect = document.getElementById('patientSelect');
        const doctorSelect = document.getElementById('doctorSelect');
        const diagnosisTextarea = document.getElementById('diagnosisTextarea');
        const selectedAppointmentId = document.getElementById('selectedAppointmentId');

        const filterPatient = document.getElementById('filterPatient');
        const filterDoctor = document.getElementById('filterDoctor');
        const filterDate = document.getElementById('filterDate');
        const appointmentRows = document.querySelectorAll('.appointment-row');

        // Handle appointment selection
        function handleAppointmentSelection(appointmentRadio) {
            if (appointmentRadio.checked) {
                const patientId = appointmentRadio.getAttribute('data-patient');
                const doctorId = appointmentRadio.getAttribute('data-doctor');
                const diagnosis = appointmentRadio.getAttribute('data-diagnosis') || '';

                // Fill the form
                if (patientId) patientSelect.value = patientId;
                if (doctorId) doctorSelect.value = doctorId;
                if (diagnosis) diagnosisTextarea.value = diagnosis;

                // Set hidden input
                selectedAppointmentId.value = appointmentRadio.value;

                // Highlight selected row
                document.querySelectorAll('.appointment-row').forEach(row => {
                    row.classList.remove('bg-blue-50');
                });
                appointmentRadio.closest('tr').classList.add('bg-blue-50');
            }
        }

        // Add event listeners to appointment radios
        appointmentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                handleAppointmentSelection(this);
            });

            // Handle initial state if radio is checked
            if (radio.checked) {
                handleAppointmentSelection(radio);
            }
        });

        // Filter appointments
        function filterAppointments() {
            const patientFilter = filterPatient.value;
            const doctorFilter = filterDoctor.value;
            const dateFilter = filterDate.value;

            appointmentRows.forEach(row => {
                const patientId = row.getAttribute('data-patient');
                const doctorId = row.getAttribute('data-doctor');
                const date = row.getAttribute('data-date');

                let showRow = true;

                if (patientFilter && patientId !== patientFilter) {
                    showRow = false;
                }

                if (doctorFilter && doctorId !== doctorFilter) {
                    showRow = false;
                }

                if (dateFilter && date !== dateFilter) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }

        // Add event listeners to filters
        filterPatient.addEventListener('change', filterAppointments);
        filterDoctor.addEventListener('change', filterAppointments);
        filterDate.addEventListener('change', filterAppointments);

        // Initialize filters
        filterAppointments();
    });
    </script>

    <style>
    .appointment-row:hover {
        background-color: #f9fafb;
    }

    .appointment-row.bg-blue-50 {
        background-color: #eff6ff;
    }
    </style>
@endsection