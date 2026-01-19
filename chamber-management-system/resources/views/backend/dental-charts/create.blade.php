@php
    $patientJson = $patients->map(function ($p) {
        return [
            'id' => $p->id,
            'full_name' => $p->full_name,
            'patient_code' => $p->patient_code,
        ];
    });
@endphp

@extends('backend.layout.structure')

@section('title', 'Add Dental Chart Record')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1">Add Dental Chart Record</h1>
                <p class="text-muted mb-0">Create a new dental chart entry for a patient</p>
            </div>

            <a href="{{ route('backend.dental-charts.index') }}"
                class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left-circle"></i> Back to List
            </a>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('backend.dental-charts.store') }}">
            @csrf

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    {{-- Patient --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Patient <span class="text-danger">*</span>
                        </label>

                        <div id="patient_select_react" data-patients='@json($patientJson)'
                            data-old="{{ old('patient_id', optional($patient)->id) }}"></div>

                        @error('patient_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tooth & Condition --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Tooth & Condition <span class="text-danger">*</span>
                        </label>

                        <div id="dental_teeth_react" data-old='@json(old('charts', []))'></div>

                        @error('charts')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remarks --}}
                    <div class="mb-3">
                        <label class="form-label">Remarks / Notes</label>
                        <textarea name="remarks" rows="4" class="form-control">{{ old('remarks') }}</textarea>

                        @error('remarks')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Save Dental Chart
                        </button>

                        <a href="{{ route('backend.dental-charts.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>

                </div>
            </div>
        </form>

    </div>
@endsection

@section('scripts')
    @vite('resources/js/reactApp.jsx')
@endsection
