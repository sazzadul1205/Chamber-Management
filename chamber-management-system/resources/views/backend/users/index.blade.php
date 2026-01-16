{{-- backend.users.index --}}
@extends('backend.layout.structure')

@section('title', 'Users')

@section('styles')
    <style>
        /* Card styling */
        .card-custom {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        /* Table styling */
        .table-custom thead {
            background: #f3f4f6;
            /* soft gray */
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table-custom tbody tr {
            transition: all 0.2s;
            padding-top: 3px;
            padding-bottom: 3px;
        }

        .table-custom tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Badge improvements */
        .badge-role {
            background-color: #4f46e5;
            color: #fff;
            font-weight: 500;
        }

        .badge-active {
            background-color: #10b981;
        }

        .badge-inactive {
            background-color: #6b7280;
        }

        /* Buttons */
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .form-select-sm,
        .form-control-sm {
            min-width: 150px;
        }

        /* Search & filters container */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }

        .td-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }


        /* Responsive: stack inputs on mobile */
        @media(max-width: 768px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
@endsection

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div>
                <h1 class="mb-1">All Users</h1>
                <p class="text-muted mb-0">List of all registered users in the system</p>
            </div>

            {{-- Search & Filters --}}
            <form method="GET" action="{{ route('backend.users.index') }}" class="d-flex align-items-center gap-2 mb-3">

                <!-- Search input grows -->
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search by Name, Email, Phone" value="{{ request('search') }}"
                    style="flex: 1; min-width: 200px; height: 34px;"> <!-- set height explicitly -->

                <!-- Role dropdown -->
                <select name="role_id" class="form-select form-select-sm" style="width: 150px; height: 34px;">
                    <option value="">All Roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Status dropdown -->
                <select name="status" class="form-select form-select-sm" style="width: 120px; height: 34px;">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <!-- Filter button -->
                <button type="submit" class="btn btn-sm btn-primary" style="height: 34px;">
                    Filter
                </button>

            </form>


        </div>

        {{-- Users Table --}}
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle mb-0">
                        <thead>
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
                                <tr class="px-2">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $user->display_name }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->email ?? '—' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-role">{{ $user->role?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>{{ $user->updated_at->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('backend.users.edit', $user->id) }}"
                                            class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Edit this user">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                        <form action="{{ route('backend.users.destroy', $user->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Delete this user">
                                                <i class="bi bi-trash me-1"></i> Delete
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
                    <div class="d-flex justify-content-between align-items-center px-5 py-3 flex-wrap gap-2">

                        {{-- Page Info --}}
                        <div class="text-muted small">
                            Showing page <strong>{{ $users->currentPage() }}</strong> of
                            <strong>{{ $users->lastPage() }}</strong>
                            (Total users: {{ $users->total() }})
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
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
