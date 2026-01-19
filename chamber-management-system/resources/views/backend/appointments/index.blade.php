{{-- backend.appointments.index --}}
@extends('backend.layout.structure')

@section('title', 'Appointments')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">

            {{-- Left side: Title and subtitle --}}
            <div class="flex-shrink-0">
                <h1 class="mb-1">Appointments</h1>
                <p class="text-muted mb-0">Manage patient appointments</p>
            </div>

            {{-- Right side: Filters + Buttons --}}
            <div
                class="d-flex flex-column flex-lg-row flex-wrap gap-3 align-items-start align-items-lg-center flex-shrink-0">

                {{-- Filter Form --}}
                <form action="{{ route('backend.appointments.index') }}" method="GET"
                    class="d-flex flex-wrap gap-2 align-items-center flex-grow-1 flex-lg-grow-0">

                    {{-- Date Filter --}}
                    <div class="flex-fill flex-lg-grow-0" style="min-width: 150px;">
                        <input type="date" name="date" class="form-control form-control-sm w-100"
                            value="{{ $date }}" style="height: 38px;">
                    </div>

                    {{-- Status Filter --}}
                    <div class="flex-fill flex-lg-grow-0" style="min-width: 150px;">
                        <select name="status" class="form-select form-select-sm w-100" style="height: 38px;">
                            <option value="">All Status</option>
                            <option value="scheduled" {{ $status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="checked_in" {{ $status == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                            <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress
                            </option>
                            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="no_show" {{ $status == 'no_show' ? 'selected' : '' }}>No Show</option>
                        </select>
                    </div>

                    {{-- Search Filter --}}
                    <div class="flex-fill" style="min-width: 400px;">
                        <input type="text" name="search" class="form-control form-control-sm w-100"
                            placeholder="Search patient or doctor" value="{{ $search }}" style="height: 38px;">
                    </div>

                    {{-- Filter Button --}}
                    <div class="flex-shrink-0">
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                            style="height: 38px;">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </div>
                </form>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                    <a href="{{ route('backend.appointments.calendar') }}"
                        class="btn btn-info btn-sm d-flex align-items-center gap-1" style="height: 38px;">
                        <i class="bi bi-calendar-week"></i> Calendar
                    </a>

                    <a href="{{ route('backend.appointments.dashboard') }}"
                        class="btn btn-warning btn-sm d-flex align-items-center gap-1" style="height: 38px;">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>

                    <a href="{{ route('backend.appointments.create') }}"
                        class="btn btn-success btn-sm d-flex align-items-center gap-1" style="height: 38px;">
                        <i class="bi bi-plus-circle"></i> Add Appointment
                    </a>
                </div>

            </div>
        </div>



        {{-- Appointment Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Date & Time</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Chair</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($appointments as $appointment)
                                <tr>
                                    <td>{{ $loop->iteration + ($appointments->currentPage() - 1) * $appointments->perPage() }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $appointment->appointment_date->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">{{ $appointment->formatted_time }}</small>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $appointment->patient->full_name }}</div>
                                        <small class="text-muted">{{ $appointment->patient->phone }}</small>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $appointment->doctor->full_name }}</div>
                                        <small
                                            class="text-muted">{{ $appointment->doctor->specialization ?? 'General' }}</small>
                                    </td>

                                    <td>
                                        <span
                                            class="badge {{ $appointment->chair->is_available ? 'bg-success' : 'bg-warning' }}">
                                            {{ $appointment->chair->name }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst($appointment->appointment_type) }}
                                        </span>
                                        @if ($appointment->appointment_type == 'fifo')
                                            <small class="text-muted d-block">Queue #{{ $appointment->queue_no }}</small>
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $statusColors = [
                                                'scheduled' => 'bg-primary',
                                                'checked_in' => 'bg-info',
                                                'in_progress' => 'bg-warning',
                                                'completed' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                'no_show' => 'bg-secondary',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusColors[$appointment->status] ?? 'bg-secondary' }}">
                                            {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                                        </span>
                                    </td>

                                    <td>{{ $appointment->created_at->format('d M Y') }}</td>

                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('backend.appointments.show', $appointment->id) }}"
                                                class="btn btn-outline-info" data-bs-toggle="tooltip" title="View details">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="{{ route('backend.appointments.edit', $appointment->id) }}"
                                                class="btn btn-outline-primary" data-bs-toggle="tooltip"
                                                title="Edit appointment">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            @if ($appointment->status == 'scheduled')
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-success dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        title="Update status">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <form
                                                                action="{{ route('backend.appointments.update-status', $appointment->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status" value="checked_in">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="bi bi-person-check me-2"></i>Check In
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('backend.appointments.update-status', $appointment->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif

                                            @if ($appointment->status == 'checked_in')
                                                <form
                                                    action="{{ route('backend.appointments.update-status', $appointment->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button type="submit" class="btn btn-outline-warning"
                                                        data-bs-toggle="tooltip" title="Start treatment">
                                                        <i class="bi bi-play-circle"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($appointment->status == 'in_progress')
                                                <form
                                                    action="{{ route('backend.appointments.update-status', $appointment->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="btn btn-outline-success"
                                                        data-bs-toggle="tooltip" title="Complete">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('backend.appointments.destroy', $appointment->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this appointment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger" data-bs-toggle="tooltip"
                                                    title="Delete appointment">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        No appointments found
                                        @if ($date != today()->format('Y-m-d'))
                                            <br>
                                            <a href="{{ route('backend.appointments.index', ['date' => today()->format('Y-m-d')]) }}"
                                                class="btn btn-sm btn-link mt-2">
                                                View today's appointments
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($appointments->lastPage() > 1)
                    <div class="d-flex justify-content-between align-items-center px-5 py-3 flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing page <strong>{{ $appointments->currentPage() }}</strong> of
                            <strong>{{ $appointments->lastPage() }}</strong>
                            (Total appointments: {{ $appointments->total() }})
                        </div>

                        <div class="btn-group btn-group-sm" role="group" aria-label="Pagination">
                            <a href="{{ $appointments->previousPageUrl() }}{{ request()->except('page') ? '&' . http_build_query(request()->except('page')) : '' }}"
                                class="btn btn-outline-primary {{ $appointments->onFirstPage() ? 'disabled' : '' }}">
                                &lsaquo; Prev
                            </a>

                            <a href="{{ $appointments->nextPageUrl() }}{{ request()->except('page') ? '&' . http_build_query(request()->except('page')) : '' }}"
                                class="btn btn-outline-primary {{ $appointments->currentPage() == $appointments->lastPage() ? 'disabled' : '' }}">
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
