@extends('backend.layout.structure')

@section('title', 'Edit Tooth Record')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Edit Tooth Record</h1>
                <p class="text-muted mb-0">
                    Update dental condition for a single tooth
                </p>
            </div>

            <a href="{{ route('backend.dental-charts.show', $chart->patient_id) }}"
                class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left-circle"></i>
                Back to Chart
            </a>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('backend.dental-charts.single.update', $chart->id) }}">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    {{-- Patient Info (Read-only) --}}
                    <div class="mb-4">
                        <label class="form-label">Patient</label>

                        <div class="form-control bg-light">
                            <strong>{{ $chart->patient->full_name }}</strong>
                            <span class="text-muted ms-2">
                                ({{ $chart->patient->patient_code }})
                            </span>
                        </div>
                    </div>

                    {{-- Tooth Number (Read-only) --}}
                    <div class="mb-4">
                        <label class="form-label">Tooth Number</label>
                        <input type="text" class="form-control bg-light" value="{{ $chart->tooth_number }}" readonly>
                    </div>

                    {{-- Tooth Condition --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Tooth Condition <span class="text-danger">*</span>
                        </label>

                        <select name="tooth_condition" class="form-select" required>
                            @foreach ($toothConditions as $condition)
                                <option value="{{ $condition }}"
                                    {{ $chart->tooth_condition === $condition ? 'selected' : '' }}>
                                    {{ $condition }}
                                </option>
                            @endforeach
                        </select>

                        @error('tooth_condition')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remarks --}}
                    <div class="mb-3">
                        <label class="form-label">Remarks / Notes</label>
                        <textarea name="remarks" rows="4" class="form-control">{{ old('remarks', $chart->remarks) }}</textarea>

                        @error('remarks')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Update Tooth
                        </button>

                        <a href="{{ route('backend.dental-charts.show', $chart->patient_id) }}"
                            class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>

                </div>
            </div>
        </form>

    </div>
@endsection
