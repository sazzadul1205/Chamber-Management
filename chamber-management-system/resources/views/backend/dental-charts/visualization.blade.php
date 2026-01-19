{{-- backend.dental-charts.show --}}
@extends('backend.layout.structure')

@section('title', 'Dental Chart Visualization')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h1 class="mb-1">Dental Chart: {{ $patient->full_name }}</h1>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('backend.dental-charts.index', ['patient' => $patient->id]) }}"
                    class="btn btn-secondary btn-sm">
                    List View
                </a>
                <a href="{{ route('backend.dental-charts.create', ['patient_id' => $patient->id]) }}"
                    class="btn btn-success btn-sm">
                    Add Record
                </a>
                <a href="{{ route('backend.patients.show', $patient) }}" class="btn btn-primary btn-sm">
                    Patient Details
                </a>
            </div>
        </div>

        {{-- Patient Info --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-person-circle fs-2 text-primary"></i>
                <div>
                    <h5 class="mb-0">{{ $patient->full_name }}</h5>
                    <small class="text-muted">{{ $patient->patient_code }} • {{ $patient->age }} years •
                        {{ ucfirst($patient->gender) }}</small>
                    <p class="text-muted mt-1">{{ $patient->dental_chart_summary }}</p>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-2">Condition Legend:</h6>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $legends = [
                            'Healthy' => 'bg-success text-white border',
                            'Cavity' => 'bg-warning text-dark border',
                            'Filled' => 'bg-primary text-white border',
                            'Crown' => 'bg-purple text-white border',
                            'Missing' => 'bg-danger text-white border',
                            'Implant' => 'bg-info text-white border',
                            'Root Canal' => 'bg-orange text-white border',
                            'Decay' => 'bg-brown text-white border',
                            'Fractured' => 'bg-pink text-white border',
                            'Discolored' => 'bg-indigo text-white border',
                            'Sensitive' => 'bg-teal text-white border',
                            'Other' => 'bg-secondary text-white border',
                        ];
                    @endphp
                    @foreach ($legends as $condition => $classes)
                        <span class="px-2 py-1 rounded small {{ $classes }}">{{ $condition }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Dental Chart Visualization --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form action="{{ route('backend.dental-charts.bulk-update', $patient->id) }}" method="POST"
                    id="dentalChartForm">
                    @csrf

                    {{-- Upper Jaw --}}
                    <h6 class="text-center fw-bold mb-3">Upper Jaw (Maxillary)</h6>
                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
                        @php
                            $upperTeeth = [
                                '18',
                                '17',
                                '16',
                                '15',
                                '14',
                                '13',
                                '12',
                                '11',
                                '21',
                                '22',
                                '23',
                                '24',
                                '25',
                                '26',
                                '27',
                                '28',
                            ];
                        @endphp
                        @foreach ($upperTeeth as $tooth)
                            @php
                                $chart = $charts[$tooth] ?? null;
                                $condition = $chart ? $chart->tooth_condition : 'Unknown';
                            @endphp
                            <div class="text-center position-relative">
                                <div class="border rounded p-2 cursor-pointer" style="width:60px;height:80px;"
                                    onclick="openToothModal('{{ $tooth }}','{{ $chart->tooth_condition ?? '' }}','{{ $chart->remarks ?? '' }}')">
                                    <div class="fw-bold">{{ $tooth }}</div>
                                    <div class="small text-muted">{{ $chart->tooth_condition ?? 'Not recorded' }}</div>
                                </div>

                                @if ($chart)
                                    <a href="{{ route('backend.dental-charts.edit', $chart) }}"
                                        class="position-absolute top-0 end-0 btn btn-sm btn-primary p-0 px-1"
                                        title="Edit">
                                        ✏️
                                    </a>
                                @endif

                                <input type="hidden" name="charts[{{ $tooth }}][tooth_number]"
                                    value="{{ $tooth }}">
                                <input type="hidden" name="charts[{{ $tooth }}][tooth_condition]"
                                    value="{{ $chart->tooth_condition ?? '' }}" id="condition_{{ $tooth }}">
                                <input type="hidden" name="charts[{{ $tooth }}][remarks]"
                                    value="{{ $chart->remarks ?? '' }}" id="remarks_{{ $tooth }}">
                            </div>
                        @endforeach
                    </div>

                    {{-- Lower Jaw --}}
                    <h6 class="text-center fw-bold mb-3">Lower Jaw (Mandibular)</h6>
                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
                        @php
                            $lowerTeeth = [
                                '48',
                                '47',
                                '46',
                                '45',
                                '44',
                                '43',
                                '42',
                                '41',
                                '31',
                                '32',
                                '33',
                                '34',
                                '35',
                                '36',
                                '37',
                                '38',
                            ];
                        @endphp
                        @foreach ($lowerTeeth as $tooth)
                            @php
                                $chart = $charts[$tooth] ?? null;
                            @endphp
                            <div class="text-center position-relative">
                                <div class="border rounded p-2 cursor-pointer" style="width:60px;height:80px;"
                                    onclick="openToothModal('{{ $tooth }}','{{ $chart->tooth_condition ?? '' }}','{{ $chart->remarks ?? '' }}')">
                                    <div class="fw-bold">{{ $tooth }}</div>
                                    <div class="small text-muted">{{ $chart->tooth_condition ?? 'Not recorded' }}</div>
                                </div>

                                @if ($chart)
                                    <a href="{{ route('backend.dental-charts.edit', $chart) }}"
                                        class="position-absolute top-0 end-0 btn btn-sm btn-primary p-0 px-1"
                                        title="Edit">
                                        ✏️
                                    </a>
                                @endif

                                <input type="hidden" name="charts[{{ $tooth }}][tooth_number]"
                                    value="{{ $tooth }}">
                                <input type="hidden" name="charts[{{ $tooth }}][tooth_condition]"
                                    value="{{ $chart->tooth_condition ?? '' }}" id="condition_{{ $tooth }}">
                                <input type="hidden" name="charts[{{ $tooth }}][remarks]"
                                    value="{{ $chart->remarks ?? '' }}" id="remarks_{{ $tooth }}">
                            </div>
                        @endforeach
                    </div>

                    {{-- Bulk Update --}}
                    <div class="text-center">
                        <button type="button" onclick="openBulkUpdateModal()" class="btn btn-success">
                            Quick Bulk Update
                        </button>
                        <p class="small text-muted mt-1">Click a tooth to edit individually or use bulk update.</p>
                    </div>

                </form>
            </div>
        </div>

    </div>

    {{-- Tooth Modal --}}
    <div class="modal fade" id="toothModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Tooth <span id="modalToothNumber"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Condition</label>
                        <select id="modalCondition" class="form-select">
                            <option value="">Select Condition</option>
                            @foreach ($legends as $condition => $classes)
                                <option value="{{ $condition }}">{{ $condition }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea id="modalRemarks" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveToothChanges()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentTooth = '';

            function openToothModal(toothNumber, condition, remarks) {
                currentTooth = toothNumber;
                document.getElementById('modalToothNumber').textContent = toothNumber;
                document.getElementById('modalCondition').value = condition;
                document.getElementById('modalRemarks').value = remarks || '';
                new bootstrap.Modal(document.getElementById('toothModal')).show();
            }

            function saveToothChanges() {
                const condition = document.getElementById('modalCondition').value;
                const remarks = document.getElementById('modalRemarks').value;

                if (currentTooth && condition) {
                    document.getElementById(`condition_${currentTooth}`).value = condition;
                    document.getElementById(`remarks_${currentTooth}`).value = remarks;

                    // Update visual text
                    const toothDiv = document.querySelector(`[onclick*="${currentTooth}"]`);
                    if (toothDiv) {
                        const span = toothDiv.querySelector('.small');
                        if (span) span.textContent = condition;
                    }

                    currentTooth = '';
                    bootstrap.Modal.getInstance(document.getElementById('toothModal')).hide();
                } else {
                    alert('Please select a condition.');
                }
            }

            function openBulkUpdateModal() {
                if (confirm('This will update all teeth with current selections. Continue?')) {
                    document.getElementById('dentalChartForm').submit();
                }
            }
        </script>
    @endpush
@endsection
