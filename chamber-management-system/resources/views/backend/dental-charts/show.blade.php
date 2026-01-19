@extends('backend.layout.structure')

@section('title', 'Dental Chart Details')

@section('content')
    <div class="px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="mb-2 fw-bold">Dental Chart Record</h1>
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
                            • <i class="bi bi-gender-ambiguous me-1"></i>{{ ucfirst($patient->gender ?? '-') }}
                        </small>
                    </div>
                </div>
            </div>

            @php
                $latestChart = $patient->dentalCharts->sortByDesc('created_at')->first();
            @endphp

            <div class="d-flex gap-2">
                {{-- Visualization button --}}
                <a href="{{ route('backend.dental-charts.visualization', $patient) }}"
                    class="btn btn-info d-flex align-items-center gap-2 shadow-sm px-4">
                    <i class="bi bi-eye"></i>
                    <span>Visualization</span>
                </a>

                {{-- Edit Button --}}
                @if ($latestChart)
                    <a href="{{ route('backend.dental-charts.edit', $latestChart->id) }}"
                        class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-4">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit Chart</span>
                    </a>
                @endif

                {{-- Back Button --}}
                <a href="{{ route('backend.dental-charts.index') }}"
                    class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm px-4">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>Back to List</span>
                </a>
            </div>
        </div>

        {{-- Dental Chart List --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-list-check me-2 text-primary"></i>
                        Teeth Records
                    </h5>
                    <span class="badge bg-primary rounded-pill">{{ $charts->count() }} teeth</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if ($charts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase small">
                                <tr>
                                    <th class="ps-4">Tooth</th>
                                    <th>Condition</th>
                                    <th>Remarks</th>
                                    <th>Last Updated</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($charts as $chart)
                                    <tr class="border-bottom">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="tooth-icon bg-light border rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <span class="fw-bold text-primary">{{ $chart->tooth_number }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">Tooth {{ $chart->tooth_number }}</h6>
                                                    <small class="text-muted">Universal Numbering</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $conditionColors = [
                                                    'Healthy' => 'success',
                                                    'Cavity' => 'danger',
                                                    'Filled' => 'info',
                                                    'Crown' => 'primary',
                                                    'Missing' => 'secondary',
                                                    'Implant' => 'warning',
                                                    'Root Canal' => 'dark',
                                                    'Decay' => 'danger',
                                                    'Fractured' => 'warning',
                                                    'Discolored' => 'info',
                                                    'Sensitive' => 'warning',
                                                    'Other' => 'secondary',
                                                ];
                                                $color = $conditionColors[$chart->tooth_condition] ?? 'secondary';
                                                $icons = [
                                                    'Healthy' => 'bi-check-circle',
                                                    'Cavity' => 'bi-exclamation-triangle',
                                                    'Filled' => 'bi-shield-check',
                                                    'Crown' => 'bi-gem',
                                                    'Missing' => 'bi-dash-circle',
                                                    'Implant' => 'bi-cpu',
                                                    'Root Canal' => 'bi-droplet',
                                                    'Decay' => 'bi-exclamation-octagon',
                                                    'Fractured' => 'bi-lightning',
                                                    'Discolored' => 'bi-palette',
                                                    'Sensitive' => 'bi-thermometer-half',
                                                    'Other' => 'bi-three-dots',
                                                ];
                                                $icon = $icons[$chart->tooth_condition] ?? 'bi-circle';
                                            @endphp
                                            <span
                                                class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} border-opacity-25 d-flex align-items-center gap-2 py-2 px-3 rounded-pill">
                                                <i class="bi {{ $icon }}"></i>
                                                {{ ucfirst(str_replace('_', ' ', $chart->tooth_condition)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($chart->remarks)
                                                <div class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-chat-left-text text-muted mt-1"></i>
                                                    <span
                                                        class="text-muted small">{{ Str::limit($chart->remarks, 60) }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic small">No remarks</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-clock-history text-muted"></i>
                                                <div>
                                                    <div class="small">{{ $chart->updated_at->format('d M Y') }}</div>
                                                    <div class="text-muted smaller">
                                                        {{ $chart->updated_at->format('h:i A') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('backend.dental-charts.single.edit', $chart->id) }}"
                                                    class="btn btn-outline-warning btn-sm px-3" data-bs-toggle="tooltip"
                                                    title="Edit Tooth">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form
                                                    action="{{ route('backend.dental-charts.single.destroy', $chart->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Delete this tooth record?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3"
                                                        data-bs-toggle="tooltip" title="Delete Tooth">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-tooth display-1 text-muted opacity-25"></i>
                        </div>
                        <h5 class="text-muted mb-2">No Dental Records Found</h5>
                        <p class="text-muted mb-4">This patient has no dental chart records yet.</p>
                        <a href="{{ route('backend.dental-charts.create', ['patient_id' => $patient->id]) }}"
                            class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle"></i>
                            Create First Record
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <style>
        .avatar {
            width: 48px;
            height: 48px;
        }

        .icon-wrapper {
            width: 60px;
            height: 60px;
        }

        .tooth-icon {
            transition: all 0.3s ease;
        }

        .tooth-icon:hover {
            transform: scale(1.1);
            background-color: #e3f2fd !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .table>tbody>tr {
            transition: all 0.2s ease;
        }

        .table>tbody>tr:hover {
            background-color: rgba(0, 123, 255, 0.05) !important;
        }

        .smaller {
            font-size: 0.75rem;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection
