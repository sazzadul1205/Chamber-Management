@extends('backend.layout.structure')

@section('title', 'Patients')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Patients</h1>
                <p class="text-muted mb-0">List of all registered patients</p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                {{-- Search Box --}}
                <form action="{{ route('backend.patients.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search by Name, Code, or Phone" value="{{ request('search') }}"
                        style="min-width: 220px; height: 38px;">
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                        style="height: 38px;">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>

                {{-- Refresh Button --}}
                <a href="{{ route('backend.patients.index') }}" class="btn btn-info btn-sm d-flex align-items-center gap-1"
                    style="height: 38px;">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>

                {{-- Add Patient --}}
                <a href="{{ route('backend.patients.create') }}"
                    class="btn btn-success btn-sm d-flex align-items-center gap-1" style="height:38px;">
                    <i class="bi bi-plus-circle"></i> Add Patient
                </a>
            </div>
        </div>

        {{-- Patient Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Patient Code</th>
                                <th>Patient</th>
                                <th>Phone</th>
                                <th>Gender</th>
                                <th>Age</th>
                                <th>Referred By</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($patients as $patient)
                                <tr>
                                    <td>{{ $loop->iteration + ($patients->currentPage() - 1) * $patients->perPage() }}</td>

                                    <td class="fw-semibold">{{ $patient->patient_code }}</td>

                                    <td>
                                        <div class="fw-semibold">{{ $patient->full_name }}</div>
                                        <small class="text-muted">{{ $patient->email ?? '—' }}</small>
                                    </td>

                                    <td>{{ $patient->phone }}</td>

                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($patient->gender) }}</span>
                                    </td>

                                    <td>
                                        {{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age . ' yrs' : '—' }}
                                    </td>

                                    <td>
                                        @if ($patient->referral_type === 'patient')
                                            {{ $patient->referredByPatient?->full_name ?? '—' }}
                                        @elseif($patient->referral_type)
                                            {{ $patient->referred_by_text }}
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td>{{ $patient->created_at->format('d M Y') }}</td>

                                    <td class="text-end">
                                        <a href="{{ route('backend.patients.show', $patient->id) }}"
                                            class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                            title="View details">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('backend.patients.edit', $patient->id) }}"
                                            class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                            title="Edit patient">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('backend.patients.destroy', $patient->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this patient?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip"
                                                title="Delete patient">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        No patients found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($patients->lastPage() > 1)
                    <div class="d-flex justify-content-between align-items-center px-5 py-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing page <strong>{{ $patients->currentPage() }}</strong> of
                            <strong>{{ $patients->lastPage() }}</strong>
                            (Total patients: {{ $patients->total() }})
                        </div>

                        <div class="btn-group btn-group-sm" role="group" aria-label="Pagination">
                            <a href="{{ $patients->previousPageUrl() }}{{ request()->getQueryString() ? '&' . request()->getQueryString() : '' }}"
                                class="btn btn-outline-primary {{ $patients->onFirstPage() ? 'disabled' : '' }}">
                                &lsaquo; Prev
                            </a>

                            <a href="{{ $patients->nextPageUrl() }}{{ request()->getQueryString() ? '&' . request()->getQueryString() : '' }}"
                                class="btn btn-outline-primary {{ $patients->currentPage() == $patients->lastPage() ? 'disabled' : '' }}">
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
