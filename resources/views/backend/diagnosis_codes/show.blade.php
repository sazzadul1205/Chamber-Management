@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Diagnosis Code Details</h1>
                <p class="text-gray-600 mt-1">
                    View complete information for ICD-10 Code: {{ $diagnosisCode->code }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.diagnosis-codes.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Back',
                        'class' => 'w-4 h-4',
                    ])
                    Back to List
                </a>
                <a href="{{ route('backend.diagnosis-codes.edit', $diagnosisCode->id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Edit',
                        'class' => 'w-4 h-4 text-white',
                    ])
                    Edit Code
                </a>
                <button type="button"
                    onclick="openDeleteModal('{{ route('backend.diagnosis-codes.destroy', $diagnosisCode->id) }}', '{{ addslashes($diagnosisCode->code) }}')"
                    class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Delete',
                        'class' => 'w-4 h-4 text-white',
                    ])
                    Delete Code
                </button>
            </div>
        </div>

        <!-- DETAILS CARD -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 space-y-6">

                <!-- BASIC INFORMATION SECTION -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ICD-10 Code -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700">ICD-10 Code</span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Code
                                </span>
                            </div>
                            <div class="text-lg font-semibold text-gray-900 p-3 bg-gray-50 rounded-md">
                                {{ $diagnosisCode->code }}
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700">Category</span>
                            </div>
                            <div class="text-lg font-semibold text-gray-900 p-3 bg-gray-50 rounded-md">
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                                    {{ $diagnosisCode->category_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STATUS SECTION -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700">Current Status</span>
                            </div>
                            <div
                                class="text-lg font-semibold p-3 rounded-md {{ $diagnosisCode->status == 'active' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                                <span class="flex items-center gap-2">
                                    <span
                                        class="w-2 h-2 rounded-full {{ $diagnosisCode->status == 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ ucfirst($diagnosisCode->status) }}
                                </span>
                                <p
                                    class="text-sm font-normal mt-1 {{ $diagnosisCode->status == 'active' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $diagnosisCode->status == 'active' ? 'Available for patient diagnosis' : 'Hidden from selection' }}
                                </p>
                            </div>
                        </div>

                        <!-- Usage Stats -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700">Record Information</span>
                            </div>
                            <div class="space-y-3 p-3 bg-gray-50 rounded-md">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Created</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $diagnosisCode->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Last Updated</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $diagnosisCode->updated_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Time Created</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $diagnosisCode->created_at->format('h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPTION SECTION -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700">Full Description</span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-md min-h-[100px]">
                            @if ($diagnosisCode->description)
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $diagnosisCode->description }}</p>
                            @else
                                <p class="text-gray-400 italic">No description provided</p>
                            @endif
                        </div>
                        @if ($diagnosisCode->description)
                            <div class="text-xs text-gray-500 text-right">
                                {{ strlen($diagnosisCode->description) }} characters
                            </div>
                        @endif
                    </div>
                </div>

                <!-- TIMESTAMP SECTION -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Timestamps</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Creation Details -->
                        <div class="space-y-3 p-4 bg-blue-50 rounded-md">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-blue-800">Created</span>
                            </div>
                            <div class="space-y-1">
                                <div class="text-sm text-blue-700">
                                    <span class="font-medium">Date:</span>
                                    {{ $diagnosisCode->created_at->format('F j, Y') }}
                                </div>
                                <div class="text-sm text-blue-700">
                                    <span class="font-medium">Time:</span>
                                    {{ $diagnosisCode->created_at->format('h:i A') }}
                                </div>
                                <div class="text-sm text-blue-700">
                                    <span class="font-medium">Full:</span>
                                    {{ $diagnosisCode->created_at->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                        </div>

                        <!-- Update Details -->
                        <div class="space-y-3 p-4 bg-green-50 rounded-md">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span class="text-sm font-medium text-green-800">Last Updated</span>
                            </div>
                            <div class="space-y-1">
                                <div class="text-sm text-green-700">
                                    <span class="font-medium">Date:</span>
                                    {{ $diagnosisCode->updated_at->format('F j, Y') }}
                                </div>
                                <div class="text-sm text-green-700">
                                    <span class="font-medium">Time:</span>
                                    {{ $diagnosisCode->updated_at->format('h:i A') }}
                                </div>
                                <div class="text-sm text-green-700">
                                    <span class="font-medium">Full:</span>
                                    {{ $diagnosisCode->updated_at->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">

            <!-- Header -->
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-red-600">Delete Diagnosis Code</h3>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-gray-700 text-sm mb-2">
                    Are you sure you want to delete the diagnosis code "<span id="codeName"
                        class="font-semibold"></span>"?
                </p>
                <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.343 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-red-800">Warning</p>
                            <p class="text-xs text-red-700 mt-1">
                                This action cannot be undone. All records associated with this diagnosis code will be
                                affected.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 font-medium">
                    Cancel
                </button>

                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium">
                        Yes, Delete Code
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function openDeleteModal(actionUrl, codeName) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const nameSpan = document.getElementById('codeName');

            form.action = actionUrl;
            nameSpan.textContent = codeName;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeDeleteModal();
            }
        });
    </script>
@endsection
