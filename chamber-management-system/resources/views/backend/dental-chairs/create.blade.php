{{-- backend.dental-chairs.create --}}
@extends('backend.layout.structure')

@section('title', 'Add Dental Chair')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="mb-3">
            <h1 class="mb-1">Add Dental Chair</h1>
            <p class="text-muted mb-0">Create a new dental chair for the clinic</p>
        </div>

        <form action="{{ route('backend.dental-chairs.store') }}" method="POST">
            @csrf

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Chair Name --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Chair Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                placeholder="e.g. Chair A" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Status <span
                                    class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror"
                                required>
                                <option value="">— Select Status —</option>
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available
                                </option>
                                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied
                                </option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                                    Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="card-footer d-flex justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm px-3">
                        <i class="bi bi-save me-1"></i> Save Patient
                    </button>
                    <a href="{{ route('backend.dental-chairs.index') }}" class="btn btn-outline-secondary shadow-sm px-3">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>
@endsection
