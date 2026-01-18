{{-- backend.dental-chairs.edit --}}
@extends('backend.layout.structure')

@section('title', 'Edit Dental Chair')

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="mb-3">
            <h1 class="mb-1">Edit Dental Chair</h1>
            <p class="text-muted mb-0">Update the details of this dental chair</p>
        </div>

        <form action="{{ route('backend.dental-chairs.update', $dentalChair->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Chair Name --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Chair Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $dentalChair->name) }}" placeholder="e.g. Chair A" required>
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
                                <option value="available"
                                    {{ old('status', $dentalChair->status) == 'available' ? 'selected' : '' }}>Available
                                </option>
                                <option value="occupied"
                                    {{ old('status', $dentalChair->status) == 'occupied' ? 'selected' : '' }}>Occupied
                                </option>
                                <option value="maintenance"
                                    {{ old('status', $dentalChair->status) == 'maintenance' ? 'selected' : '' }}>Maintenance
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="card-footer d-flex justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Update Chair
                    </button>

                    <a href="{{ route('backend.dental-chairs.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Back
                    </a>

                </div>
            </div>
        </form>
    </div>
@endsection
