{{-- backend.doctors.edit --}}
@extends('backend.layout.structure')

@section('title', 'Edit Doctor')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="mb-3">
            <h1 class="mb-1">Edit Doctor</h1>
            <p class="text-muted mb-0">Update doctor profile information</p>
        </div>

        <form action="{{ route('backend.doctors.update', $doctor->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">

                        {{-- User Info (Read-only) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Doctor User</label>
                            <input type="text" class="form-control"
                                value="{{ $doctor->full_name }} ({{ $doctor->phone }})" readonly>
                        </div>

                        {{-- Specialization --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Specialization</label>
                            <input type="text" name="specialization"
                                class="form-control @error('specialization') is-invalid @enderror"
                                value="{{ old('specialization', $doctor->specialization) }}"
                                placeholder="e.g. Orthodontist">
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Designation --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Designation</label>
                            <input type="text" name="designation"
                                class="form-control @error('designation') is-invalid @enderror"
                                value="{{ old('designation', $doctor->designation) }}" placeholder="e.g. Consultant">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Experience Years --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Experience (Years)</label>
                            <input type="number" name="experience_years"
                                class="form-control @error('experience_years') is-invalid @enderror"
                                value="{{ old('experience_years', $doctor->experience_years) }}" placeholder="0">
                            @error('experience_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Consultation Fee --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Consultation Fee <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="consultation_fee"
                                class="form-control @error('consultation_fee') is-invalid @enderror"
                                value="{{ old('consultation_fee', $doctor->consultation_fee) }}" required>
                            @error('consultation_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Commission Percent --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Commission (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="commission_percent"
                                class="form-control @error('commission_percent') is-invalid @enderror"
                                value="{{ old('commission_percent', $doctor->commission_percent) }}" required>
                            @error('commission_percent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Profile Photo --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Profile Photo</label>
                            @if ($doctor->photo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $doctor->photo) }}" alt="Doctor Photo"
                                        class="img-thumbnail" width="120">
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
                            <small class="text-muted">Leave blank to keep existing photo</small>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Bio --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Bio</label>
                            <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror"
                                placeholder="Short introduction about the doctor">{{ old('bio', $doctor->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-4 pb-4 px-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                        <i class="bi bi-save me-1"></i>
                        Update Doctor
                    </button>

                    <a href="{{ route('backend.doctors.index') }}" class="btn btn-outline-secondary shadow-sm px-4">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
