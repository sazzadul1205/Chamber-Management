{{-- backend.patient-families.index --}}
@extends('backend.layout.structure')

@section('title', 'Patient Families')


{{-- Extra CSS for Hover Shadow --}}
@section('styles')
    <style>
        /* Base card shadow */
        .card.shadow-sm {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        /* Hover effect */
        .hover-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.50);
        }
    </style>
@endsection



@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

            {{-- Title --}}
            <div>
                <h1 class="mb-1">Patient Families</h1>
                <p class="text-muted mb-0">List of all registered patient families</p>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2 flex-wrap">

                {{-- Search Box --}}
                <form action="{{ route('backend.patient-families.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search by Family or Head Patient" value="{{ request('search') }}"
                        style="min-width: 220px; height: 38px;">
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                        style="height: 38px;">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>

                {{-- Refresh Button --}}
                <a href="{{ route('backend.patient-families.index') }}"
                    class="btn btn-info btn-sm d-flex align-items-center gap-1" style="height: 38px;">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>

                {{-- Add Family --}}
                <a href="{{ route('backend.patient-families.create') }}"
                    class="btn btn-success btn-sm d-flex align-items-center gap-1" style="height:38px;">
                    <i class="bi bi-plus-circle"></i> Add Family
                </a>

            </div>
        </div>

        {{-- Families Grid --}}
        <div class="row g-4">
            @forelse($families as $family)
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 hover-shadow">
                        <div class="card-body d-flex flex-column">

                            {{-- Header: Family Name --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0 fw-bold">{{ $family->family_name }}</h5>
                                <span class="badge bg-info text-dark">{{ $family->member_count }} members</span>
                            </div>

                            {{-- Head Patient Info --}}
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-person-circle fs-4 me-2 text-primary"></i>
                                <div>
                                    <div class="fw-semibold">{{ $family->headPatient?->full_name ?? '—' }}</div>
                                    <small class="text-muted">{{ $family->headPatient?->patient_code ?? '—' }}</small>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-auto d-flex gap-2 flex">
                                <a href="{{ route('backend.patient-families.show', $family->id) }}"
                                    class="btn btn-sm btn-outline-info d-flex align-items-center gap-1 w-100">
                                    <i class="bi bi-eye"></i> View
                                </a>

                                <a href="{{ route('backend.patient-families.edit', $family->id) }}"
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 w-100">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <form action="{{ route('backend.patient-families.destroy', $family->id) }}" method="POST"
                                    class="d-inline w-100" onsubmit="return confirm('Delete this family?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 w-100">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 mb-2"></i>
                    <div class="fw-semibold">No families found.</div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($families->lastPage() > 1)
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                <div class="text-muted small">
                    Showing page <strong>{{ $families->currentPage() }}</strong> of
                    <strong>{{ $families->lastPage() }}</strong>
                    (Total: {{ $families->total() }} families)
                </div>

                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ $families->previousPageUrl() }}"
                        class="btn btn-outline-primary {{ $families->onFirstPage() ? 'disabled' : '' }}">
                        &lsaquo; Prev
                    </a>
                    <a href="{{ $families->nextPageUrl() }}"
                        class="btn btn-outline-primary {{ $families->currentPage() == $families->lastPage() ? 'disabled' : '' }}">
                        Next &rsaquo;
                    </a>
                </div>
            </div>
        @endif
    </div>

@endsection
