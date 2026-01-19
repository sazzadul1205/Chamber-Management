@extends('backend.layout.structure')

@section('title', 'Dental Chart Details')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Dental Chart Details</h1>
                <p class="text-muted mb-0">Affected teeth for {{ $patient->full_name }}</p>
            </div>

            {{-- Edit Latest Chart --}}
            @php
                $latestChart = $patient->dentalCharts->sortByDesc('created_at')->first();
            @endphp
            <div class="d-flex gap-2">
                <a href="{{ route('backend.dental-charts.edit', $latestChart) }}"
                    class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="{{ route('backend.dental-charts.index') }}"
                    class="btn btn-sm btn-secondary d-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-arrow-left-circle"></i> Back
                </a>
            </div>
        </div>

        {{-- Patient Info --}}
        <div class="mb-4 p-3 bg-light rounded d-flex align-items-center gap-3">
            <i class="bi bi-person-circle fs-2 text-primary"></i>
            <div>
                <h5 class="mb-0">{{ $patient->full_name }}</h5>
                <small class="text-muted">
                    {{ $patient->patient_code }} • {{ $patient->age ?? '-' }} years •
                    {{ ucfirst($patient->gender ?? '-') }}
                </small>
            </div>
        </div>

        {{-- Teeth Table (only affected teeth) --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Tooth Number</th>
                                <th>Condition</th>
                                <th>Remarks</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($charts as $index => $chart)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $chart->tooth_number }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $chart->tooth_condition)) }}</td>
                                    <td>{{ $chart->remarks ?? '-' }}</td>
                                    <td>{{ $chart->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        No affected teeth recorded for this patient.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
