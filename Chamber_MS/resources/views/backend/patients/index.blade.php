@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Patients Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.patients.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    <span>Register New Patient</span>
                </a>
            </div>
        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded mb-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded mb-2">{{ session('error') }}</div>
        @endif

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.patients.index') }}"
            class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by code, name, or phone"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <select name="status"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="deceased" {{ request('status') == 'deceased' ? 'selected' : '' }}>Deceased</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="gender"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Gender</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                    Filter
                </button>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Patient Code</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Phone</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Age</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Gender</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Status</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ ($patients->currentPage() - 1) * $patients->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $patient->patient_code }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">{{ $patient->full_name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $patient->phone }}</td>
                            <td class="px-4 py-3 text-sm">{{ $patient->age_text }}</td>
                            <td class="px-4 py-3 text-sm">{{ $patient->gender_text }}</td>

                            <td class="px-4 py-3 text-center text-sm">
                                <!-- Status Toggle -->
                                @if ($patient->status == 'active' || $patient->status == 'inactive')
                                    <form method="POST" action="{{ route('backend.patients.toggle-status', $patient) }}"
                                        class="inline-flex items-center" id="toggle-form-{{ $patient->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                                        <!-- Toggle Button for Patient Status -->
                                        <button type="button"
                                            onclick="showToggleModal('{{ $patient->id }}', '{{ $patient->status }}', '{{ $patient->full_name }}')"
                                            class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ $patient->status === 'active' ? 'bg-green-500' : 'bg-gray-300' }}">
                                            <span class="sr-only">Toggle status</span>
                                            <span
                                                class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform {{ $patient->status === 'active' ? 'translate-x-6' : 'translate-x-1' }}">
                                            </span>
                                        </button>

                                        <span class="ml-2 text-xs font-medium">
                                            {{ $patient->status == 'active' ? 'Active' : 'Inactive' }}
                                        </span>
                                    </form>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Deceased
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.patients.show', $patient) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                        title="View">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <a href="{{ route('backend.patients.edit', $patient) }}"
                                        class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs"
                                        title="Edit">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <button type="button" data-modal-target="deleteModal"
                                        data-route="{{ route('backend.patients.destroy', $patient) }}"
                                        data-name="{{ $patient->full_name }}"
                                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs"
                                        title="Delete">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Delete',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No patients found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$patients" />
        </div>

    </div>

    <!-- Delete Modal -->
    <x-delete-modal id="deleteModal" title="Delete Patient" message="Are you sure?" :route="null" />

    <!-- Toggle Status Modal -->
    <div id="toggleStatusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-20">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Change Patient Status</h3>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">z
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-center text-gray-700 mb-2">
                    Are you sure you want to <span id="actionText" class="font-semibold"></span> the patient?
                </p>
                <p class="text-center text-gray-600 text-sm">
                    Patient: <span id="patientName" class="font-medium"></span>
                </p>
                <p class="text-center text-gray-600 text-sm">
                    Current Status: <span id="currentStatus" class="font-medium"></span>
                </p>
                <p class="text-center text-gray-600 text-sm">
                    New Status: <span id="newStatus" class="font-medium"></span>
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg flex justify-end space-x-3">
                <button type="button" onclick="closeToggleModal()"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="button" onclick="confirmToggleStatus()"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Confirm Change
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentTogglePatientId = null;
        let currentToggleStatus = null;
        let currentPatientName = null;

        function showToggleModal(patientId, status, patientName) {
            if (status === 'deceased') {
                return;
            }

            currentTogglePatientId = patientId;
            currentToggleStatus = status;
            currentPatientName = patientName;

            const newStatus = status === 'active' ? 'inactive' : 'active';
            const actionText = newStatus === 'active' ? 'activate' : 'deactivate';
            const statusText = status === 'active' ? 'Active' : 'Inactive';
            const newStatusText = newStatus === 'active' ? 'Active' : 'Inactive';

            // Update modal content
            document.getElementById('actionText').textContent = actionText;
            document.getElementById('patientName').textContent = patientName;
            document.getElementById('currentStatus').textContent = statusText;
            document.getElementById('newStatus').textContent = newStatusText;

            // Show modal
            document.getElementById('toggleStatusModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeToggleModal() {
            document.getElementById('toggleStatusModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            currentTogglePatientId = null;
            currentToggleStatus = null;
            currentPatientName = null;
        }

        function confirmToggleStatus() {
            if (currentTogglePatientId) {
                document.getElementById(`toggle-form-${currentTogglePatientId}`).submit();
            }
            closeToggleModal();
        }

        // Close modal when clicking outside
        document.getElementById('toggleStatusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeToggleModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('toggleStatusModal').classList.contains('hidden')) {
                closeToggleModal();
            }
        });
    </script>
@endsection
