{{-- backend.treatments.index --}}
@extends('backend.layout.structure')

@section('title', 'Treatments')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Treatments</h1>
                <p class="text-muted mb-0">Manage patient treatments</p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                {{-- Status Filter --}}
                <form action="{{ route('backend.treatments.index') }}" method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                        style="width: 150px; height: 38px;">
                        <option value="">All Status</option>
                        <option value="ongoing" {{ $status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                    {{-- Search Box --}}
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search patient or diagnosis" value="{{ $search }}"
                        style="min-width: 200px; height: 38px;">

                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                        style="height: 38px;">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>

                {{-- Add Treatment --}}
                <a href="{{ route('backend.treatments.create') }}"
                    class="btn btn-success btn-sm d-flex align-items-center gap-1" style="height:38px;">
                    <i class="bi bi-plus-circle"></i> Add Treatment
                </a>
            </div>
        </div>

        {{-- Treatment Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Diagnosis Summary</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($treatments as $treatment)
                                <tr>
                                    <td>{{ $loop->iteration + ($treatments->currentPage() - 1) * $treatments->perPage() }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $treatment->patient->full_name }}</div>
                                        <small class="text-muted">{{ $treatment->patient->patient_code }}</small>
                                    </td>

                                    <td>
                                        <div>Dr. {{ $treatment->doctor->full_name }}</div>
                                        <small
                                            class="text-muted">{{ $treatment->doctor->specialization ?? 'General' }}</small>
                                    </td>

                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;"
                                            title="{{ $treatment->diagnosis }}">
                                            {{ Str::limit($treatment->diagnosis, 80) }}
                                        </div>
                                        @if ($treatment->appointment)
                                            <small class="text-muted">
                                                Appointment:
                                                {{ $treatment->appointment->appointment_date->format('d M Y') }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $statusColors = [
                                                'ongoing' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$treatment->status] ?? 'secondary' }}">
                                            {{ ucfirst($treatment->status) }}
                                        </span>

                                        @if ($treatment->procedures_count > 0)
                                            <div class="small text-muted mt-1">
                                                {{ $treatment->completed_procedures_count }}/{{ $treatment->procedures_count }}
                                                procedures
                                            </div>
                                        @endif
                                    </td>

                                    <td>{{ $treatment->created_at->format('d M Y') }}</td>

                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('backend.treatments.show', $treatment->id) }}"
                                                class="btn btn-outline-info" data-bs-toggle="tooltip" title="View details">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="{{ route('backend.treatments.edit', $treatment->id) }}"
                                                class="btn btn-outline-primary" data-bs-toggle="tooltip"
                                                title="Edit treatment">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            @if ($treatment->status == 'ongoing')
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-success dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        title="Update status">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <form
                                                                action="{{ route('backend.treatments.update-status', $treatment->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status" value="completed">
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="bi bi-check-lg me-2"></i>Mark as Completed
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('backend.treatments.update-status', $treatment->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-x-circle me-2"></i>Cancel Treatment
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif

                                            <form action="{{ route('backend.treatments.destroy', $treatment->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this treatment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger" data-bs-toggle="tooltip"
                                                    title="Delete treatment">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No treatments found
                                        @if ($search || $status)
                                            <br>
                                            <a href="{{ route('backend.treatments.index') }}"
                                                class="btn btn-sm btn-link mt-2">
                                                Clear filters
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($treatments->lastPage() > 1)
                    <div class="d-flex justify-content-between align-items-center px-5 py-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing page <strong>{{ $treatments->currentPage() }}</strong> of
                            <strong>{{ $treatments->lastPage() }}</strong>
                            (Total treatments: {{ $treatments->total() }})
                        </div>

                        <div class="btn-group btn-group-sm" role="group" aria-label="Pagination">
                            <a href="{{ $treatments->previousPageUrl() }}{{ request()->except('page') ? '&' . http_build_query(request()->except('page')) : '' }}"
                                class="btn btn-outline-primary {{ $treatments->onFirstPage() ? 'disabled' : '' }}">
                                &lsaquo; Prev
                            </a>

                            <a href="{{ $treatments->nextPageUrl() }}{{ request()->except('page') ? '&' . http_build_query(request()->except('page')) : '' }}"
                                class="btn btn-outline-primary {{ $treatments->currentPage() == $treatments->lastPage() ? 'disabled' : '' }}">
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
