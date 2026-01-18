{{-- resources/views/patient-families/show.blade.php --}}
@extends('backend.layout.structure')

@section('title', 'Patient Family Details')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="h4 mb-1"><i class="bi bi-people-fill me-1"></i> Family: {{ $patientFamily->family_name }}</h1>
                <p class="text-muted mb-0">View complete family information</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('backend.patient-families.edit', $patientFamily->id) }}"
                    class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="{{ route('backend.patient-families.index') }}"
                    class="btn btn-sm btn-secondary d-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-arrow-left-circle"></i> Back
                </a>
            </div>
        </div>
        <hr class="mb-4">

        <div class="row g-4">

            {{-- Family Head --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge-fill fs-5"></i>
                        <strong>Family Head</strong>
                    </div>
                    <div class="card-body">
                        @if ($patientFamily->headPatient)
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $patientFamily->headPatient->full_name }}</strong><br>
                                    <small class="text-muted">{{ $patientFamily->headPatient->patient_code }}</small>
                                </div>
                                <a href="{{ route('backend.patients.show', $patientFamily->headPatient->id) }}"
                                    class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                            <div class="mb-1"><i class="bi bi-telephone me-1"></i>
                                {{ $patientFamily->headPatient->phone ?? '—' }}</div>
                            <div class="mb-1"><i class="bi bi-envelope me-1"></i>
                                {{ $patientFamily->headPatient->email ?? '—' }}</div>
                            <div class="mb-0"><i class="bi bi-gender-ambiguous me-1"></i>
                                {{ ucfirst($patientFamily->headPatient->gender ?? '—') }}</div>
                        @else
                            <p class="text-muted mb-0">No head assigned</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Family Members --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-success text-white d-flex align-items-center gap-2">
                        <i class="bi bi-people-fill fs-5"></i>
                        <strong>Family Members ({{ $patientFamily->members->count() }})</strong>
                    </div>
                    <div class="card-body">
                        @if ($patientFamily->members->isEmpty())
                            <p class="text-muted mb-0">No additional members.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach ($patientFamily->members as $member)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $member->patient->full_name ?? '—' }}</strong><br>
                                            <small class="text-muted">{{ $member->patient->patient_code ?? '—' }}</small>
                                            <span
                                                class="badge bg-info ms-2">{{ ucfirst($member->patient->gender ?? '—') }}</span>
                                        </div>
                                        <a href="{{ route('backend.patients.show', $member->patient->id) }}"
                                            class="btn btn-sm btn-outline-primary">View</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
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
                                <span class="fw-bold">{{ $patientFamily->headPatient->creator?->name ?? 'System' }}</span>
                            </div>
                            <div class="col d-flex justify-content-between">
                                <span class="text-secondary"><i class="bi bi-person-gear me-1"></i> Updated By</span>
                                <span class="fw-bold">{{ $patientFamily->headPatient->updater?->name ?? '—' }}</span>
                            </div>
                            <div class="col d-flex justify-content-between">
                                <span class="text-secondary"><i class="bi bi-calendar-plus me-1"></i> Created At</span>
                                <span class="fw-bold">{{ $patientFamily->created_at->format('d M Y h:i A') }}</span>
                            </div>
                            <div class="col d-flex justify-content-between">
                                <span class="text-secondary"><i class="bi bi-calendar-check me-1"></i> Updated At</span>
                                <span class="fw-bold">{{ $patientFamily->updated_at->format('d M Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
