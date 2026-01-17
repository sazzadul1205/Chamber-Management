{{-- backend.patients.edit --}}
@extends('backend.layout.structure')

@section('title', 'Edit Patient')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="mb-3">
            <h1 class="mb-1">Edit Patient</h1>
            <p class="text-muted mb-0">Update patient record</p>
        </div>

        <form method="POST" action="{{ route('backend.patients.update', $patient->id) }}">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Full Name --}}
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name"
                                class="form-control @error('full_name') is-invalid @enderror"
                                value="{{ old('full_name', $patient->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $patient->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $patient->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gender --}}
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>
                                    Male</option>
                                <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>
                                    Female</option>
                                <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>
                                    Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Date of Birth --}}
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth"
                                class="form-control @error('date_of_birth') is-invalid @enderror"
                                value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Referral Type --}}
                        <div class="col-md-6">
                            <label class="form-label">Referral Type</label>
                            <select name="referral_type" id="referral_type"
                                class="form-select @error('referral_type') is-invalid @enderror">
                                <option value="">Select Type</option>
                                @foreach ($referralTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ old('referral_type', $patient->referral_type) === $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('referral_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Referred By Patient --}}
                        <div class="col-md-6 d-none" id="referred_by_patient_wrapper">
                            <label class="form-label">Referred By Patient</label>

                            {{-- Hidden input Laravel reads --}}
                            <input type="hidden" name="referred_by_patient_id" id="referred_by_patient_id"
                                value="{{ old('referred_by_patient_id', $patient->referred_by_patient_id) }}">

                            {{-- React mount --}}
                            <div id="referred_by_patient_react" data-patients='@json($patients)'
                                data-old="{{ old('referred_by_patient_id', $patient->referred_by_patient_id) }}">
                            </div>
                        </div>

                        {{-- Referred By Text --}}
                        <div class="col-md-6 d-none" id="referred_by_text_div">
                            <label class="form-label">Referred By (Text)</label>
                            <input type="text" name="referred_by_text"
                                class="form-control @error('referred_by_text') is-invalid @enderror"
                                value="{{ old('referred_by_text', $patient->referred_by_text) }}">
                            @error('referred_by_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Address --}}
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $patient->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm px-3">
                            <i class="bi bi-save me-1"></i> Update Patient
                        </button>
                        <a href="{{ route('backend.patients.index') }}" class="btn btn-outline-secondary shadow-sm px-3">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const referralType = document.getElementById('referral_type');
            const patientWrapper = document.getElementById('referred_by_patient_wrapper');
            const textDiv = document.getElementById('referred_by_text_div');
            const patientInput = document.getElementById('referred_by_patient_id');
            const textInput = document.querySelector('[name="referred_by_text"]');

            function toggleReferralFields() {
                const type = referralType.value;

                if (type === 'patient') {
                    patientWrapper.classList.remove('d-none');
                    textDiv.classList.add('d-none');
                    if (textInput) textInput.value = '';
                } else if (type) {
                    patientWrapper.classList.add('d-none');
                    textDiv.classList.remove('d-none');
                    if (patientInput) patientInput.value = '';
                } else {
                    patientWrapper.classList.add('d-none');
                    textDiv.classList.add('d-none');
                    if (patientInput) patientInput.value = '';
                    if (textInput) textInput.value = '';
                }
            }

            referralType.addEventListener('change', toggleReferralFields);
            toggleReferralFields();
        });
    </script>

    @vite('resources/js/reactApp.jsx')
@endsection
