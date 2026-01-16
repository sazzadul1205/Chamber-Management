{{-- backend.users.edit --}}
@extends('backend.layout.structure')

@section('title', 'Edit User')

@section('styles')
    <style>
        .form-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 2rem;
        }
    </style>
@endsection

@section('content')
    <div class="px-4 py-4">

        {{-- Page Header --}}
        <div class="mb-4">
            <h1 class="mb-1">Edit User</h1>
            <p class="text-muted mb-0">Update user information below</p>
        </div>

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Edit Form --}}
        <div class="card form-card">
            <form action="{{ route('backend.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- Full Name --}}
                    <div class="col-md-6">
                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="full_name" name="full_name" class="form-control form-control-md"
                            value="{{ old('full_name', $user->full_name) }}" required>
                    </div>

                    {{-- Phone --}}
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" id="phone" name="phone" class="form-control form-control-md"
                            value="{{ old('phone', $user->phone) }}" required>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control form-control-md"
                            value="{{ old('email', $user->email) }}">
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                        <select id="role_id" name="role_id" class="form-select" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}
                                    {{ $role->name === 'Admin' ? 'disabled' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select form-select-md" required>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>
                                Inactive</option>
                        </select>
                    </div>

                </div>

                {{-- Submit --}}
                <div class="mt-4 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center px-3 py-2"
                        style="border-radius: 8px; transition: all 0.2s;">
                        <i class="bi bi-save me-2"></i>
                        Update User
                    </button>

                    <a href="{{ route('backend.users.index') }}"
                        class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center px-3 py-2"
                        style="border-radius: 8px; transition: all 0.2s;">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancel
                    </a>
                </div>



            </form>
        </div>

    </div>
@endsection
