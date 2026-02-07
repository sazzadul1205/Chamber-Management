@php
    $statusClasses = [
        'requested' => 'bg-blue-100 text-blue-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];

    $typeBg = [
        'xray' => 'bg-blue-100',
        'lab_report' => 'bg-green-100',
        'ct_scan' => 'bg-purple-100',
        'default' => 'bg-gray-100',
    ];

    $typeIcon = [
        'xray' => 'Xray',
        'lab_report' => 'Flask',
        'ct_scan' => 'Brain',
        'default' => 'MedicalFile',
    ];
@endphp

<!-- Test Requests Section -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                Test Requests ({{ $treatment->medicalFiles->count() }})
            </h3>

            <div class="flex items-center gap-2">
                <!-- Manage Tests Button -->
                <a href="{{ route('backend.medical-files.index', ['treatment_id' => $treatment->id]) }}"
                    class="px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                    Manage Tests
                </a>

                <!-- Request Test Button -->
                <a href="{{ route('backend.medical-files.create', ['patient_id' => $treatment->patient_id, 'treatment_id' => $treatment->id]) }}"
                    class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">

                    Request Test
                </a>
            </div>
        </div>
    </div>

    @if ($treatment->medicalFiles->count())
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($treatment->medicalFiles->sortByDesc('requested_date') as $file)
                @php
                    $bgClass = $typeBg[$file->file_type] ?? $typeBg['default'];
                    $iconName = $typeIcon[$file->file_type] ?? $typeIcon['default'];
                @endphp

                <div class="border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <!-- Card Header -->
                    <div class="p-4 border-b">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center {{ $bgClass }}">
                                    @include('partials.sidebar-icon', [
                                        'name' => $iconName,
                                        'class' => 'w-5 h-5',
                                    ])
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $file->file_type_text }}</h4>
                                    <p class="text-xs text-gray-500">{{ $file->file_code }}</p>
                                </div>
                            </div>

                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$file->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $file->status_text }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-4">
                        @if ($file->requested_notes)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Instructions</p>
                                <p class="text-sm text-gray-800 line-clamp-2">
                                    {{ Str::limit($file->requested_notes, 100) }}
                                </p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-gray-500">Requested</p>
                                <p class="font-medium">{{ $file->requested_date->format('d/m/Y') }}</p>
                            </div>

                            @if ($file->expected_delivery_date)
                                <div>
                                    <p class="text-gray-500">Expected</p>
                                    <p class="font-medium">
                                        {{ \Carbon\Carbon::parse($file->expected_delivery_date)->format('d/m/Y') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        @if ($file->isUploaded)
                            <div class="bg-green-50 border border-green-100 rounded-lg p-3 flex items-center gap-2">
                                @include('partials.sidebar-icon', [
                                    'name' => 'CheckCircle',
                                    'class' => 'w-4 h-4 text-green-600',
                                ])
                                <div>
                                    <p class="text-sm font-medium text-green-800">Result Uploaded</p>
                                    <p class="text-xs text-green-600">{{ $file->uploaded_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('backend.medical-files.show', $file) }}"
                                class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg flex items-center justify-center gap-1">
                                @include('partials.sidebar-icon', [
                                    'name' => 'B_View',
                                    'class' => 'w-4 h-4',
                                ])
                                View
                            </a>

                            @if (!$file->isUploaded && $file->status != 'cancelled')
                                <a href="{{ route('backend.medical-files.edit', $file) }}"
                                    class="flex-1 px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg flex items-center justify-center gap-1">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'B_Edit',
                                        'class' => 'w-4 h-4',
                                    ])
                                    Upload
                                </a>
                            @endif

                            @if ($file->isUploaded)
                                <a href="{{ route('backend.medical-files.download', $file) }}"
                                    class="flex-1 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded-lg flex items-center justify-center gap-1">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'B_Print',
                                        'class' => 'w-4 h-4',
                                    ])
                                    Download
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-12 text-center">
            <div class="mx-auto w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                @include('partials.sidebar-icon', [
                    'name' => 'MedicalFile',
                    'class' => 'w-10 h-10 text-indigo-400',
                ])
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Test Requests Yet</h3>
            <p class="text-gray-500 mb-6">Request X-rays, lab reports, or other tests for this treatment</p>

            <a href="{{ route('backend.medical-files.create', ['patient_id' => $treatment->patient_id, 'treatment_id' => $treatment->id]) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                @include('partials.sidebar-icon', ['name' => 'B_Plus', 'class' => 'w-4 h-4'])
                Request Your First Test
            </a>
        </div>
    @endif
</div>
