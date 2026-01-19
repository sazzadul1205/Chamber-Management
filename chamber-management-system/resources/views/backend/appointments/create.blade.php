{{-- backend.appointments.create --}}
@extends('backend.layout.structure')

@section('title', 'Add Appointment')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="mb-3">
            <h1 class="mb-1">Add Appointment</h1>
            <p class="text-muted mb-0">Schedule a new appointment</p>
        </div>

        <form method="POST" action="{{ route('backend.appointments.store') }}" id="appointmentForm">
            @csrf

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Patient React Select --}}
                        <div class="col-md-6">
                            <label class="form-label">Patient <span class="text-danger">*</span></label>
                            <div id="patient_select_react" data-patients='@json($patients)'
                                data-old="{{ old('patient_id', $defaultPatientId) }}">
                            </div>
                            @error('patient_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- Doctor --}}
                        <div class="col-md-6">
                            <label class="form-label">Doctor <span class="text-danger">*</span></label>
                            <select name="doctor_id" id="doctor_id"
                                class="form-select @error('doctor_id') is-invalid @enderror" required>
                                <option value="">Select Doctor</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->user->full_name }} - {{ $doctor->specialization ?? 'General' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Appointment Type --}}
                        <div class="col-md-6">
                            <label class="form-label">Appointment Type <span class="text-danger">*</span></label>
                            <select name="appointment_type" id="appointment_type"
                                class="form-select @error('appointment_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="slot" {{ old('appointment_type') == 'slot' ? 'selected' : '' }}>Time Slot
                                </option>
                                <option value="fifo" {{ old('appointment_type') == 'fifo' ? 'selected' : '' }}>FIFO
                                    (Walk-in)</option>
                            </select>
                            @error('appointment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Dental Chair --}}
                        <div class="col-md-6">
                            <label class="form-label">Dental Chair <span class="text-danger">*</span></label>
                            <select name="chair_id" id="chair_id"
                                class="form-select @error('chair_id') is-invalid @enderror" required>
                                <option value="">Select Chair</option>
                                @foreach ($chairs as $chair)
                                    <option value="{{ $chair->id }}"
                                        {{ old('chair_id') == $chair->id ? 'selected' : '' }}>
                                        {{ $chair->name }} - {{ ucfirst($chair->status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('chair_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Appointment Date --}}
                        <div class="col-md-6">
                            <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
                            <input type="date" name="appointment_date" id="appointment_date"
                                class="form-control @error('appointment_date') is-invalid @enderror"
                                value="{{ old('appointment_date', $defaultDate) }}" required>
                            @error('appointment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Appointment Time (for slot type) --}}
                        <div class="col-md-6" id="time_slot_container">
                            <label class="form-label">Appointment Time <span class="text-danger">*</span></label>
                            <select name="appointment_time" id="appointment_time"
                                class="form-select @error('appointment_time') is-invalid @enderror">
                                <option value="">Select Time</option>
                                @foreach ($timeSlots as $slot)
                                    <option value="{{ $slot }}"
                                        {{ old('appointment_time') == $slot ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($slot)->format('h:i A') }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text" id="available_slots_info"></div>
                            @error('appointment_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm px-3">
                            <i class="bi bi-calendar-plus me-1"></i> Schedule Appointment
                        </button>
                        <a href="{{ route('backend.appointments.index') }}"
                            class="btn btn-outline-secondary shadow-sm px-3">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const appointmentType = document.getElementById('appointment_type');
            const timeSlotContainer = document.getElementById('time_slot_container');
            const appointmentDate = document.getElementById('appointment_date');
            const chairId = document.getElementById('chair_id');
            const doctorId = document.getElementById('doctor_id');
            const appointmentTime = document.getElementById('appointment_time');
            const slotsInfo = document.getElementById('available_slots_info');

            // Toggle time slot visibility based on appointment type
            function toggleTimeSlot() {
                if (appointmentType.value === 'slot') {
                    timeSlotContainer.style.display = 'block';
                    appointmentTime.setAttribute('required', 'required');
                } else {
                    timeSlotContainer.style.display = 'none';
                    appointmentTime.removeAttribute('required');
                }
            }

            // Fetch available time slots
            function fetchAvailableSlots() {
                if (!appointmentDate.value || !chairId.value || !doctorId.value || appointmentType.value !==
                    'slot') {
                    return;
                }

                slotsInfo.innerHTML =
                    '<span class="text-warning"><i class="bi bi-clock-history"></i> Checking availability...</span>';

                fetch(
                        `{{ route('backend.appointments.available-slots') }}?date=${appointmentDate.value}&chair_id=${chairId.value}&doctor_id=${doctorId.value}`
                    )
                    .then(response => response.json())
                    .then(data => {
                        // Clear existing options
                        appointmentTime.innerHTML = '<option value="">Select Time</option>';

                        // Add available slots
                        data.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.time;
                            option.textContent = slot.formatted;
                            if (!slot.available) {
                                option.disabled = true;
                                option.textContent += ' (Booked)';
                            }
                            appointmentTime.appendChild(option);
                        });

                        // Update info
                        const availableCount = data.filter(s => s.available).length;
                        slotsInfo.innerHTML = `<span class="text-${availableCount > 0 ? 'success' : 'danger'}">
                        <i class="bi bi-${availableCount > 0 ? 'check-circle' : 'x-circle'}"></i>
                        ${availableCount} time slots available
                    </span>`;
                    })
                    .catch(error => {
                        slotsInfo.innerHTML =
                            '<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Error loading slots</span>';
                    });
            }

            // Event listeners
            appointmentType.addEventListener('change', toggleTimeSlot);
            appointmentDate.addEventListener('change', fetchAvailableSlots);
            chairId.addEventListener('change', fetchAvailableSlots);
            doctorId.addEventListener('change', fetchAvailableSlots);

            // Form validation
            document.getElementById('appointmentForm').addEventListener('submit', function(e) {
                if (appointmentType.value === 'slot' && !appointmentTime.value) {
                    e.preventDefault();
                    alert('Please select an appointment time for slot type appointments.');
                    appointmentTime.focus();
                }
            });

            // Initialize
            toggleTimeSlot();
            if (appointmentType.value === 'slot') {
                fetchAvailableSlots();
            }
        });

        @vite('resources/js/reactApp.jsx')
    </script>
@endsection
