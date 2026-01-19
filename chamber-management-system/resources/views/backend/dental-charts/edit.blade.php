@extends('backend.layout.structure')

@section('title', 'Edit Dental Chart')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Edit Dental Chart</h1>
                <p class="text-muted mb-0">Update multiple teeth for {{ $patient->full_name }}</p>
            </div>
            <a href="{{ route('backend.dental-charts.index') }}"
                class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left-circle"></i> Back to List
            </a>
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

        {{-- Form for editing dental charts --}}
        <form action="{{ route('backend.dental-charts.bulk-update', ['patient' => $patient->id]) }}" method="POST">
            @csrf

            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- React Tooth Selector --}}
                    @php
                        $chartData = $charts->map(
                            fn($c) => [
                                'tooth_number' => $c->tooth_number,
                                'tooth_condition' => $c->tooth_condition,
                            ],
                        );
                        // Get universal remark from latest chart
                        $remark = $charts->sortByDesc('created_at')->first()?->remarks ?? '';
                    @endphp
                    <div id="dental_teeth_react" data-old='@json($chartData)'></div>

                    {{-- Global Remark --}}
                    <div class="mt-3">
                        <label for="remarks" class="form-label">Remarks / Notes</label>
                        <textarea name="remarks" id="remarks" rows="3" class="form-control">{{ old('remarks', $remark) }}</textarea>
                        @error('remarks')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Form Actions --}}
                    <div class="mt-4 d-flex gap-2 justify-content-end">
                        <a href="{{ route('backend.dental-charts.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Dental Chart</button>
                    </div>

                </div>
            </div>
        </form>

    </div>
@endsection

@section('scripts')
    @vite('resources/js/reactApp.jsx')
@endsection
