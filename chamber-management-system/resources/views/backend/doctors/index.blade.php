{{-- backend.doctors.index --}}
@extends('backend.layout.structure')

@section('title', 'Doctors')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Doctors</h1>
                <p class="text-muted mb-0">List of all registered doctors</p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                {{-- Search --}}
                <form action="{{ route('backend.doctors.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search by name, phone, email, specialization" value="{{ $search }}"
                        style="min-width: 260px; height: 38px;">
                    <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" style="height:38px;">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>

                {{-- Refresh --}}
                <a href="{{ route('backend.doctors.index') }}" class="btn btn-info btn-sm d-flex align-items-center gap-1"
                    style="height:38px;">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>

                {{-- Add Doctor --}}
                <a href="{{ route('backend.doctors.create') }}"
                    class="btn btn-success btn-sm d-flex align-items-center gap-1" style="height:38px;">
                    <i class="bi bi-plus-circle"></i> Add Doctor
                </a>
            </div>
        </div>

        {{-- Doctors Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Doctor</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Specialization</th>
                                <th>Consultation Fee</th>
                                <th>Commission</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($doctors as $doctor)
                                <tr>
                                    <td>{{ $loop->iteration + ($doctors->currentPage() - 1) * $doctors->perPage() }}</td>

                                    {{-- Doctor Name + Photo Placeholder --}}
                                    <td class="d-flex align-items-center gap-2">
                                        @if (isset($doctor->photo))
                                            <img src="{{ asset('storage/' . $doctor->photo) }}" alt="Photo"
                                                class="rounded-circle" width="40" height="40">
                                        @else
                                            <i class="bi bi-person-circle fs-3 text-secondary"></i>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $doctor->full_name }}</div>
                                            <small class="text-muted">ID: {{ $doctor->user_id }}</small>
                                        </div>
                                    </td>

                                    <td>{{ $doctor->phone }}</td>
                                    <td>{{ $doctor->email ?? '—' }}</td>
                                    <td>{{ $doctor->specialization ?? '—' }}</td>

                                    {{-- Important Fields --}}
                                    <td class="fw-semibold text-primary">৳
                                        {{ number_format($doctor->consultation_fee, 2) }}</td>
                                    <td class="fw-semibold text-success">{{ $doctor->commission_percent }}%</td>

                                    {{-- Status --}}
                                    <td>
                                        <span
                                            class="badge {{ $doctor->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($doctor->status) }}
                                        </span>
                                    </td>

                                    <td>{{ $doctor->created_at->format('d M Y') }}</td>

                                    {{-- Actions --}}
                                    <td class="text-end">
                                        <a href="{{ route('backend.doctors.show', $doctor->id) }}"
                                            class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                            title="View doctor">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('backend.doctors.edit', $doctor->id) }}"
                                            class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                            title="Edit doctor">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('backend.doctors.destroy', $doctor->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this doctor?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip"
                                                title="Delete doctor">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        No doctors found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($doctors->lastPage() > 1)
                    <div class="d-flex justify-content-between align-items-center px-5 py-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing page <strong>{{ $doctors->currentPage() }}</strong> of
                            <strong>{{ $doctors->lastPage() }}</strong>
                            (Total doctors: {{ $doctors->total() }})
                        </div>

                        <div class="btn-group btn-group-sm">
                            <a href="{{ $doctors->previousPageUrl() }}"
                                class="btn btn-outline-primary {{ $doctors->onFirstPage() ? 'disabled' : '' }}">
                                &lsaquo; Prev
                            </a>
                            <a href="{{ $doctors->nextPageUrl() }}"
                                class="btn btn-outline-primary {{ $doctors->currentPage() == $doctors->lastPage() ? 'disabled' : '' }}">
                                Next &rsaquo;
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tooltips --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
