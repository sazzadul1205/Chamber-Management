{{-- backend.appointments.edit --}}
@extends('backend.layout.structure')

@section('title', 'Edit Appointment')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1">Edit Appointment</h1>
                    <p class="text-muted mb-0">Update appointment details</p>
                </div>
                <div class="d-flex gap-2">
                    @if ($appointment->status == 'scheduled')
                        <form action="{{ route('backend.appointments.update-status', $appointment->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="checked_in">
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="bi bi-person-check me-1"></i> Check In
                            </button>
                        </form>
                    @endif

                    @if ($appointment->status == 'checked_in')
                        <form action="{{ route('backend.appointments.update-status', $appointment->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="bi bi-play-circle me-1"></i> Start Treatment
                            </button>
                        </form>
                    @endif

                    @if ($appointment->status == 'in_progress')
                        <form action="{{ route('backend.appointments.update-status', $appointment->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check-circle me-1"></i> Complete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('backend.appointments.update', $appointment->id) }}">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Current Status --}}
                        <div class="col-12 mb-3">
                            <div
                                class="alert alert-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'info') }} py-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <div>
                                        <strong>Current Status:</strong>
                                        <span
                                            class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'primary') }}">
                                            {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                                        </span>
                                        @if ($appointment->appointment_type == 'fifo')
                                            | <strong>Queue Number:</strong> {{ $appointment->queue_no }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Patient --}}
                        <div class="col-md-6">
                            <label class="form-label">Patient <span class="text-danger">*</span></label>
                            <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror"
                                required>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}"
                                        {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->full_name }} ({{ $patient->patient_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Doctor --}}
                        <div class="col-md-6">
                            <label class="form-label">Doctor <span class="text-danger">*</span></label>
                            <select name="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}>
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
                                <option value="slot"
                                    {{ old('appointment_type', $appointment->appointment_type) == 'slot' ? 'selected' : '' }}>
                                    Time Slot</option>
                                <option value="fifo"
                                    {{ old('appointment_type', $appointment->appointment_type) == 'fifo' ? 'selected' : '' }}>
                                    FIFO (Walk-in)</option>
                            </select>
                            @error('appointment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Dental Chair --}}
                        <div class="col-md-6">
                            <label class="form-label">Dental Chair <span class="text-danger">*</span></label>
                            <select name="chair_id" class="form-select @error('chair_id') is-invalid @enderror" required>
                                @foreach ($chairs as $chair)
                                    <option value="{{ $chair->id }}"
                                        {{ old('chair_id', $appointment->chair_id) == $chair->id ? 'selected' : '' }}>
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
                            <input type="date" name="appointment_date"
                                class="form-control @error('appointment_date') is-invalid @enderror"
                                value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
                                required>
                            @error('appointment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Appointment Time --}}
                        <div class="col-md-6" id="time_slot_container">
                            <label class="form-label">Appointment Time <span class="text-danger">*</span></label>
                            <select name="appointment_time" id="appointment_time"
                                class="form-select @error('appointment_time') is-invalid @enderror">
                                <option value="">Select Time</option>
                                @foreach ($timeSlots as $slot)
                                    <option value="{{ $slot }}"
                                        {{ old('appointment_time', $appointment->appointment_time?->format('H:i')) == $slot ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($slot)->format('h:i A') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('appointment_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="scheduled"
                                    {{ old('status', $appointment->status) == 'scheduled' ? 'selected' : '' }}>Scheduled
                                </option>
                                <option value="checked_in"
                                    {{ old('status', $appointment->status) == 'checked_in' ? 'selected' : '' }}>Checked In
                                </option>
                                <option value="in_progress"
                                    {{ old('status', $appointment->status) == 'in_progress' ? 'selected' : '' }}>In
                                    Progress</option>
                                <option value="completed"
                                    {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="cancelled"
                                    {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                                <option value="no_show"
                                    {{ old('status', $appointment->status) == 'no_show' ? 'selected' : '' }}>No Show
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $appointment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm px-3">
                            <i class="bi bi-save me-1"></i> Update Appointment
                        </button>
                        <a href="{{ route('backend.appointments.show', $appointment->id) }}"
                            class="btn btn-outline-info shadow-sm px-3">
                            <i class="bi bi-eye me-1"></i> View Details
                        </a>
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
            const appointmentTime = document.getElementById('appointment_time');

            function toggleTimeSlot() {
                if (appointmentType.value === 'slot') {
                    timeSlotContainer.style.display = 'block';
                    appointmentTime.setAttribute('required', 'required');
                } else {
                    timeSlotContainer.style.display = 'none';
                    appointmentTime.removeAttribute('required');
                }
            }

            appointmentType.addEventListener('change', toggleTimeSlot);
            toggleTimeSlot(); // Initialize on load
        });
    </script>
@endsection
