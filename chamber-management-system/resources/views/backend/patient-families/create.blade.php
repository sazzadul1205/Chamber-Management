{{-- backend.patient-families.create --}}
@extends('backend.layout.structure')

@section('title', 'Create Patient Family')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h1 class="h3 mb-0">Create Patient Family</h1>
            <a href="{{ route('backend.patient-families.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left-circle"></i> Back
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form method="POST" action="{{ route('backend.patient-families.store') }}">
                    @csrf

                    {{-- Family Name --}}
                    <div class="mb-3">
                        <label class="form-label">Family Name <span class="text-danger">*</span></label>
                        <input type="text" name="family_name"
                            class="form-control @error('family_name') is-invalid @enderror" value="{{ old('family_name') }}"
                            required>
                        @error('family_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Family Head --}}
                    <div class="mb-3">
                        <label class="form-label">Family Head <span class="text-danger">*</span></label>
                        <select name="head_patient_id" class="form-select @error('head_patient_id') is-invalid @enderror"
                            required>
                            <option value="">Select Head Patient</option>
                            @foreach ($availablePatients as $patient)
                                <option value="{{ $patient->id }}"
                                    {{ old('head_patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->full_name }} ({{ $patient->patient_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('head_patient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Form Actions --}}
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                            <i class="bi bi-save"></i> Save Family
                        </button>
                        <a href="{{ route('backend.patient-families.index') }}"
                            class="btn btn-outline-secondary d-flex align-items-center gap-1">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
