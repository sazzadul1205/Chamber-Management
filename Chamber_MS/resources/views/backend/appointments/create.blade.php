@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Schedule New Appointment</h2>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('backend.appointments.store') }}" method="POST" id="appointment-form" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Patient -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                    <select name="patient_id" required
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('patient_id') border-red-500 @enderror">
                        <option value="">Select Patient</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->patient_code }} - {{ $patient->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Doctor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor *</label>
                    <select name="doctor_id" id="doctor-select" required
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('doctor_id') border-red-500 @enderror">
                        <option value="">Select Doctor</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->user->full_name }} ({{ $doctor->specialization ?? 'General' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Appointment Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Date *</label>
                    <input type="date" name="appointment_date" id="appointment-date"
                        value="{{ old('appointment_date', date('Y-m-d')) }}" required
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('appointment_date') border-red-500 @enderror">
                </div>

                <!-- Expected Arrival Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Expected Arrival Time *
                    </label>
                    <p class="text-xs text-gray-500 mb-1">
                        Time is approximate. Patients are served in arrival order.
                    </p>
                    <input type="time" name="appointment_time" value="{{ old('appointment_time') }}" required
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('appointment_time') border-red-500 @enderror">
                </div>


                <!-- Dental Chair -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dental Chair</label>
                    <select name="chair_id"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('chair_id') border-red-500 @enderror">
                        <option value="">Select Chair (Optional)</option>
                        @foreach ($chairs as $chair)
                            <option value="{{ $chair->id }}" {{ old('chair_id') == $chair->id ? 'selected' : '' }}>
                                {{ $chair->chair_code }} - {{ $chair->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Appointment Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Type *</label>
                    <select name="appointment_type" required
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('appointment_type') border-red-500 @enderror">
                        @foreach (App\Models\Appointment::appointmentTypes() as $key => $value)
                            <option value="{{ $key }}" {{ old('appointment_type') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                    <input type="number" name="expected_duration" min="5" max="240" step="5" required
                        value="{{ old('expected_duration', 30) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('expected_duration') border-red-500 @enderror">
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                    <select name="priority" required
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('priority') border-red-500 @enderror">
                        @foreach (App\Models\Appointment::priorities() as $key => $value)
                            <option value="{{ $key }}" {{ old('priority') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Chief Complaint -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chief Complaint / Reason</label>
                <textarea name="chief_complaint" rows="2"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('chief_complaint') }}</textarea>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit -->
            <x-back-submit-buttons back-url="{{ route('backend.appointments.index') }}"
                submit-text="Schedule Appointment" />

        </form>
    </div>
@endsection
