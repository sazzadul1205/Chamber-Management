{{-- backend.patient-families.index --}}
@extends('backend.layout.structure')

@section('title', 'Patient Families')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h1 class="h3 mb-0">Patient Families</h1>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('backend.patient-families.create') }}"
                    class="btn btn-success btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-plus-circle"></i> Add Family
                </a>

                <form action="{{ route('backend.patient-families.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search by Family or Head Patient" value="{{ $search ?? '' }}"
                        style="min-width:220px; height:38px;">
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                        style="height:38px;">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>
        </div>

        {{-- Families List --}}
        <div class="row g-3">
            @forelse($families as $family)
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            {{-- Header --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $family->family_name }}</h5>
                                <span class="badge bg-info text-dark">{{ $family->member_count }} members</span>
                            </div>

                            {{-- Head Patient --}}
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-circle fs-4 me-2 text-primary"></i>
                                <div>
                                    <div class="fw-semibold">{{ $family->headPatient?->full_name ?? '—' }}</div>
                                    <small class="text-muted">{{ $family->headPatient?->patient_code ?? '—' }}</small>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-auto d-flex gap-2 flex-wrap">
                                <a href="{{ route('backend.patient-families.show', $family->id) }}"
                                    class="btn btn-sm btn-outline-info d-flex align-items-center gap-1">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('backend.patient-families.edit', $family->id) }}"
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <form action="{{ route('backend.patient-families.destroy', $family->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Delete this family?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    No families found.
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
