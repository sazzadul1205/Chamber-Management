{{-- backend.doctors.show --}}
@extends('backend.layout.structure')

@section('title', 'Show Doctor Details')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Doctor Details</h1>
                <p class="text-muted mb-0">Complete profile of the doctor</p>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('backend.doctors.index') }}"
                    class="btn btn-outline-secondary shadow-sm px-4 d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>

                <a href="{{ route('backend.doctors.edit', $doctor->id) }}"
                    class="btn btn-primary shadow-sm px-4 d-flex align-items-center gap-1">
                    <i class="bi bi-pencil"></i> Edit Doctor
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row g-4">

                    {{-- Profile Photo --}}
                    <div class="col-md-4 text-center">
                        @if ($doctor->photo)
                            <img src="{{ asset('storage/' . $doctor->photo) }}" alt="Doctor Photo"
                                class="img-fluid rounded mb-3">
                        @else
                            <img src="https://via.placeholder.com/200x200?text=No+Photo" alt="No Photo"
                                class="img-fluid rounded mb-3">
                        @endif
                        <h5 class="fw-semibold">{{ $doctor->full_name }}</h5>
                        <small class="text-muted">ID: {{ $doctor->user_id }}</small>
                    </div>

                    {{-- Doctor Info --}}
                    <div class="col-md-8">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <div><i class="bi bi-telephone me-1"></i>{{ $doctor->phone }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <div><i class="bi bi-envelope me-1"></i>{{ $doctor->email ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Specialization</label>
                                <div><i class="bi bi-clipboard-data me-1"></i>{{ $doctor->specialization ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Designation</label>
                                <div><i class="bi bi-award me-1"></i>{{ $doctor->designation ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Experience (Years)</label>
                                <div><i class="bi bi-hourglass-split me-1"></i>{{ $doctor->experience_years ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Consultation Fee</label>
                                <div><i class="bi bi-cash-stack me-1"></i>৳
                                    {{ number_format($doctor->consultation_fee, 2) }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Commission (%)</label>
                                <div><i class="bi bi-percent me-1"></i>{{ $doctor->commission_percent }}%</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <div>
                                    <span class="badge {{ $doctor->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($doctor->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Bio</label>
                                <div>{{ $doctor->bio ?? '—' }}</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
