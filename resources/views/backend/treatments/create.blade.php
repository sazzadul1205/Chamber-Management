@extends('backend.layout.structure')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold">Create New Treatment</h2>
    </div>

    <!-- Appointment Selection -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium mb-4">Select Appointment (Checked In)</h3>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Filter by Patient</label>
                <select id="filterPatient" class="mt-1 block w-full border rounded px-3 py-2">
                    <option value="">All Patients</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->patient_code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Filter by Doctor</label>
                <select id="filterDoctor" class="mt-1 block w-full border rounded px-3 py-2">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->user->full_name }}</option>
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
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase">Select</th>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase">Code</th>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase">Patient</th>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase">Doctor</th>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase">Date & Time</th>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase">Chief Complaint</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="appointmentsTable">
                    @foreach($appointments as $appointment)
                    <tr class="appointment-row hover:bg-gray-50" 
                        data-patient="{{ $appointment->patient_id }}"
                        data-doctor="{{ $appointment->doctor_id }}"
                        data-date="{{ $appointment->appointment_date->format('Y-m-d') }}">
                        <td class="px-3 py-2 text-center">
                            <input type="radio" name="appointment_id" value="{{ $appointment->id }}"
                                class="appointment-radio"
                                data-patient="{{ $appointment->patient_id }}"
                                data-doctor="{{ $appointment->doctor_id }}"
                                data-diagnosis="{{ $appointment->chief_complaint }}"
                                {{ old('appointment_id') == $appointment->id ? 'checked' : '' }}>
                        </td>
                        <td class="px-3 py-2 text-sm">
                            <span class="px-2 py-1 text-xs rounded bg-cyan-100 text-cyan-800">{{ $appointment->appointment_code }}</span>
                        </td>
                        <td class="px-3 py-2 text-sm text-blue-600 hover:underline">
                            {{ $appointment->patient->full_name }}
                        </td>
                        <td class="px-3 py-2 text-sm">{{ $appointment->doctor->user->full_name }}</td>
                        <td class="px-3 py-2 text-sm">
                            {{ $appointment->appointment_date->format('d/m/Y') }} {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                        </td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 text-xs rounded-full {{ $appointment->status_color_class }}">
                                {{ $appointment->status_text }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-sm">{{ Str::limit($appointment->chief_complaint, 50) }}</td>
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
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium mb-4">Treatment Details</h3>

        <form action="{{ route('backend.treatments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="appointment_id" id="selectedAppointmentId" value="{{ old('appointment_id') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Patient -->
<div>
    <label class="block text-sm font-medium text-gray-700">Patient *</label>
    <select name="patient_id" id="patientSelect" required
        class="mt-1 block w-full border rounded px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed pointer-events-none @error('patient_id') border-red-500 @enderror">
        <option value="">Select Patient</option>
        @foreach($patients as $patient)
        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
            {{ $patient->patient_code }} - {{ $patient->full_name }}
        </option>
        @endforeach
    </select>
    @error('patient_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<!-- Doctor -->
<div>
    <label class="block text-sm font-medium text-gray-700">Doctor *</label>
    <select name="doctor_id" id="doctorSelect" required
        class="mt-1 block w-full border rounded px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed pointer-events-none @error('doctor_id') border-red-500 @enderror">
        <option value="">Select Doctor</option>
        @foreach($doctors as $doctor)
        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
            {{ $doctor->user->full_name }} ({{ $doctor->specialization ?? 'General' }})
        </option>
        @endforeach
    </select>
    @error('doctor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                    @error('treatment_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Estimated Sessions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estimated Sessions *</label>
                    <input type="number" name="estimated_sessions" min="1" required
                        value="{{ old('estimated_sessions', 1) }}"
                        class="mt-1 block w-full border rounded px-3 py-2 @error('estimated_sessions') border-red-500 @enderror">
                    @error('estimated_sessions')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Treatment Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Treatment Date *</label>
                    <input type="date" name="treatment_date" required
                        value="{{ old('treatment_date', date('Y-m-d')) }}"
                        class="mt-1 block w-full border rounded px-3 py-2 @error('treatment_date') border-red-500 @enderror">
                    @error('treatment_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Diagnosis -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Diagnosis *</label>
                <textarea name="diagnosis" id="diagnosisTextarea" rows="3" required
                    class="mt-1 block w-full border rounded px-3 py-2 @error('diagnosis') border-red-500 @enderror">{{ old('diagnosis') }}</textarea>
                @error('diagnosis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

        <!-- Submit -->
            <x-back-submit-buttons back-url="{{ route('backend.treatments.index') }}"
                submit-text="Save Treatment" />
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

    function handleAppointmentSelection(appointmentRadio) {
        if (appointmentRadio.checked) {
            const patientId = appointmentRadio.dataset.patient;
            const doctorId = appointmentRadio.dataset.doctor;
            const diagnosis = appointmentRadio.dataset.diagnosis || '';

            patientSelect.value = patientId;
            doctorSelect.value = doctorId;
            diagnosisTextarea.value = diagnosis;
            selectedAppointmentId.value = appointmentRadio.value;

            appointmentRows.forEach(row => row.classList.remove('bg-blue-50'));
            appointmentRadio.closest('tr').classList.add('bg-blue-50');
        }
    }

    appointmentRadios.forEach(radio => {
        radio.addEventListener('change', () => handleAppointmentSelection(radio));
        if (radio.checked) handleAppointmentSelection(radio);
    });

    function filterAppointments() {
        const patientFilter = filterPatient.value;
        const doctorFilter = filterDoctor.value;
        const dateFilter = filterDate.value;

        appointmentRows.forEach(row => {
            const patientId = row.dataset.patient;
            const doctorId = row.dataset.doctor;
            const date = row.dataset.date;
            row.style.display = (!patientFilter || patientFilter === patientId) &&
                                (!doctorFilter || doctorFilter === doctorId) &&
                                (!dateFilter || dateFilter === date) ? '' : 'none';
        });
    }

    filterPatient.addEventListener('change', filterAppointments);
    filterDoctor.addEventListener('change', filterAppointments);
    filterDate.addEventListener('change', filterAppointments);
    filterAppointments();
});
</script>

<style>
.appointment-row:hover { background-color: #f9fafb; }
.appointment-row.bg-blue-50 { background-color: #eff6ff; }
</style>
@endsection
