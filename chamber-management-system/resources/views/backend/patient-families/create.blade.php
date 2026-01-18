@extends('backend.layout.structure')

@section('title', 'Create Patient Family')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="mb-3">
            <h1 class="mb-1">Create Patient Family</h1>
            <p class="text-muted mb-0">Fill in the details to register a new family</p>
        </div>

        <form method="POST" action="{{ route('backend.patient-families.store') }}">
            @csrf

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Family Name --}}
                        <div class="col-md-6">
                            <label class="form-label">Family Name <span class="text-danger">*</span></label>
                            <input type="text" name="family_name"
                                class="form-control @error('family_name') is-invalid @enderror"
                                value="{{ old('family_name') }}" required>
                            @error('family_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Family Head --}}
                        <div class="col-md-6">
                            <label class="form-label">Family Head <span class="text-danger">*</span></label>
                            <div id="family_head_react" data-patients='@json($availablePatients)'
                                data-old="{{ old('head_patient_id') }}">
                            </div>
                            @error('head_patient_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Family Members --}}
                        <div class="col-md-12">
                            <label class="form-label">Family Members</label>
                            <div id="family_members_react" data-patients='@json($availablePatients)'
                                data-old='@json(old('member_patient_ids', []))'>
                            </div>
                            @error('member_patient_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Family
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
