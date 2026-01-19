{{-- backend.appointments.show --}}
@extends('backend.layout.structure')

@section('title', 'Appointment Details')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1">Appointment Details</h1>
                    <p class="text-muted mb-0">View appointment information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.appointments.edit', $appointment->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <a href="{{ route('backend.appointments.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        {{-- Status Alert --}}
        @php
            $statusColors = [
                'scheduled' => 'alert-primary',
                'checked_in' => 'alert-info',
                'in_progress' => 'alert-warning',
                'completed' => 'alert-success',
                'cancelled' => 'alert-danger',
                'no_show' => 'alert-secondary',
            ];
        @endphp
        <div class="alert {{ $statusColors[$appointment->status] }} d-flex justify-content-between align-items-center mb-4">
            <div>
                <strong>Status:</strong>
                <span
                    class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'primary') }}">
                    {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                </span>
                | <strong>Type:</strong> {{ ucfirst($appointment->appointment_type) }}
                @if ($appointment->appointment_type == 'fifo')
                    | <strong>Queue:</strong> #{{ $appointment->queue_no }}
                @endif
            </div>

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

        <div class="row g-4">
            {{-- Left Column: Appointment Details --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Appointment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Date & Time --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Date</label>
                                <div class="fw-semibold">
                                    <i class="bi bi-calendar me-2"></i>
                                    {{ $appointment->appointment_date->format('d M Y, l') }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small">Time</label>
                                <div class="fw-semibold">
                                    <i class="bi bi-clock me-2"></i>
                                    {{ $appointment->formatted_time }}
                                </div>
                            </div>

                            {{-- Patient Info --}}
                            <div class="col-12 mt-3">
                                <hr>
                                <h6 class="mb-3"><i class="bi bi-person me-2"></i>Patient Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Patient Name</label>
                                        <div class="fw-semibold">{{ $appointment->patient->full_name }}</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Patient Code</label>
                                        <div class="fw-semibold">{{ $appointment->patient->patient_code }}</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Phone</label>
                                        <div>{{ $appointment->patient->phone }}</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Age & Gender</label>
                                        <div>
                                            {{ $appointment->patient->age }} years |
                                            {{ ucfirst($appointment->patient->gender) }}
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label text-muted small">Address</label>
                                        <div>{{ $appointment->patient->address ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Doctor & Chair --}}
                            <div class="col-12 mt-3">
                                <hr>
                                <h6 class="mb-3"><i class="bi bi-hospital me-2"></i>Clinic Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Doctor</label>
                                        <div class="fw-semibold">
                                            <i class="bi bi-person-badge me-2"></i>
                                            Dr. {{ $appointment->doctor->full_name }}
                                        </div>
                                        <small
                                            class="text-muted">{{ $appointment->doctor->specialization ?? 'General Dentistry' }}</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Dental Chair</label>
                                        <div class="fw-semibold">
                                            <i class="bi bi-hospital me-2"></i>
                                            {{ $appointment->chair->name }}
                                        </div>
                                        <span
                                            class="badge {{ $appointment->chair->is_available ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($appointment->chair->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Notes --}}
                            @if ($appointment->notes)
                                <div class="col-12 mt-3">
                                    <hr>
                                    <h6 class="mb-3"><i class="bi bi-chat-left-text me-2"></i>Notes</h6>
                                    <div class="bg-light p-3 rounded">
                                        {{ $appointment->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Meta Information & Actions --}}
            <div class="col-lg-4">
                {{-- Meta Information --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Meta Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                <span class="text-muted">Created By</span>
                                <span class="fw-semibold">{{ $appointment->creator->display_name ?? '—' }}</span>
                            </div>

                            <div
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                <span class="text-muted">Created On</span>
                                <span>{{ $appointment->created_at->format('d M Y, h:i A') }}</span>
                            </div>

                            <div
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                <span class="text-muted">Last Updated By</span>
                                <span class="fw-semibold">{{ $appointment->updater->display_name ?? '—' }}</span>
                            </div>

                            <div
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                <span class="text-muted">Last Updated</span>
                                <span>{{ $appointment->updated_at->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('backend.patients.show', $appointment->patient_id) }}"
                                class="btn btn-outline-primary">
                                <i class="bi bi-person me-2"></i> View Patient
                            </a>

                            <a href="{{ route('backend.doctors.show', $appointment->doctor_id) }}"
                                class="btn btn-outline-info">
                                <i class="bi bi-person-badge me-2"></i> View Doctor
                            </a>

                            @if ($appointment->status == 'completed')
                                <a href="#" class="btn btn-outline-success">
                                    <i class="bi bi-file-medical me-2"></i> Create Treatment
                                </a>

                                <a href="#" class="btn btn-outline-warning">
                                    <i class="bi bi-receipt me-2"></i> Generate Invoice
                                </a>
                            @endif

                            @if (in_array($appointment->status, ['scheduled', 'checked_in']))
                                <form action="{{ route('backend.appointments.update-status', $appointment->id) }}"
                                    method="POST" class="d-grid">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-x-circle me-2"></i> Cancel Appointment
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('backend.appointments.destroy', $appointment->id) }}" method="POST"
                                class="d-grid" onsubmit="return confirm('Delete this appointment permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-trash me-2"></i> Delete Appointment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
