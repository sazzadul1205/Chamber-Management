@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Walk-in Appointment</h2>
            <a href="{{ route('backend.appointments.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm font-medium transition">
                Back to Appointments
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded shadow p-6">
            <form method="POST" action="{{ route('backend.backend.appointments.walk-in.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Patient -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Patient <span class="text-red-500">*</span></label>
                        <select name="patient_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Doctor -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Doctor <span class="text-red-500">*</span></label>
                        <select name="doctor_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->user->full_name ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Chair -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Chair (Optional)</label>
                        <select name="chair_id" class="w-full border rounded px-3 py-2">
                            <option value="">Select Chair</option>
                            @foreach($chairs as $chair)
                                <option value="{{ $chair->id }}">{{ $chair->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="appointment_type" class="w-full border rounded px-3 py-2" required>
                            <option value="consultation">Consultation</option>
                            <option value="treatment">Treatment</option>
                            <option value="followup">Follow-up</option>
                            <option value="emergency">Emergency</option>
                            <option value="checkup">Check-up</option>
                        </select>
                    </div>

                    <!-- Expected Duration -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Expected Duration (minutes) <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="expected_duration" value="30" class="w-full border rounded px-3 py-2"
                            required>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Priority <span class="text-red-500">*</span></label>
                        <select name="priority" class="w-full border rounded px-3 py-2" required>
                            <option value="normal">Normal</option>
                            <option value="urgent">Urgent</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <!-- Chief Complaint -->
                <div>
                    <label class="block text-sm font-medium mb-1">Chief Complaint</label>
                    <textarea name="chief_complaint" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                        Register Walk-in
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection