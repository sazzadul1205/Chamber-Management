@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Procedure Details</h1>
                <p class="text-gray-600 mt-1">
                    View complete information about {{ $procedureCatalog->procedure_name }}
                </p>
            </div>

            <a href="{{ route('backend.procedure-catalog.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                @include('partials.sidebar-icon', [
                    'name' => 'B_Back',
                    'class' => 'w-4 h-4',
                ])
                Back to Procedures
            </a>
        </div>

        <!-- DETAILS CARD -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <!-- BASIC INFORMATION SECTION -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    Procedure Code
                                </label>
                                <p class="text-gray-900 font-medium">{{ $procedureCatalog->procedure_code }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    Category
                                </label>
                                <p class="text-gray-900 font-medium">{{ $procedureCatalog->category_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    Status
                                </label>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $procedureCatalog->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($procedureCatalog->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    Procedure Name
                                </label>
                                <p class="text-gray-900 font-medium">{{ $procedureCatalog->procedure_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    Standard Duration
                                </label>
                                <p class="text-gray-900 font-medium">{{ $procedureCatalog->formatted_duration }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    Standard Cost
                                </label>
                                <p class="text-gray-900 font-medium">{{ $procedureCatalog->formatted_cost }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPTION SECTION -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        @if ($procedureCatalog->description)
                            <p class="text-gray-700 whitespace-pre-line">{{ $procedureCatalog->description }}</p>
                        @else
                            <p class="text-gray-500 italic">No description provided</p>
                        @endif
                    </div>
                </div>

                <!-- ADDITIONAL INFORMATION SECTION -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Appointment Information</p>
                                    <p class="text-xs text-blue-700 mt-1">
                                        This procedure requires {{ $procedureCatalog->standard_duration }} minutes per
                                        appointment
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 border border-green-100 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-green-900">Pricing Information</p>
                                    <p class="text-xs text-green-700 mt-1">
                                        Base cost: {{ $procedureCatalog->formatted_cost }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TIMESTAMP INFORMATION -->
                <div class="border-t pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                Created On
                            </label>
                            <p class="text-gray-900">
                                {{ $procedureCatalog->created_at->format('F d, Y') }}
                                <span class="text-gray-500 text-xs">
                                    at {{ $procedureCatalog->created_at->format('h:i A') }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                Last Updated
                            </label>
                            <p class="text-gray-900">
                                {{ $procedureCatalog->updated_at->format('F d, Y') }}
                                <span class="text-gray-500 text-xs">
                                    at {{ $procedureCatalog->updated_at->format('h:i A') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTIONS SECTION -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-500">
                        Procedure ID: <span class="font-mono text-gray-700">{{ $procedureCatalog->id }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('backend.procedure-catalog.edit', $procedureCatalog->id) }}"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Procedure
                        </a>

                        <button type="button" onclick="showDeleteModal()"
                            class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Procedure
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.342 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="mt-4 text-center">
                    <h3 class="text-lg font-medium text-gray-900">Delete Procedure</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete the procedure "{{ $procedureCatalog->procedure_name }}"?
                        </p>
                        <p class="text-xs text-red-600 mt-2">
                            <strong>Warning:</strong> This action cannot be undone. All appointments and records associated
                            with this procedure will be affected.
                        </p>
                    </div>
                    <div class="flex justify-center gap-3 mt-6">
                        <form action="{{ route('backend.procedure-catalog.destroy', $procedureCatalog->id) }}"
                            method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow transition">
                                Yes, Delete
                            </button>
                        </form>
                        <button onclick="hideDeleteModal()"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md shadow transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('deleteModal');
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    hideDeleteModal();
                }
            });

            // Close with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    hideDeleteModal();
                }
            });
        });
    </script>
@endsection
