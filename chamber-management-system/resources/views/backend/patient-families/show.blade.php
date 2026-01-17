{{-- resources/views/patient-families/show.blade.php --}}
@extends('backend.layout.structure')

@section('title', 'Patient Family Details')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h1 class="h3 mb-0"><i class="bi bi-people-fill me-1"></i> Family: {{ $patientFamily->family_name }}</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('backend.patient-families.edit', $patientFamily->id) }}"
                    class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="{{ route('backend.patient-families.index') }}"
                    class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left-circle"></i> Back
                </a>
            </div>
        </div>

        <div class="row g-4">

            {{-- Family Head --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge"></i>
                        <strong>Family Head</strong>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $patientFamily->headPatient->full_name ?? '—' }}</h5>
                        <p class="mb-1"><i class="bi bi-upc-scan me-1"></i> Code:
                            {{ $patientFamily->headPatient->patient_code ?? '—' }}</p>
                        <p class="mb-1"><i class="bi bi-telephone me-1"></i> Phone:
                            {{ $patientFamily->headPatient->phone ?? '—' }}</p>
                        <p class="mb-1"><i class="bi bi-envelope me-1"></i> Email:
                            {{ $patientFamily->headPatient->email ?? '—' }}</p>
                        <p class="mb-0"><i class="bi bi-gender-ambiguous me-1"></i> Gender:
                            {{ ucfirst($patientFamily->headPatient->gender ?? '—') }}</p>
                    </div>
                </div>
            </div>

            {{-- Family Members --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="bi bi-people"></i>
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
                                            <i class="bi bi-person-circle me-1"></i>
                                            {{ $member->patient->full_name ?? '—' }}
                                            <small class="text-muted">({{ $member->patient->patient_code ?? '—' }})</small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge bg-secondary">{{ ucfirst($member->patient->gender ?? '—') }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            {{-- System Information --}}
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i>
                        <strong>System Information</strong>
                    </div>
                    <div class="card-body d-flex flex-wrap gap-4">
                        <div>
                            <i class="bi bi-person-check me-1"></i> Created By:
                            {{ $patientFamily->headPatient->creator?->name ?? 'System' }}
                        </div>
                        <div>
                            <i class="bi bi-person-gear me-1"></i> Updated By:
                            {{ $patientFamily->headPatient->updater?->name ?? '—' }}
                        </div>
                        <div>
                            <i class="bi bi-calendar-plus me-1"></i> Created At:
                            {{ $patientFamily->created_at->format('d M Y h:i A') }}
                        </div>
                        <div>
                            <i class="bi bi-calendar-check me-1"></i> Updated At:
                            {{ $patientFamily->updated_at->format('d M Y h:i A') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
