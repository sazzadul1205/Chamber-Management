{{-- backend.doctors.create --}}
@extends('backend.layout.structure')

@section('title', 'Add Doctor')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="mb-3">
            <h1 class="mb-1">Add Doctor</h1>
            <p class="text-muted mb-0">Create a new doctor profile</p>
        </div>

        <form action="{{ route('backend.doctors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <div class="row g-3">

                        {{-- Select User --}}
                        <div class="col-md-6">
                            <label class="form-label">Doctor User <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">— Select Doctor User —</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }} ({{ $user->phone }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Specialization --}}
                        <div class="col-md-6">
                            <label class="form-label">Specialization</label>
                            <input type="text" name="specialization"
                                class="form-control @error('specialization') is-invalid @enderror"
                                value="{{ old('specialization') }}" placeholder="e.g. Orthodontist">
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Designation --}}
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation"
                                class="form-control @error('designation') is-invalid @enderror"
                                value="{{ old('designation') }}" placeholder="e.g. Consultant">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Experience --}}
                        <div class="col-md-6">
                            <label class="form-label">Experience (Years)</label>
                            <input type="number" name="experience_years"
                                class="form-control @error('experience_years') is-invalid @enderror"
                                value="{{ old('experience_years') }}" placeholder="0">
                            @error('experience_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Consultation Fee --}}
                        <div class="col-md-6">
                            <label class="form-label">Consultation Fee <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="consultation_fee"
                                class="form-control @error('consultation_fee') is-invalid @enderror"
                                value="{{ old('consultation_fee') }}" placeholder="0.00" required>
                            @error('consultation_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Commission Percent --}}
                        <div class="col-md-6">
                            <label class="form-label">Commission (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="commission_percent"
                                class="form-control @error('commission_percent') is-invalid @enderror"
                                value="{{ old('commission_percent') }}" placeholder="0 - 100" required>
                            @error('commission_percent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Profile Photo --}}
                        <div class="col-md-6">
                            <label class="form-label">Profile Photo</label>
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional. JPG, PNG, max 2MB.</small>
                        </div>

                        {{-- Bio --}}
                        <div class="col-12">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror"
                                placeholder="Short introduction about the doctor">{{ old('bio') }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                </div>

                {{-- Actions --}}
                <div class="mt-4 pb-4 px-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                        <i class="bi bi-save me-1"></i> Create Doctor
                    </button>

                    <a href="{{ route('backend.doctors.index') }}" class="btn btn-outline-secondary shadow-sm px-4">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
