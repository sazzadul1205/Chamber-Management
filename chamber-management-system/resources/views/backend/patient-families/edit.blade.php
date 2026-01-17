{{-- backend.patient-families.edit --}}
@extends('backend.layout.structure')

@section('title', 'Edit Patient Family')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h1 class="h3 mb-0">Edit Family: {{ $patientFamily->family_name }}</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('backend.patient-families.index') }}"
                    class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left-circle"></i> Back
                </a>
            </div>
        </div>

        {{-- Family Info --}}
        <div class="card mb-4">
            <div class="card-body d-flex flex-wrap gap-3 align-items-center">
                <div><strong>Family Name:</strong> {{ $patientFamily->family_name }}</div>
                <div><strong>Head Patient:</strong> {{ $patientFamily->headPatient->full_name ?? '—' }}</div>
            </div>
        </div>

        {{-- Edit Family Name --}}
        <div class="card mb-4">
            <div class="card-header"><strong>Edit Family Name</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('backend.patient-families.update', $patientFamily) }}">
                    @csrf
                    @method('PUT')
                    <div class="input-group">
                        <input type="text" name="family_name"
                            class="form-control @error('family_name') is-invalid @enderror"
                            value="{{ old('family_name', $patientFamily->family_name) }}" required>
                        <button type="submit" class="btn btn-primary">Update</button>
                        @error('family_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
        </div>

        {{-- Family Members --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Family Members ({{ $patientFamily->members->count() }})</strong>
            </div>
            <div class="card-body">

                {{-- Add Member --}}
                <form id="add-member-form" class="mb-3" method="POST"
                    action="{{ route('backend.patient-family-members.store', $patientFamily) }}">
                    @csrf
                    <div class="input-group">
                        <select name="patient_id" class="form-select" required>
                            <option value="">Select Patient to Add</option>
                            @foreach ($availablePatients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->full_name }}
                                    ({{ $patient->patient_code }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success">Add Member</button>
                    </div>
                </form>

                {{-- Members List --}}
                <div class="row g-3">
                    @forelse($patientFamily->members as $member)
                        <div class="col-md-4">
                            <div class="card border shadow-sm">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $member->patient->full_name }}</strong><br>
                                        <small class="text-muted">{{ $member->patient->patient_code }}</small>
                                    </div>
                                    <form method="POST"
                                        action="{{ route('backend.patient-family-members.destroy', $member) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Remove this member from family?')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">No members yet.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        // Optional: submit add member via AJAX for instant update (requires JS handling)
    </script>
@endsection
