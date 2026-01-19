@extends('backend.layout.structure')

@section('title', 'Dental Chart Visualization')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="mb-2 fw-bold">Dental Chart Visualization</h1>
                <div class="d-flex align-items-center gap-2">
                    <div
                        class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-semibold">{{ $patient->full_name }}</h5>
                        <small class="text-muted">
                            <i class="bi bi-tag-fill me-1"></i>{{ $patient->patient_code }}
                            • <i class="bi bi-calendar3 me-1"></i>{{ $patient->age ?? '-' }} years
                        </small>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('backend.dental-charts.show', $patient) }}"
                    class="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm px-4">
                    <i class="bi bi-list-check"></i>
                    <span>List View</span>
                </a>
                <a href="{{ route('backend.dental-charts.index') }}"
                    class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm px-4">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>

        {{-- Legend --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-palette2 me-2 text-primary"></i>
                    Tooth Condition Legend
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $conditionColors = [
                            'Healthy' => ['bg' => 'bg-success', 'text' => 'text-success', 'icon' => 'bi-check-circle'],
                            'Cavity' => [
                                'bg' => 'bg-danger',
                                'text' => 'text-danger',
                                'icon' => 'bi-exclamation-triangle',
                            ],
                            'Filled' => ['bg' => 'bg-info', 'text' => 'text-info', 'icon' => 'bi-shield-check'],
                            'Crown' => ['bg' => 'bg-primary', 'text' => 'text-primary', 'icon' => 'bi-gem'],
                            'Missing' => [
                                'bg' => 'bg-secondary',
                                'text' => 'text-secondary',
                                'icon' => 'bi-dash-circle',
                            ],
                            'Implant' => ['bg' => 'bg-warning', 'text' => 'text-warning', 'icon' => 'bi-cpu'],
                            'Root Canal' => ['bg' => 'bg-dark', 'text' => 'text-dark', 'icon' => 'bi-droplet'],
                            'Decay' => [
                                'bg' => 'bg-danger',
                                'text' => 'text-danger',
                                'icon' => 'bi-exclamation-octagon',
                            ],
                            'Fractured' => ['bg' => 'bg-warning', 'text' => 'text-warning', 'icon' => 'bi-lightning'],
                            'Discolored' => ['bg' => 'bg-info', 'text' => 'text-info', 'icon' => 'bi-palette'],
                            'Sensitive' => [
                                'bg' => 'bg-warning',
                                'text' => 'text-warning',
                                'icon' => 'bi-thermometer-half',
                            ],
                            'Other' => ['bg' => 'bg-secondary', 'text' => 'text-secondary', 'icon' => 'bi-three-dots'],
                        ];
                    @endphp
                    @foreach ($legends as $condition)
                        @php
                            $colorData = $conditionColors[$condition] ?? $conditionColors['Other'];
                            $count = $charts->where('tooth_condition', $condition)->count();
                        @endphp
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="d-flex align-items-center gap-3 p-2 border rounded">
                                <div class="{{ $colorData['bg'] }} rounded-circle p-2">
                                    <i class="bi {{ $colorData['icon'] }} text-white fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $condition }}</h6>
                                    <small class="text-muted">{{ $count }} teeth</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Dental Chart Visualization --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-tooth me-2 text-primary"></i>
                        Dental Chart - Universal Numbering System
                    </h5>
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Click on any tooth for details
                    </div>
                </div>
            </div>
            <div class="card-body">

                {{-- Maxilla (Upper Jaw) --}}
                <div class="mb-5">
                    <h6 class="text-center mb-4 text-uppercase text-muted fw-semibold">
                        <i class="bi bi-arrow-up-circle me-2"></i>Maxilla (Upper Jaw)
                    </h6>

                    {{-- Upper Jaw Diagram --}}
                    <div class="d-flex justify-content-center mb-3">
                        <div class="position-relative" style="width: 100%; max-width: 800px; height: 120px;">
                            {{-- Jaw Curve --}}
                            <div
                                class="position-absolute top-0 start-0 w-100 h-100 border-top border-3 border-primary rounded-full">
                            </div>

                            {{-- Upper Teeth --}}
                            <div class="position-absolute top-50 start-50 translate-middle w-100">
                                <div class="d-flex justify-content-between px-5">
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
                                            $colorData = $conditionColors[$condition] ?? $conditionColors['Other'];
                                        @endphp
                                        <div class="tooth-container" data-tooth="{{ $tooth }}"
                                            data-condition="{{ $condition }}"
                                            data-remarks="{{ $chart ? $chart->remarks : '' }}" data-bs-toggle="tooltip"
                                            title="Tooth {{ $tooth }}: {{ $condition }}">
                                            <div class="tooth-icon {{ $colorData['bg'] }} text-white rounded shadow-sm d-flex flex-column align-items-center justify-content-center"
                                                style="width: 50px; height: 50px; cursor: pointer; transition: all 0.3s ease;">
                                                <span class="fw-bold">{{ $tooth }}</span>
                                                <small
                                                    class="small">{{ $loop->iteration <= 8 ? 'Left' : 'Right' }}</small>
                                            </div>
                                            <div class="text-center mt-1 small fw-semibold {{ $colorData['text'] }}">
                                                {{ substr($condition, 0, 3) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Patient Face Diagram --}}
                <div class="text-center mb-5">
                    <div class="position-relative d-inline-block">
                        {{-- Face Outline --}}
                        <div class="rounded-circle border border-3 border-secondary"
                            style="width: 200px; height: 200px; position: relative;">

                            {{-- Eyes --}}
                            <div class="position-absolute" style="top: 30%; left: 25%;">
                                <div class="bg-secondary rounded-circle" style="width: 20px; height: 10px;"></div>
                            </div>
                            <div class="position-absolute" style="top: 30%; right: 25%;">
                                <div class="bg-secondary rounded-circle" style="width: 20px; height: 10px;"></div>
                            </div>

                            {{-- Nose --}}
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <div class="bg-secondary rounded-pill" style="width: 15px; height: 30px;"></div>
                            </div>

                            {{-- Mouth --}}
                            <div class="position-absolute" style="bottom: 20%; left: 25%; right: 25%;">
                                <div class="border-top border-3 border-primary rounded-pill" style="height: 30px;"></div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-muted mb-0">{{ $patient->full_name }}</h6>
                            <small class="text-muted">Patient View</small>
                        </div>
                    </div>
                </div>

                {{-- Mandible (Lower Jaw) --}}
                <div class="mt-5">
                    <h6 class="text-center mb-4 text-uppercase text-muted fw-semibold">
                        <i class="bi bi-arrow-down-circle me-2"></i>Mandible (Lower Jaw)
                    </h6>

                    {{-- Lower Jaw Diagram --}}
                    <div class="d-flex justify-content-center">
                        <div class="position-relative" style="width: 100%; max-width: 800px; height: 120px;">
                            {{-- Jaw Curve --}}
                            <div
                                class="position-absolute bottom-0 start-0 w-100 h-100 border-bottom border-3 border-primary rounded-full">
                            </div>

                            {{-- Lower Teeth --}}
                            <div class="position-absolute top-50 start-50 translate-middle w-100">
                                <div class="d-flex justify-content-between px-5">
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
                                            $condition = $chart ? $chart->tooth_condition : 'Unknown';
                                            $colorData = $conditionColors[$condition] ?? $conditionColors['Other'];
                                        @endphp
                                        <div class="tooth-container" data-tooth="{{ $tooth }}"
                                            data-condition="{{ $condition }}"
                                            data-remarks="{{ $chart ? $chart->remarks : '' }}" data-bs-toggle="tooltip"
                                            title="Tooth {{ $tooth }}: {{ $condition }}">
                                            <div class="text-center mb-1 small fw-semibold {{ $colorData['text'] }}">
                                                {{ substr($condition, 0, 3) }}
                                            </div>
                                            <div class="tooth-icon {{ $colorData['bg'] }} text-white rounded shadow-sm d-flex flex-column align-items-center justify-content-center"
                                                style="width: 50px; height: 50px; cursor: pointer; transition: all 0.3s ease;">
                                                <span class="fw-bold">{{ $tooth }}</span>
                                                <small
                                                    class="small">{{ $loop->iteration <= 8 ? 'Left' : 'Right' }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tooth Details Modal --}}
                <div class="modal fade" id="toothDetailsModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tooth Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center mb-4">
                                    <div class="tooth-display-icon mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px; font-size: 2rem;">
                                        <i class="bi bi-tooth"></i>
                                    </div>
                                    <h4 id="toothNumber" class="fw-bold mb-0"></h4>
                                    <span id="toothCondition" class="badge fs-6 mt-2"></span>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-1">Position</h6>
                                                <h5 id="toothPosition" class="fw-bold mb-0"></h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-1">Quadrant</h6>
                                                <h5 id="toothQuadrant" class="fw-bold mb-0"></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Remarks</h6>
                                    <div class="p-3 border rounded bg-white">
                                        <p id="toothRemarks" class="mb-0"></p>
                                        <p id="noRemarks" class="mb-0 text-muted fst-italic d-none">No remarks recorded
                                        </p>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <a href="#" id="editToothLink" class="btn btn-primary">
                                        <i class="bi bi-pencil-square me-2"></i>Edit This Tooth
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Statistics --}}
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                        <h3>{{ $charts->where('tooth_condition', 'Healthy')->count() }}</h3>
                        <p class="text-muted mb-0">Healthy Teeth</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3"></i>
                        <h3>{{ $charts->whereIn('tooth_condition', ['Cavity', 'Decay', 'Fractured'])->count() }}</h3>
                        <p class="text-muted mb-0">Problem Teeth</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check fs-1 text-info mb-3"></i>
                        <h3>{{ $charts->whereIn('tooth_condition', ['Filled', 'Crown', 'Implant', 'Root Canal'])->count() }}
                        </h3>
                        <p class="text-muted mb-0">Treated Teeth</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .avatar {
            width: 48px;
            height: 48px;
        }

        .tooth-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .tooth-container {
            position: relative;
            z-index: 1;
        }

        .tooth-display-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .jaw-diagram {
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            border-radius: 50% 50% 0 0;
        }

        .tooth-missing {
            opacity: 0.5;
            background-color: #e9ecef !important;
            color: #6c757d !important;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // Tooth click handler
            document.querySelectorAll('.tooth-container').forEach(function(tooth) {
                tooth.addEventListener('click', function() {
                    const toothNumber = this.getAttribute('data-tooth');
                    const condition = this.getAttribute('data-condition');
                    const remarks = this.getAttribute('data-remarks');

                    // Determine position and quadrant
                    const toothInt = parseInt(toothNumber);
                    let position = '';
                    let quadrant = '';

                    if (toothInt >= 11 && toothInt <= 18) {
                        position = 'Upper Right';
                        quadrant = '1st Quadrant';
                    } else if (toothInt >= 21 && toothInt <= 28) {
                        position = 'Upper Left';
                        quadrant = '2nd Quadrant';
                    } else if (toothInt >= 31 && toothInt <= 38) {
                        position = 'Lower Left';
                        quadrant = '3rd Quadrant';
                    } else if (toothInt >= 41 && toothInt <= 48) {
                        position = 'Lower Right';
                        quadrant = '4th Quadrant';
                    }

                    // Update modal content
                    document.getElementById('toothNumber').textContent = 'Tooth ' + toothNumber;
                    document.getElementById('toothCondition').textContent = condition;
                    document.getElementById('toothCondition').className = 'badge fs-6 mt-2 ' +
                        getConditionClass(condition);
                    document.getElementById('toothPosition').textContent = position;
                    document.getElementById('toothQuadrant').textContent = quadrant;

                    // Handle remarks
                    if (remarks && remarks.trim() !== '') {
                        document.getElementById('toothRemarks').textContent = remarks;
                        document.getElementById('toothRemarks').classList.remove('d-none');
                        document.getElementById('noRemarks').classList.add('d-none');
                    } else {
                        document.getElementById('toothRemarks').classList.add('d-none');
                        document.getElementById('noRemarks').classList.remove('d-none');
                    }

                    // Update edit link
                    const editLink = document.getElementById('editToothLink');
                    editLink.href =
                        `/backend/dental-charts?tooth=${toothNumber}&patient={{ $patient->id }}`;

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('toothDetailsModal'));
                    modal.show();
                });
            });

            function getConditionClass(condition) {
                const conditionClasses = {
                    'Healthy': 'bg-success',
                    'Cavity': 'bg-danger',
                    'Filled': 'bg-info',
                    'Crown': 'bg-primary',
                    'Missing': 'bg-secondary',
                    'Implant': 'bg-warning',
                    'Root Canal': 'bg-dark',
                    'Decay': 'bg-danger',
                    'Fractured': 'bg-warning',
                    'Discolored': 'bg-info',
                    'Sensitive': 'bg-warning',
                    'Other': 'bg-secondary',
                    'Unknown': 'bg-light text-dark'
                };
                return conditionClasses[condition] || 'bg-secondary';
            }

            // Add hover effect to teeth
            document.querySelectorAll('.tooth-icon').forEach(function(icon) {
                icon.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });

                icon.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
@endsection
