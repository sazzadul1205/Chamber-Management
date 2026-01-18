@extends('backend.layout.structure')

@section('title', 'Edit Patient Family')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="mb-3">
            <h1 class="mb-1">Edit Family</h1>
            <p class="text-muted mb-0">{{ $patientFamily->family_name }}</p>
        </div>

        {{-- Info --}}
        <div class="card mb-4">
            <div class="card-body d-flex gap-4 flex-wrap">
                <div><strong>Family Name:</strong> {{ $patientFamily->family_name }}</div>
                <div><strong>Head:</strong> {{ $patientFamily->headPatient->full_name ?? '—' }}</div>
                <div><strong>Members:</strong> {{ $patientFamily->members->count() }}</div>
            </div>
        </div>

        {{-- Edit Form --}}
        <form method="POST" action="{{ route('backend.patient-families.update', $patientFamily) }}">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Family Name --}}
                        <div class="col-md-6">
                            <label class="form-label">Family Name</label>
                            <input type="text" name="family_name" class="form-control"
                                value="{{ old('family_name', $patientFamily->family_name) }}">
                        </div>

                        {{-- Family Head --}}
                        <div class="col-md-6">
                            <label class="form-label">Family Head</label>
                            <div id="family_head_react" data-patients='@json($availablePatients)'
                                data-old="{{ old('head_patient_id', $patientFamily->head_patient_id) }}">
                            </div>
                        </div>

                        {{-- Members --}}
                        <div class="col-md-12">
                            <label class="form-label">Family Members</label>
                            <div id="family_members_react" data-patients='@json($availablePatients)'
                                data-old='@json(old('member_patient_ids', $patientFamily->members->pluck('patient_id')))'>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Update Family
                        </button>
                        <a href="{{ route('backend.patient-families.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>

                </div>
            </div>
        </form>

    </div>
@endsection

@section('scripts')
    @vite('resources/js/reactApp.jsx')
@endsection
