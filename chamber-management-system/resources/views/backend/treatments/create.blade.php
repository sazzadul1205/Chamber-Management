{{-- backend.treatments.create --}}
@extends('backend.layout.structure')

@section('title', 'Add Treatment')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="mb-3">
            <h1 class="mb-1">Add Treatment</h1>
            <p class="text-muted mb-0">Create a new treatment record</p>
        </div>

        <form method="POST" action="{{ route('backend.treatments.store') }}">
            @csrf

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Patient Selection --}}
                        <div class="col-md-6">
                            <label class="form-label">Patient <span class="text-danger">*</span></label>
                            <select name="patient_id" id="patient_id"
                                class="form-select @error('patient_id') is-invalid @enderror" required>
                                <option value="">Select Patient</option>
                                @foreach ($patients as $patientOption)
                                    <option value="{{ $patientOption->id }}"
                                        {{ old('patient_id') == $patientOption->id ? 'selected' : '' }}>
                                        {{ $patientOption->full_name }} ({{ $patientOption->patient_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Doctor Selection --}}
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

                        {{-- Appointment Selection (Optional) --}}
                        <div class="col-md-6">
                            <label class="form-label">Appointment (Optional)</label>
                            <select name="appointment_id" id="appointment_id"
                                class="form-select @error('appointment_id') is-invalid @enderror">
                                <option value="">No Appointment Link</option>
                                @foreach ($appointments as $apt)
                                    <option value="{{ $apt->id }}"
                                        {{ old('appointment_id') == $apt->id ? 'selected' : '' }}>
                                        {{ $apt->patient->full_name }} -
                                        {{ $apt->appointment_date->format('d M Y') }} -
                                        Dr. {{ $apt->doctor->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('appointment_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Link to an existing appointment for reference</small>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">Select Status</option>
                                <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Diagnosis --}}
                        <div class="col-12">
                            <label class="form-label">Diagnosis / Notes <span class="text-danger">*</span></label>
                            <textarea name="diagnosis" class="form-control @error('diagnosis') is-invalid @enderror" rows="6" required
                                placeholder="Enter diagnosis, observations, and treatment notes...">{{ old('diagnosis') }}</textarea>
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Describe the patient's condition, findings, and proposed treatment
                                plan</small>
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm px-3">
                            <i class="bi bi-file-medical me-1"></i> Create Treatment
                        </button>
                        <a href="{{ route('backend.treatments.index') }}" class="btn btn-outline-secondary shadow-sm px-3">
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
            // Filter appointments based on selected patient
            const patientSelect = document.getElementById('patient_id');
            const appointmentSelect = document.getElementById('appointment_id');

            patientSelect.addEventListener('change', function() {
                if (this.value) {
                    // In a real app, you would fetch appointments for this patient via AJAX
                    // For now, we'll just show all appointments
                    console.log('Patient selected:', this.value);
                }
            });

            @if (isset($appointment) && $appointment)
                // If coming from appointment, pre-fill the form
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('patient_id').value = '{{ $appointment->patient_id }}';
                    document.getElementById('doctor_id').value = '{{ $appointment->doctor_id }}';
                    document.getElementById('appointment_id').value = '{{ $appointment->id }}';
                });
            @endif
        });
    </script>
@endsection
