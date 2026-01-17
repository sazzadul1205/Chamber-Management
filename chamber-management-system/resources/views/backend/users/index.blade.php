{{-- backend.users.index --}}
@extends('backend.layout.structure')

@section('title', 'Users')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">All Users</h1>
                <p class="text-muted mb-0">List of all registered users in the system</p>
            </div>

            <div class="d-flex gap-2 flex-wrap">

                {{-- Search Box --}}
                <form action="{{ route('backend.users.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search by Name, Email, or Phone" value="{{ request('search') }}"
                        style="min-width:220px; height:38px;">

                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                        style="height:38px;">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>

                {{-- Refresh --}}
                <a href="{{ route('backend.users.index') }}" class="btn btn-info btn-sm d-flex align-items-center gap-1"
                    style="height:38px;">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>

                {{-- Add User --}}
                <a href="{{ route('backend.users.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1"
                    style="height:38px;">
                    <i class="bi bi-plus-circle"></i> Add User
                </a>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Status</th>
                                <th>Created</th>
                                <th>Last Updated</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                <tr >
                                    <td >{{ $loop->iteration }}</td>

                                    <td>
                                        <div class="fw-semibold">{{ $user->display_name }}</div>
                                    </td>

                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->email ?? '—' }}</td>

                                    <td class="text-center">
                                        <span class="badge bg-primary text-white">
                                            {{ $user->role?->name ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <span
                                            class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }} text-white">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>


                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>{{ $user->updated_at->format('d M Y') }}</td>

                                    <td class="text-end">
                                        {{-- Status Toggle --}}
                                        <div class="form-check form-switch d-inline-block me-2 align-middle">
                                            <input class="form-check-input user-status-toggle" type="checkbox"
                                                role="switch" data-user-id="{{ $user->id }}"
                                                {{ $user->status === 'active' ? 'checked' : '' }}
                                                {{ $user->hasRole('Admin') ? 'disabled' : '' }} data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="{{ $user->hasRole('Admin') ? 'Admin status cannot be changed' : 'Toggle user status (Active / Inactive)' }}">
                                        </div>

                                        {{-- Edit --}}
                                        <a href="{{ route('backend.users.edit', $user->id) }}"
                                            class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip"
                                            title="Edit this user">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('backend.users.destroy', $user->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return {{ $user->hasRole('Admin') ? 'false' : 'confirm(\'Are you sure you want to delete this user?\')' }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"
                                                {{ $user->hasRole('Admin') ? 'disabled' : '' }} data-bs-toggle="tooltip"
                                                title="{{ $user->hasRole('Admin') ? 'Admin users cannot be deleted' : 'Delete this user' }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">No users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($users->lastPage() > 1)
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 flex-wrap gap-2">

                        {{-- Page Info --}}
                        <div class="text-muted small">
                            Showing page <strong>{{ $users->currentPage() }}</strong> of
                            <strong>{{ $users->lastPage() }}</strong> (Total users: {{ $users->total() }})
                        </div>

                        {{-- Navigation Buttons --}}
                        <div class="btn-group btn-group-sm" role="group" aria-label="Pagination">
                            {{-- Previous --}}
                            <a href="{{ $users->previousPageUrl() }}"
                                class="btn btn-outline-primary {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                &lsaquo; Prev
                            </a>

                            {{-- Next --}}
                            <a href="{{ $users->nextPageUrl() }}"
                                class="btn btn-outline-primary {{ $users->currentPage() == $users->lastPage() ? 'disabled' : '' }}">
                                Next &rsaquo;
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bootstrap tooltips --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });

            // Status toggle AJAX
            document.querySelectorAll('.user-status-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const userId = this.dataset.userId;
                    const status = this.checked ? 'active' : 'inactive';

                    fetch(`/backend/users/${userId}/toggle-status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                status
                            })
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Failed');
                        })
                        .catch(() => {
                            alert('Could not update user status');
                            this.checked = !this.checked; // rollback
                        });
                });
            });
        });
    </script>
@endsection
