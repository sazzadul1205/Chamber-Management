{{-- backend.appointments.calendar --}}
@extends('backend.layout.structure')

@section('title', 'Appointment Calendar')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Appointment Calendar</h1>
                <p class="text-muted mb-0">Weekly view of appointments</p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                {{-- Week Navigation --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('backend.appointments.calendar', ['date' => $startOfWeek->copy()->subWeek()->format('Y-m-d'), 'doctor_id' => $doctorId]) }}"
                        class="btn btn-outline-primary">
                        <i class="bi bi-chevron-left"></i> Prev Week
                    </a>
                    <a href="{{ route('backend.appointments.calendar', ['date' => today()->format('Y-m-d'), 'doctor_id' => $doctorId]) }}"
                        class="btn btn-outline-primary">
                        Today
                    </a>
                    <a href="{{ route('backend.appointments.calendar', ['date' => $startOfWeek->copy()->addWeek()->format('Y-m-d'), 'doctor_id' => $doctorId]) }}"
                        class="btn btn-outline-primary">
                        Next Week <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                {{-- Doctor Filter --}}
                <form action="{{ route('backend.appointments.calendar') }}" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="date" value="{{ $startOfWeek->format('Y-m-d') }}">
                    <select name="doctor_id" class="form-select form-select-sm" onchange="this.form.submit()"
                        style="width: 200px; height: 38px;">
                        <option value="">All Doctors</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ $doctorId == $doctor->id ? 'selected' : '' }}>
                                Dr. {{ $doctor->user->full_name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                {{-- Add Appointment --}}
                <a href="{{ route('backend.appointments.create') }}"
                    class="btn btn-success btn-sm d-flex align-items-center gap-1" style="height:38px;">
                    <i class="bi bi-plus-circle"></i> Add Appointment
                </a>
            </div>
        </div>

        {{-- Week Header --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-2">
                <div class="row g-0 text-center">
                    <div class="col-1 fw-semibold text-muted small">Time</div>
                    @foreach ($weekDays as $day)
                        <div class="col {{ $day['is_today'] ? 'bg-light' : '' }}">
                            <div class="fw-semibold {{ $day['is_today'] ? 'text-primary' : 'text-dark' }}">
                                {{ $day['formatted'] }}
                            </div>
                            <small class="text-muted">{{ $day['date']->format('D') }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Calendar Grid --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="min-height: 500px;">
                        <tbody>
                            @foreach ($timeSlots as $time)
                                <tr>
                                    {{-- Time Column --}}
                                    <td class="bg-light text-center" style="width: 80px;">
                                        <div class="fw-semibold">{{ $time->format('h:i') }}</div>
                                        <small class="text-muted">{{ $time->format('A') }}</small>
                                    </td>

                                    {{-- Day Columns --}}
                                    @foreach ($weekDays as $day)
                                        @php
                                            $dateStr = $day['date']->format('Y-m-d');
                                            $appointmentsForSlot = $appointments->get($dateStr, collect());
                                        @endphp
                                        <td class="position-relative {{ $day['is_today'] ? 'bg-light' : '' }}"
                                            style="height: 60px; min-width: 150px;">

                                            {{-- Display appointments for this chair and time slot --}}
                                            @foreach ($chairs as $chair)
                                                @php
                                                    $chairAppointments = $appointmentsForSlot->get(
                                                        $chair->id,
                                                        collect(),
                                                    );
                                                    $appointmentForThisTime = $chairAppointments->first(function (
                                                        $apt,
                                                    ) use ($time) {
                                                        return \Carbon\Carbon::parse($apt->appointment_time)->format(
                                                            'H:i',
                                                        ) == $time->format('H:i');
                                                    });
                                                @endphp


                                                @if ($appointmentForThisTime)
                                                    <div class="appointment-block mb-1"
                                                        style="border-left: 4px solid #0d6efd; background-color: rgba(13, 110, 253, 0.1);">
                                                        <div class="small p-1">
                                                            <div class="fw-semibold text-truncate">
                                                                {{ $appointmentForThisTime->patient->full_name }}
                                                            </div>
                                                            <small class="text-muted d-block">
                                                                {{ $chair->name }} •
                                                                {{ \Carbon\Carbon::parse($appointmentForThisTime->appointment_time)->format('h:i A') }}
                                                            </small>
                                                            <small class="badge bg-primary">
                                                                {{ str_replace('_', ' ', ucfirst($appointmentForThisTime->status)) }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach

                                            {{-- Empty slot indicator --}}
                                            @if (!$appointmentsForSlot->count())
                                                <div
                                                    class="text-center text-muted small h-100 d-flex align-items-center justify-content-center">
                                                    No appointments
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center gap-3">
                        <span class="small text-muted">Legend:</span>

                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-primary">Scheduled</span>
                            <span class="small text-muted">= Appointments</span>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <div
                                style="width: 20px; height: 20px; background-color: rgba(13, 110, 253, 0.1); 
                                      border-left: 4px solid #0d6efd;">
                            </div>
                            <span class="small text-muted">= Time slot (30 mins)</span>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <div class="bg-light p-1 border"></div>
                            <span class="small text-muted">= Today</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .appointment-block {
            border-radius: 4px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .appointment-block:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        td {
            vertical-align: top;
            padding: 4px !important;
        }

        .table-bordered td {
            border: 1px solid #dee2e6;
        }
    </style>
@endsection
