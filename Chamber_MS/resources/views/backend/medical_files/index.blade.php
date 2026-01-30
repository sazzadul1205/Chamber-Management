@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Medical Test Requests</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.medical-files.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    Request New Test
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.medical-files.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3">

            <!-- Search -->
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search test code"
                    class="w-full border rounded px-3 py-2">
            </div>

            <!-- Patient -->
            <div class="md:col-span-2">
                <select name="patient_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Patients</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Treatment -->
            <div class="md:col-span-2">
                <select name="treatment_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Treatments</option>
                    @foreach ($treatments as $treatment)
                        <option value="{{ $treatment->id }}"
                            {{ request('treatment_id') == $treatment->id ? 'selected' : '' }}>
                            {{ $treatment->treatment_code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Requested</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Test Type -->
            <div class="md:col-span-2">
                <select name="file_type" class="w-full border rounded px-3 py-2">
                    <option value="">All Types</option>
                    <option value="xray" {{ request('file_type') == 'xray' ? 'selected' : '' }}>X-Ray</option>
                    <option value="lab_report" {{ request('file_type') == 'lab_report' ? 'selected' : '' }}>Lab Report
                    </option>
                    <option value="ct_scan" {{ request('file_type') == 'ct_scan' ? 'selected' : '' }}>CT Scan</option>
                    <option value="photo" {{ request('file_type') == 'photo' ? 'selected' : '' }}>Photo</option>
                    <option value="document" {{ request('file_type') == 'document' ? 'selected' : '' }}>Document</option>
                    <option value="prescription" {{ request('file_type') == 'prescription' ? 'selected' : '' }}>
                        Prescription</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
                    Filter
                </button>
            </div>

            <!-- Clear Filters -->
            @if (request()->anyFilled(['search', 'patient_id', 'treatment_id', 'status', 'file_type']))
                <div class="md:col-span-1">
                    <a href="{{ route('backend.medical-files.index') }}"
                        class="w-full inline-block text-center bg-gray-300 hover:bg-gray-400 text-gray-800 rounded px-3 py-2">
                        Clear
                    </a>
                </div>
            @endif
        </form>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $stats = [
                    'requested' => [
                        'color' => 'blue',
                        'icon' => 'Schedule',
                        'count' => $medicalFiles->where('status', 'requested')->count(),
                    ],
                    'pending' => [
                        'color' => 'yellow',
                        'icon' => 'Pending',
                        'count' => $medicalFiles->where('status', 'pending')->count(),
                    ],
                    'completed' => [
                        'color' => 'green',
                        'icon' => 'Completed', 
                        'count' => $medicalFiles->where('status', 'completed')->count(),
                    ],
                    'cancelled' => [
                        'color' => 'red',
                        'icon' => 'Cancel',
                        'count' => $medicalFiles->where('status', 'cancelled')->count(),
                    ],
                ];
            @endphp

            @foreach ($stats as $status => $data)
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 capitalize">{{ $status }} Tests</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $data['count'] }}</p>
                        </div>

                        <div class="w-12 h-12 bg-{{ $data['color'] }}-100 rounded-full flex items-center justify-center">
                            @include('partials.sidebar-icon', [
                                'name' => $data['icon'],
                                'class' => 'w-8 h-8 text-' . $data['color'] . '-600',
                            ])
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">Test Details</th>
                        <th class="px-3 py-2 text-left text-sm">Patient</th>
                        <th class="px-3 py-2 text-left text-sm">Treatment</th>
                        <th class="px-3 py-2 text-left text-sm">Status</th>
                        <th class="px-3 py-2 text-left text-sm">Dates</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($medicalFiles as $file)
                        <tr class="hover:bg-gray-50">
                            <!-- Test Details -->
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-3">
                                    @php
                                        $fileTypeMap = [
                                            'xray' => [
                                                'bg' => 'bg-blue-100',
                                                'icon' => 'Xray',
                                                'color' => 'text-blue-600',
                                            ],
                                            'lab_report' => [
                                                'bg' => 'bg-green-100',
                                                'icon' => 'Flask',
                                                'color' => 'text-green-600',
                                            ],
                                            'ct_scan' => [
                                                'bg' => 'bg-purple-100',
                                                'icon' => 'Brain',
                                                'color' => 'text-purple-600',
                                            ],
                                        ];

                                        $fileUI = $fileTypeMap[$file->file_type] ?? [
                                            'bg' => 'bg-gray-100',
                                            'icon' => 'MedicalFile',
                                            'color' => 'text-gray-600',
                                        ];
                                    @endphp

                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center {{ $fileUI['bg'] }}">
                                        @include('partials.sidebar-icon', [
                                            'name' => $fileUI['icon'],
                                            'class' => 'w-5 h-5 ' . $fileUI['color'],
                                        ])
                                    </div>

                                    <div>
                                        <a href="{{ route('backend.medical-files.show', $file) }}"
                                            class="font-medium text-gray-900 hover:text-blue-700 transition-colors">
                                            {{ $file->file_type_text }}
                                        </a>
                                        <div class="text-xs text-gray-500">
                                            {{ $file->file_code }}
                                        </div>
                                        @if ($file->requested_notes)
                                            <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                                {{ Str::limit($file->requested_notes, 60) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Patient -->
                            <td class="px-3 py-2">
                                @if ($file->patient)
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'User',
                                                'class' => 'w-4 h-4 text-blue-600',
                                            ])
                                        </div>
                                        <div>
                                            <a href="{{ route('backend.patients.show', $file->patient_id) }}"
                                                class="text-blue-600 hover:underline font-medium">
                                                {{ $file->patient->full_name }}
                                            </a>
                                            <div class="text-xs text-gray-500">
                                                {{ $file->patient->patient_code }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>

                            <!-- Treatment -->
                            <td class="px-3 py-2">
                                @if ($file->treatment)
                                    <div>
                                        <a href="{{ route('backend.treatments.show', $file->treatment_id) }}"
                                            class="font-medium text-gray-900 hover:text-blue-700">
                                            {{ $file->treatment->treatment_code }}
                                        </a>
                                        <div class="text-xs text-gray-500">
                                            {{ optional($file->treatment->patient)->full_name ?? 'N/A' }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-3 py-2">
                                @php
                                    $statusClasses = [
                                        'requested' => 'bg-blue-100 text-blue-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded {{ $statusClasses[$file->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $file->status_text }}
                                </span>
                                @if ($file->requestedBy)
                                    <div class="text-xs text-gray-500 mt-1">
                                        By: {{ $file->requestedBy->full_name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Dates -->
                            <td class="px-3 py-2">
                                <div class="text-sm">
                                    <div class="text-gray-700">
                                        {{ $file->requested_date->format('M d, Y') }}
                                    </div>
                                    @if ($file->expected_delivery_date)
                                        <div class="text-xs text-gray-500">
                                            Expected:
                                            {{ \Carbon\Carbon::parse($file->expected_delivery_date)->format('M d') }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-3 py-2">
                                <div class="flex justify-center gap-1">
                                    <!-- View -->
                                    <a href="{{ route('backend.medical-files.show', $file) }}"
                                        class="w-8 h-8 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                        title="View Details">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    @if (!$file->isUploaded && $file->status != 'cancelled')
                                        <!-- Upload -->
                                        <a href="{{ route('backend.medical-files.edit', $file) }}"
                                            class="w-8 h-8 flex items-center justify-center bg-green-500 hover:bg-green-600 text-white rounded text-xs"
                                            title="Upload Result">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Upload',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </a>
                                    @endif

                                    @if ($file->isUploaded)
                                        <!-- Download -->
                                        <a href="{{ route('backend.medical-files.download', $file) }}"
                                            class="w-8 h-8 flex items-center justify-center bg-purple-500 hover:bg-purple-600 text-white rounded text-xs"
                                            title="Download">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Download',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </a>
                                    @endif

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('backend.medical-files.destroy', $file) }}"
                                        onsubmit="return confirm('Delete this test request?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded text-xs"
                                            title="Delete">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Delete',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <div
                                    class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'MedicalFile',
                                        'class' => 'w-8 h-8 text-gray-400',
                                    ])
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Test Requests Found</h3>
                                <p class="mb-4">Start by creating a new test request.</p>
                                <a href="{{ route('backend.medical-files.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'B_Add',
                                        'class' => 'w-4 h-4 mr-2',
                                    ])
                                    Request Your First Test
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($medicalFiles->hasPages())
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $medicalFiles->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <script>
        // Auto-select treatment if coming from treatment page
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const treatmentId = urlParams.get('treatment_id');

            if (treatmentId) {
                const treatmentSelect = document.querySelector('select[name="treatment_id"]');
                if (treatmentSelect) {
                    treatmentSelect.value = treatmentId;
                }
            }
        });
    </script>
@endsection
