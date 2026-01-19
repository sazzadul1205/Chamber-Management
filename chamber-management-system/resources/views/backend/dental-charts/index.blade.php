@extends('backend.layout.structure')

@section('title', 'Dental Charts')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Dental Charts</h1>
                <p class="text-muted mb-0">Latest dental chart summary per patient</p>
            </div>

            <a href="{{ route('backend.dental-charts.create') }}"
                class="btn btn-success btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-plus-circle"></i> Add Dental Chart
            </a>
        </div>

        {{-- Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Teeth Recorded</th>
                                <th>Tooth Numbers</th>
                                <th>Last Updated</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($patients as $patient)
                                @php
                                    $charts = $patient->dentalCharts;
                                    $toothNumbers = $charts->pluck('tooth_number')->implode(', ');
                                    $lastUpdated = $charts->max('updated_at');
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration + ($patients->currentPage() - 1) * $patients->perPage() }}</td>

                                    {{-- Patient Info --}}
                                    <td>
                                        <div class="fw-semibold">{{ $patient->full_name }}</div>
                                        <small class="text-muted">{{ $patient->patient_code }}</small>
                                    </td>

                                    {{-- Teeth Count --}}
                                    <td>
                                        <span class="badge bg-secondary">{{ $charts->count() }}</span>
                                    </td>

                                    {{-- Tooth Numbers --}}
                                    <td>
                                        <div class="small text-muted">{{ $toothNumbers }}</div>
                                    </td>

                                    {{-- Last Updated --}}
                                    <td>
                                        <div class="small text-muted">
                                            {{ $lastUpdated ? $lastUpdated->format('d M Y') : '-' }}
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-end">
                                        {{-- View Details --}}
                                        <a href="{{ route('backend.dental-charts.show', $patient->id) }}"
                                            class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                            title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>


                                        {{-- Edit Latest Chart --}}
                                        @php
                                            $latestChart = $patient->dentalCharts->sortByDesc('created_at')->first();
                                        @endphp
                                        @if ($latestChart)
                                            <a href="{{ route('backend.dental-charts.edit', $latestChart) }}"
                                                class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                                title="Edit Latest Chart">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            {{-- Delete Latest Chart --}}
                                            <form action="{{ route('backend.dental-charts.destroy', $latestChart) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this chart?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip"
                                                    title="Delete Latest Chart">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No dental chart records found
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
                            <a href="{{ $patients->previousPageUrl() }}{{ request()->except('page') ? '&' . http_build_query(request()->except('page')) : '' }}"
                                class="btn btn-outline-primary {{ $patients->onFirstPage() ? 'disabled' : '' }}">
                                &lsaquo; Prev
                            </a>

                            <a href="{{ $patients->nextPageUrl() }}{{ request()->except('page') ? '&' . http_build_query(request()->except('page')) : '' }}"
                                class="btn btn-outline-primary {{ $patients->currentPage() == $patients->lastPage() ? 'disabled' : '' }}">
                                Next &rsaquo;
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
