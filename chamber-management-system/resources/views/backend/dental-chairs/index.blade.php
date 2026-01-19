{{-- backend.dental-chairs.index --}}
@extends('backend.layout.structure')

@section('title', 'Dental Chairs')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Dental Chairs</h1>
                <p class="text-muted mb-0">Overview of all dental chairs in the clinic</p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('backend.dental-chairs.create') }}"
                    class="btn btn-success btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-plus-circle"></i> Add Chair
                </a>
                <a href="{{ route('backend.dental-chairs.dashboard') }}"
                    class="btn btn-info btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>
        </div>

        {{-- Cards --}}
        <div class="row g-3">
            @forelse ($chairs as $chair)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card shadow-sm h-100 text-center p-3">

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
                        <h6 class="fw-semibold mb-1">{{ $chair->name }}</h6>

                        {{-- Status Badge --}}
                        @if ($chair->isAvailable)
                            <span class="badge bg-success">Available</span>
                        @elseif($chair->isOccupied)
                            <span class="badge bg-warning text-dark">Occupied</span>
                        @elseif($chair->isUnderMaintenance)
                            <span class="badge bg-danger">Maintenance</span>
                        @endif

                        {{-- Inside the card div for each chair --}}
                        <div class="mt-3 d-flex justify-content-center gap-1 flex-wrap">

                            {{-- Edit Button --}}
                            <a href="{{ route('backend.dental-chairs.edit', $chair->id) }}"
                                class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                <i class="bi bi-pencil"></i> Edit
                            </a>

                            {{-- Delete Button --}}
                            <form action="{{ route('backend.dental-chairs.destroy', $chair->id) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Delete this chair?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>

                            {{-- Status Changer Dropdown --}}
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-gear"></i> Status
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @foreach (['available' => 'Available', 'occupied' => 'Occupied', 'maintenance' => 'Maintenance'] as $key => $label)
                                        <li>
                                            <form action="{{ route('backend.dental-chairs.update-status', $chair->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $key }}">
                                                <button type="submit" class="dropdown-item">{{ $label }}</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card shadow-sm text-center p-4 text-muted">
                        No dental chairs found
                    </div>
                </div>
            @endforelse
        </div>

    </div>
@endsection
