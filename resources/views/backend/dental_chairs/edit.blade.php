@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Dental Chair: {{ $dentalChair->name }}</h1>
                <p class="text-gray-600 mt-1">
                    Update dental chair information and status
                </p>
                <div class="mt-2 flex items-center gap-2 text-sm">
                    <span class="px-2 py-1 rounded-full text-white"
                        style="background-color: {{ $dentalChair->status_color == 'success' ? '#10b981' : ($dentalChair->status_color == 'warning' ? '#f59e0b' : '#ef4444') }}">
                        {{ $dentalChair->status_name }}
                    </span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">Code: {{ $dentalChair->chair_code }}</span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">{{ $dentalChair->name }}</span>
                </div>
            </div>
        </div>

        <!-- VALIDATION ERRORS -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM CARD -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.dental-chairs.update', $dentalChair->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    <!-- BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Chair Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chair Code *
                                </label>
                                <input type="text" name="chair_code"
                                    value="{{ old('chair_code', $dentalChair->chair_code) }}" readonly required
                                    maxlength="20"
                                    class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Chair code cannot be changed</p>
                            </div>

                            <!-- Chair Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chair Name *
                                </label>
                                <input type="text" name="name" value="{{ old('name', $dentalChair->name) }}" required
                                    maxlength="50" placeholder="e.g., Main Chair, Emergency Chair"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Location
                                </label>
                                <input type="text" name="location" value="{{ old('location', $dentalChair->location) }}"
                                    maxlength="100" placeholder="e.g., Room A, Left side, 2nd Floor"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Status</option>
                                    @foreach ($statuses as $key => $label)
                                        <option value="{{ $key }}" @selected(old('status', $dentalChair->status) == $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Notes - Full Width -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Notes
                                </label>
                                <textarea name="notes" rows="4"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Any special notes about this chair...">{{ old('notes', $dentalChair->notes) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Additional information about the chair's location,
                                    equipment, or special considerations</p>
                            </div>
                        </div>
                    </div>

                    <!-- CHAIR INFORMATION -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Usage Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <!-- Last Used -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Last Used
                                </label>
                                <input type="text" value="{{ $dentalChair->formatted_last_used }}" readonly
                                    class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Appointments Count -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Total Appointments
                                </label>
                                <input type="text" value="{{ $dentalChair->appointments->count() }} appointments"
                                    readonly
                                    class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Current Status Display -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Current Status
                                </label>
                                <div class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50 px-3 py-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        style="background-color: {{ $dentalChair->status_color == 'success' ? '#d1fae5' : ($dentalChair->status_color == 'warning' ? '#fef3c7' : '#fee2e2') }};
                                               color: {{ $dentalChair->status_color == 'success' ? '#065f46' : ($dentalChair->status_color == 'warning' ? '#92400e' : '#991b1b') }}">
                                        {{ $dentalChair->status_name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QUICK STATS -->
                    @if ($dentalChair->appointments->count() > 0)
                        <div class="border-t pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Appointments</h3>
                                <a href="{{ route('backend.appointments.index', ['chair_id' => $dentalChair->id]) }}"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    View All
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($dentalChair->appointments()->latest()->take(2)->get() as $appointment)
                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-gray-800">
                                                        {{ $appointment->patient_name }}
                                                    </span>
                                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                                        {{ $appointment->formatted_time }}
                                                    </span>
                                                </div>
                                                <div class="text-sm text-gray-600 mt-1">{{ $appointment->formatted_date }}
                                                </div>
                                                @if ($appointment->notes)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ Str::limit($appointment->notes, 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <x-back-submit-buttons back-url="{{ route('backend.dental-chairs.show', $dentalChair) }}"
                        submit-text="Update Chair" delete-modal-id="deleteModal" submit-color="blue" />
                </div>
            </form>
        </div>

        <!-- QUICK LINKS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('backend.appointments.create', ['chair_id' => $dentalChair->id]) }}"
                class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-blue-300 hover:shadow transition">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">New Appointment</h3>
                        <p class="text-sm text-gray-500 mt-1">Schedule appointment for this chair</p>
                    </div>
                </div>
            </a>

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Dental Chair</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to delete dental chair "{{ $dentalChair->name }}"?
                    This action cannot be undone. All related appointments and data will be permanently removed.
                </p>
                <form action="{{ route('backend.dental-chairs.destroy', $dentalChair->id) }}" method="POST"
                    class="space-y-4">
                    @csrf
                    @method('DELETE')
                    <div>
                        <label for="confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Type "DELETE" to confirm:
                        </label>
                        <input type="text" id="confirmation" name="confirmation" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder="Type DELETE here">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideDeleteModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 border border-transparent rounded-md text-sm font-medium text-white">
                            Delete Chair
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('confirmation').value = '';
        }

        // Close modal on outside click
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });

        // Capitalize first letter of chair name on blur
        const chairNameInput = document.querySelector('[name="name"]');
        if (chairNameInput) {
            chairNameInput.addEventListener('blur', function() {
                if (this.value) {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                }
            });
        }
    </script>
@endsection
