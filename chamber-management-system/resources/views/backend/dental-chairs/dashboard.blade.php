{{-- backend.dental-chairs.dashboard --}}
@extends('backend.layout.structure')

@section('title', 'Dental Chairs Dashboard')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Dental Chairs Dashboard</h1>
                <p class="text-muted mb-0">Overview of all dental chairs and their current status</p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('backend.dental-chairs.index') }}"
                    class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left-circle"></i> Back to List
                </a>

                <a href="{{ route('backend.dental-chairs.create') }}"
                    class="btn btn-success btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-plus-circle"></i> Add Chair
                </a>
            </div>
        </div>

        {{-- Status Summary --}}
        <div class="row mb-4 g-3">
            <div class="col-md-4">
                <div class="card shadow-sm text-center p-3">
                    <h5>Available Chairs</h5>
                    <h2 class="text-success">{{ $availableChairs }}</h2>
                    <i class="bi bi-check-circle text-success fs-2"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm text-center p-3">
                    <h5>Occupied Chairs</h5>
                    <h2 class="text-warning">{{ $occupiedChairs }}</h2>
                    <i class="bi bi-person-fill text-warning fs-2"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm text-center p-3">
                    <h5>Under Maintenance</h5>
                    <h2 class="text-danger">{{ $maintenanceChairs }}</h2>
                    <i class="bi bi-tools text-danger fs-2"></i>
                </div>
            </div>
        </div>

        {{-- Chairs Grid --}}
        <div class="row g-3">
            @forelse ($chairs as $chair)
                <div class="col-md-3 col-sm-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            {{-- SVG Dental Chair Icon --}}
                            <div class="mb-2">
                                @php
                                    $color = $chair->isAvailable ? 'green' : ($chair->isOccupied ? 'orange' : 'red');
                                @endphp
                                {!! file_get_contents(public_path('svg/dental-chair.svg')) !!}
                                <style>
                                    svg {
                                        width: 60px;
                                        height: 60px;
                                        fill: {{ $color }};
                                    }
                                </style>
                            </div>

                            {{-- Chair Name --}}
                            <h5 class="card-title">{{ $chair->name }}</h5>

                            {{-- Status --}}
                            <p>
                                @if ($chair->is_available)
                                    <span class="badge bg-success">Available</span>
                                @elseif($chair->is_occupied)
                                    <span class="badge bg-warning text-dark">Occupied</span>
                                @elseif($chair->is_under_maintenance)
                                    <span class="badge bg-danger">Maintenance</span>
                                @endif
                            </p>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-center gap-1 flex-wrap mt-2">
                                <a href="{{ route('backend.dental-chairs.edit', $chair->id) }}"
                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('backend.dental-chairs.destroy', $chair->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Delete this chair?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">
                    No dental chairs found
                </div>
            @endforelse
        </div>

    </div>
@endsection
