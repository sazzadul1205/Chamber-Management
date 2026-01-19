{{-- backend.appointments.dashboard --}}
@extends('backend.layout.structure')

@section('title', 'Appointments Dashboard')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Appointments Dashboard</h1>
                <p class="text-muted mb-0">Today's overview: {{ $today }}</p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                {{-- Date Navigation --}}
                <div class="btn-group" role="group">
                    <a href="{{ route('backend.appointments.dashboard', ['date' => \Carbon\Carbon::parse($today)->subDay()->format('Y-m-d')]) }}" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-chevron-left"></i> Yesterday
                    </a>
                    <a href="{{ route('backend.appointments.dashboard', ['date' => today()->format('Y-m-d')]) }}" 
                       class="btn btn-outline-primary">
                        Today
                    </a>
                    <a href="{{ route('backend.appointments.dashboard', ['date' => \Carbon\Carbon::parse($today)->addDay()->format('Y-m-d')]) }}" 
                       class="btn btn-outline-primary">
                        Tomorrow <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                {{-- Calendar View --}}
                <a href="{{ route('backend.appointments.calendar') }}" 
                   class="btn btn-info btn-sm d-flex align-items-center gap-1" style="height: 38px;">
                    <i class="bi bi-calendar-week"></i> Calendar View
                </a>

                {{-- List View --}}
                <a href="{{ route('backend.appointments.index', ['date' => $today]) }}" 
                   class="btn btn-warning btn-sm d-flex align-items-center gap-1" style="height: 38px;">
                    <i class="bi bi-list"></i> List View
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            {{-- Total Appointments --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-calendar-check text-primary fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $counts['total'] }}</h3>
                                <p class="text-muted mb-0">Total Appointments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scheduled --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-clock text-info fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $counts['scheduled'] }}</h3>
                                <p class="text-muted mb-0">Scheduled</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- In Progress --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-play-circle text-warning fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $counts['in_progress'] + $counts['checked_in'] }}</h3>
                                <p class="text-muted mb-0">In Clinic</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Completed --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $counts['completed'] }}</h3>
                                <p class="text-muted mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left Column: Appointments by Status --}}
            <div class="col-lg-8">
                {{-- Scheduled Appointments --}}
                @if(isset($appointments['scheduled']) && $appointments['scheduled']->count() > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Scheduled Appointments ({{ $appointments['scheduled']->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Chair</th>
                                        <th>Type</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments['scheduled'] as $apt)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</div>
                                            <small class="text-muted">Slot</small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $apt->patient->full_name }}</div>
                                            <small class="text-muted">{{ $apt->patient->phone }}</small>
                                        </td>
                                        <td>
                                            <div>Dr. {{ $apt->doctor->full_name }}</div>
                                            <small class="text-muted">{{ $apt->doctor->specialization ?? 'General' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $apt->chair->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($apt->appointment_type) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('backend.appointments.show', $apt->id) }}" 
                                                   class="btn btn-outline-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <form action="{{ route('backend.appointments.update-status', $apt->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="checked_in">
                                                    <button type="submit" class="btn btn-outline-success" title="Check In">
                                                        <i class="bi bi-person-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('backend.appointments.update-status', $apt->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-outline-danger" title="Cancel">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- In Clinic (Checked In + In Progress) --}}
                @php
                    $inClinic = collect();
                    if(isset($appointments['checked_in'])) {
                        $inClinic = $inClinic->merge($appointments['checked_in']);
                    }
                    if(isset($appointments['in_progress'])) {
                        $inClinic = $inClinic->merge($appointments['in_progress']);
                    }
                @endphp
                
                @if($inClinic->count() > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-activity me-2"></i>In Clinic ({{ $inClinic->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($inClinic as $apt)
                            <div class="col-md-6">
                                <div class="card border {{ $apt->status == 'in_progress' ? 'border-warning' : 'border-info' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">{{ $apt->patient->full_name }}</h6>
                                                <small class="text-muted">{{ $apt->patient->phone }}</small>
                                            </div>
                                            <span class="badge bg-{{ $apt->status == 'in_progress' ? 'warning' : 'info' }}">
                                                {{ str_replace('_', ' ', ucfirst($apt->status)) }}
                                            </span>
                                        </div>
                                        
                                        <div class="row g-2 small">
                                            <div class="col-6">
                                                <i class="bi bi-person-badge text-muted me-1"></i>
                                                Dr. {{ $apt->doctor->full_name }}
                                            </div>
                                            <div class="col-6">
                                                <i class="bi bi-hospital text-muted me-1"></i>
                                                {{ $apt->chair->name }}
                                            </div>
                                            <div class="col-6">
                                                <i class="bi bi-clock text-muted me-1"></i>
                                                {{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}
                                            </div>
                                            <div class="col-6">
                                                @if($apt->status == 'checked_in')
                                                <form action="{{ route('backend.appointments.update-status', $apt->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button type="submit" class="btn btn-sm btn-warning">
                                                        Start Treatment
                                                    </button>
                                                </form>
                                                @else
                                                <form action="{{ route('backend.appointments.update-status', $apt->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        Complete
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- FIFO Queue --}}
                @if($fifoQueue->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-ol me-2"></i>FIFO Queue ({{ $fifoQueue->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">Queue #</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Arrival Time</th>
                                        <th>Wait Time</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fifoQueue as $apt)
                                    @php
                                        $waitTime = $apt->created_at->diffInMinutes(now());
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="text-center">
                                                <span class="badge bg-primary fs-6">#{{ $apt->queue_no }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $apt->patient->full_name }}</div>
                                            <small class="text-muted">{{ $apt->patient->phone }}</small>
                                        </td>
                                        <td>
                                            <div>Dr. {{ $apt->doctor->full_name }}</div>
                                        </td>
                                        <td>
                                            {{ $apt->created_at->format('h:i A') }}
                                        </td>
                                        <td>
                                            @if($waitTime < 60)
                                                <span class="badge bg-success">{{ $waitTime }} min</span>
                                            @elseif($waitTime < 120)
                                                <span class="badge bg-warning">{{ $waitTime }} min</span>
                                            @else
                                                <span class="badge bg-danger">{{ $waitTime }} min</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('backend.appointments.show', $apt->id) }}" 
                                                   class="btn btn-outline-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <form action="{{ route('backend.appointments.update-status', $apt->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="checked_in">
                                                    <button type="submit" class="btn btn-outline-success" title="Call">
                                                        <i class="bi bi-telephone-inbound"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right Column: Clinic Status --}}
            <div class="col-lg-4">
                {{-- Chair Status --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-hospital me-2"></i>Chair Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="mb-0">{{ $availableChairs }}</h3>
                                <p class="text-muted mb-0">Available</p>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0">{{ $occupiedChairs }}</h3>
                                <p class="text-muted mb-0">Occupied</p>
                            </div>
                        </div>
                        
                        <div class="progress mb-3" style="height: 20px;">
                            @php
                                $totalChairs = $availableChairs + $occupiedChairs;
                                $availablePercent = $totalChairs > 0 ? ($availableChairs / $totalChairs) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $availablePercent }}%">
                                {{ $availableChairs }}
                            </div>
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ 100 - $availablePercent }}%">
                                {{ $occupiedChairs }}
                            </div>
                        </div>
                        
                        <a href="{{ route('backend.dental-chairs.dashboard') }}" 
                           class="btn btn-outline-dark btn-sm w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> View All Chairs
                        </a>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Today's Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                <span class="text-muted">Scheduled</span>
                                <span class="badge bg-primary rounded-pill">{{ $counts['scheduled'] }}</span>
                            </div>
                            
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                <span class="text-muted">Checked In</span>
                                <span class="badge bg-info rounded-pill">{{ $counts['checked_in'] }}</span>
                            </div>
                            
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                <span class="text-muted">In Progress</span>
                                <span class="badge bg-warning rounded-pill">{{ $counts['in_progress'] }}</span>
                            </div>
                            
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                <span class="text-muted">Completed</span>
                                <span class="badge bg-success rounded-pill">{{ $counts['completed'] }}</span>
                            </div>
                            
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                <span class="text-muted">Cancelled/No Show</span>
                                <span class="badge bg-danger rounded-pill">
                                    {{ (isset($appointments['cancelled']) ? $appointments['cancelled']->count() : 0) + 
                                       (isset($appointments['no_show']) ? $appointments['no_show']->count() : 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('backend.appointments.create', ['date' => $today]) }}" 
                               class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i> New Appointment
                            </a>
                            
                            <a href="{{ route('backend.appointments.create', ['appointment_type' => 'fifo', 'date' => $today]) }}" 
                               class="btn btn-secondary">
                                <i class="bi bi-person-plus me-2"></i> Add Walk-in (FIFO)
                            </a>
                            
                            <a href="{{ route('backend.patients.create') }}" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-person-add me-2"></i> New Patient
                            </a>
                            
                            <button type="button" class="btn btn-outline-warning" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i> Print Today's Schedule
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Auto-refresh script --}}
    @if(request('auto_refresh', 'true') == 'true')
    <script>
        // Auto-refresh dashboard every 60 seconds
        setTimeout(function() {
            window.location.href = "{{ route('backend.appointments.dashboard', ['date' => $today, 'auto_refresh' => 'true']) }}";
        }, 60000);
        
        // Show refresh notification
        document.addEventListener('DOMContentLoaded', function() {
            const refreshBtn = document.createElement('button');
            refreshBtn.className = 'btn btn-sm btn-outline-info position-fixed';
            refreshBtn.style.bottom = '20px';
            refreshBtn.style.right = '20px';
            refreshBtn.style.zIndex = '1000';
            refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Auto-refresh ON';
            refreshBtn.onclick = function() {
                window.location.reload();
            };
            document.body.appendChild(refreshBtn);
        });
    </script>
    @endif

    <style>
        .appointment-block {
            border-radius: 4px;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .appointment-block:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .progress-bar {
            font-weight: bold;
        }
    </style>
@endsection 