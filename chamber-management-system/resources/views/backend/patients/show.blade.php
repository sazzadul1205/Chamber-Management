@extends('backend.layout.structure')

@section('title', 'Patient Details')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="h4 mb-1">Patient Details</h1>
                <p class="text-muted mb-0">View complete patient information</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('backend.patients.edit', $patient->id) }}"
                    class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="{{ route('backend.patients.index') }}"
                    class="btn btn-sm btn-secondary d-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-arrow-left-circle"></i> Back
                </a>
            </div>
        </div>
        <hr class="mb-4">


        <div class="row g-4">

            {{-- Basic Information --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge-fill fs-5"></i>
                        <strong>Basic Information</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-hash me-1"></i> Patient Code</span>
                            <span class="fw-bold">{{ $patient->patient_code }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-person me-1"></i> Full Name</span>
                            <span class="fw-bold">{{ $patient->full_name }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-telephone me-1"></i> Phone</span>
                            <span class="fw-bold">{{ $patient->phone }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-envelope me-1"></i> Email</span>
                            <span class="fw-bold">{{ $patient->email ?? '—' }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-gender-ambiguous me-1"></i> Gender</span>
                            <span class="badge bg-info">{{ ucfirst($patient->gender) }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-calendar me-1"></i> Date of Birth</span>
                            <span class="fw-bold">{{ $patient->date_of_birth->format('d M Y') }}</span>
                        </div>
                        <div class="mb-0 d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-geo-alt me-1"></i> Address</span>
                            <span class="fw-bold">{{ $patient->address ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Referral Information --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-success text-white d-flex align-items-center gap-2">
                        <i class="bi bi-share-fill fs-5"></i>
                        <strong>Referral Information</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-diagram-3 me-1"></i> Referral Type</span>
                            <span class="fw-bold">{{ ucfirst($patient->referral_type ?? 'N/A') }}</span>
                        </div>
                        <div class="mb-0 d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-person-lines-fill me-1"></i> Referred By</span>
                            @if ($patient->referral_type === 'patient' && $patient->referredByPatient)
                                <div class="text-end">
                                    <span class="fw-bold">{{ $patient->referredByPatient->full_name }}</span><br>
                                    <small class="text-muted">{{ $patient->referredByPatient->patient_code }}</small>
                                </div>
                            @else
                                <span class="fw-bold">{{ $patient->referred_by_text ?? '—' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- System Information --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-white d-flex align-items-center gap-2">
                        <i class="bi bi-gear-fill fs-5"></i>
                        <strong>System Information</strong>
                    </div>
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <div class="col d-flex justify-content-between">
                                <span class="text-secondary"><i class="bi bi-person-check me-1"></i> Created By</span>
                                <span class="fw-bold">{{ $patient->creator?->name ?? 'System' }}</span>
                            </div>
                            <div class="col d-flex justify-content-between">
                                <span class="text-secondary"><i class="bi bi-person-gear me-1"></i> Updated By</span>
                                <span class="fw-bold">{{ $patient->updater?->name ?? '—' }}</span>
                            </div>
                            <div class="col d-flex justify-content-between">
                                <span class="text-secondary"><i class="bi bi-calendar-plus me-1"></i> Created At</span>
                                <span class="fw-bold">{{ $patient->created_at->format('d M Y h:i A') }}</span>
                            </div>
                            <div class="col d-flex justify-content-between">
                                <span class="text-secondary"><i class="bi bi-calendar-check me-1"></i> Updated At</span>
                                <span class="fw-bold">{{ $patient->updated_at->format('d M Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
