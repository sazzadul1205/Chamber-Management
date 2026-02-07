@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Treatments Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.treatments.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4 text-white'])
                    <span>New Treatment</span>
                </a>
            </div>
        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded border border-green-300 mb-2">
                <div class="flex items-center gap-2">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Tick',
                        'class' => 'w-4 h-4 text-green-600',
                    ])
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded border border-red-300 mb-2">
                <div class="flex items-center gap-2">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Cross',
                        'class' => 'w-4 h-4 text-red-600',
                    ])
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- FILTERS -->
        <form method="GET" action="{{ route('backend.treatments.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        @include('partials.sidebar-icon', [
                            'name' => 'Search',
                            'class' => 'w-4 h-4 text-gray-400',
                        ])
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search patient / code / diagnosis"
                        class="pl-10 w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                </div>
            </div>

            <div class="md:col-span-2">
                <select name="status"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Status</option>
                    @foreach (App\Models\Treatment::statuses() as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="patient_id"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Patients</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->patient_code }} - {{ $patient->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <select name="doctor_id"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Doctors</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->user->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex gap-2">
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium flex items-center justify-center gap-2">
                    @include('partials.sidebar-icon', ['name' => 'Filter', 'class' => 'w-4 h-4'])
                    <span>Filter</span>
                </button>
                @if (request()->hasAny(['search', 'status', 'patient_id', 'doctor_id']))
                    <a href="{{ route('backend.treatments.index') }}"
                        class="flex items-center justify-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium">
                        @include('partials.sidebar-icon', ['name' => 'B_Cross', 'class' => 'w-4 h-4'])
                    </a>
                @endif
            </div>
        </form>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Treatments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCount ?? $treatments->total() }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        @include('partials.sidebar-icon', [
                            'name' => 'Treatment',
                            'class' => 'w-6 h-6 text-blue-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">In Progress</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $inProgressCount ?? '0' }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Progress',
                            'class' => 'w-6 h-6 text-yellow-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Completed</p>
                        <p class="text-2xl font-bold text-green-600">{{ $completedCount ?? '0' }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        @include('partials.sidebar-icon', [
                            'name' => 'B_Tick',
                            'class' => 'w-6 h-6 text-green-600',
                        ])
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $totalRevenue ? '₹' . number_format($totalRevenue, 2) : '₹0.00' }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        @include('partials.sidebar-icon', [
                            'name' => 'Finance',
                            'class' => 'w-6 h-6 text-green-600',
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200 mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($treatments as $treatment)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($treatments->currentPage() - 1) * $treatments->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'Treatment',
                                        'class' => 'w-3 h-3 mr-1',
                                    ])
                                    {{ $treatment->treatment_code }}
                                </span>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <a href="{{ route('backend.patients.show', $treatment->patient_id) }}"
                                            class="text-sm font-medium text-blue-600 hover:text-blue-900 hover:underline">
                                            {{ $treatment->patient->full_name }}
                                        </a>
                                        @if ($treatment->appointment)
                                            <div class="text-xs text-gray-500 mt-1">
                                                <span class="inline-flex items-center gap-1">
                                                    @include('partials.sidebar-icon', [
                                                        'name' => 'Appointment',
                                                        'class' => 'w-3 h-3',
                                                    ])
                                                    {{ $treatment->appointment->appointment_code }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ substr($treatment->doctor->user->full_name ?? 'D', 0, 1) }}
                                        </span>
                                    </div>
                                    {{ $treatment->doctor->user->full_name ?? '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $treatment->diagnosis }}">
                                    {{ Str::limit($treatment->diagnosis, 50) }}
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $treatment->session_progress_text }}
                                </div>
                                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full"
                                        style="width: {{ $treatment->session_progress_percentage }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $treatment->completed_sessions_count }} of {{ $treatment->total_sessions_count }}
                                    sessions
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $treatment->formatted_estimated_cost }}
                                </div>
                                @if ($treatment->total_actual_cost)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Actual: {{ $treatment->formatted_actual_cost }}
                                    </div>
                                @endif
                                @if ($treatment->payment_status)
                                    <div class="mt-1">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full 
                                            {{ $treatment->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($treatment->payment_status) }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $treatment->status_badge_class }}">
                                        {{ $treatment->status_text }}
                                    </span>
                                    <div class="text-xs text-gray-500">
                                        {{ $treatment->treatment_date->format('d/m/Y') }}
                                    </div>
                                    @if ($treatment->next_session_date)
                                        <div class="text-xs text-blue-600 font-medium">
                                            Next: {{ $treatment->next_session_date->format('d/m') }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-1">
                                    @php
                                        $btnBaseClasses =
                                            'relative flex items-center justify-center p-2 rounded-md transition-colors duration-200 group';
                                    @endphp

                                    <!-- View -->
                                    <a href="{{ route('backend.treatments.show', $treatment) }}"
                                        class="{{ $btnBaseClasses }} bg-blue-50 hover:bg-blue-100 text-blue-600"
                                        data-tooltip="View Treatment">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span class="tooltip">View Treatment</span>
                                    </a>

                                    <!-- Sessions -->
                                    <a href="{{ route('backend.treatment-sessions.index', ['treatment_id' => $treatment->id]) }}"
                                        class="{{ $btnBaseClasses }} bg-purple-50 hover:bg-purple-100 text-purple-600"
                                        data-tooltip="View Sessions">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'Treatment_Session',
                                            'class' => 'w-4 h-4',
                                        ])
                                        <span class="tooltip">View Sessions</span>
                                    </a>

                                    @if ($treatment->status != 'completed')
                                        <!-- Add Session -->
                                        <a href="{{ route('backend.treatment-sessions.create', ['treatment_id' => $treatment->id]) }}"
                                            class="{{ $btnBaseClasses }} bg-green-50 hover:bg-green-100 text-green-600"
                                            data-tooltip="Add Session">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_Add',
                                                'class' => 'w-4 h-4',
                                            ])
                                            <span class="tooltip">Add Session</span>
                                        </a>

                                        <!-- Complete -->
                                        @if ($treatment->canBeCompleted())
                                            <form method="POST"
                                                action="{{ route('backend.treatments.complete', $treatment) }}"
                                                class="inline" onsubmit="return confirmComplete()">
                                                @csrf
                                                <button type="submit"
                                                    class="{{ $btnBaseClasses }} bg-green-50 hover:bg-green-100 text-green-600"
                                                    data-tooltip="Complete Treatment">
                                                    @include('partials.sidebar-icon', [
                                                        'name' => 'B_Tick',
                                                        'class' => 'w-4 h-4',
                                                    ])
                                                    <span class="tooltip">Complete Treatment</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Cancel -->
                                        <form method="POST"
                                            action="{{ route('backend.treatments.cancel', $treatment) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button"
                                                onclick="showCancelModal({{ $treatment->id }}, '{{ $treatment->treatment_code }}')"
                                                class="{{ $btnBaseClasses }} bg-red-50 hover:bg-red-100 text-red-600"
                                                data-tooltip="Cancel Treatment">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Cancel',
                                                    'class' => 'w-4 h-4',
                                                ])
                                                <span class="tooltip">Cancel Treatment</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- More Actions Dropdown -->
                                    <div class="relative inline-block">
                                        <button type="button"
                                            class="{{ $btnBaseClasses }} bg-gray-50 hover:bg-gray-100 text-gray-600"
                                            onclick="toggleDropdown({{ $treatment->id }})">
                                            @include('partials.sidebar-icon', [
                                                'name' => 'B_More',
                                                'class' => 'w-4 h-4',
                                            ])
                                        </button>
                                        <div id="dropdown-{{ $treatment->id }}"
                                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                            <div class="py-1">
                                                <a href="{{ route('backend.treatments.edit', $treatment) }}"
                                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    @include('partials.sidebar-icon', [
                                                        'name' => 'B_Edit',
                                                        'class' => 'w-4 h-4 mr-2',
                                                    ])
                                                    Edit Treatment
                                                </a>
                                                <a href="{{ route('backend.treatments.notes', $treatment) }}"
                                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    @include('partials.sidebar-icon', [
                                                        'name' => 'B_Note',
                                                        'class' => 'w-4 h-4 mr-2',
                                                    ])
                                                    View Notes
                                                </a>
                                                <a href="{{ route('backend.treatments.print', $treatment) }}"
                                                    target="_blank"
                                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    @include('partials.sidebar-icon', [
                                                        'name' => 'B_Print',
                                                        'class' => 'w-4 h-4 mr-2',
                                                    ])
                                                    Print Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'Treatment',
                                        'class' => 'w-16 h-16 text-gray-400 mb-4',
                                    ])
                                    <p class="text-gray-500 text-lg font-medium mb-2">No treatments found</p>
                                    <p class="text-gray-400 text-sm mb-4">Try adjusting your filters or create a new
                                        treatment</p>
                                    <a href="{{ route('backend.treatments.create') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Add',
                                            'class' => 'w-4 h-4',
                                        ])
                                        Create Treatment
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$treatments" />
        </div>

    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Cancel Treatment</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-700 mb-4">Are you sure you want to cancel treatment <span id="cancelTreatmentCode"
                        class="font-semibold"></span>?</p>
                <form id="cancelForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Cancellation
                        </label>
                        <textarea name="cancellation_reason" id="cancellation_reason" rows="3"
                            class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Optional reason for cancellation"></textarea>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex justify-end space-x-3">
                <button type="button" onclick="closeCancelModal()"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="button" onclick="submitCancelForm()"
                    class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cancel Treatment
                </button>
            </div>
        </div>
    </div>

    <style>
        .tooltip {
            @apply absolute bottom-full mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50;
            left: 50%;
            transform: translateX(-50%);
        }

        .tooltip::after {
            content: '';
            @apply absolute top-full left-1/2 -translate-x-1/2;
            border-width: 4px;
            border-style: solid;
            border-color: #111827 transparent transparent transparent;
        }
    </style>

    <script>
        // Tooltip functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('[onclick*="toggleDropdown"]')) {
                    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                        dropdown.classList.add('hidden');
                    });
                }
            });
        });

        function toggleDropdown(treatmentId) {
            const dropdown = document.getElementById(`dropdown-${treatmentId}`);
            const isHidden = dropdown.classList.contains('hidden');

            // Close all other dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                d.classList.add('hidden');
            });

            // Toggle current dropdown
            if (isHidden) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function confirmComplete() {
            return confirm('Are you sure you want to mark this treatment as completed? This action cannot be undone.');
        }

        // Cancel Modal Functions
        function showCancelModal(treatmentId, treatmentCode) {
            document.getElementById('cancelTreatmentCode').textContent = treatmentCode;
            document.getElementById('cancelForm').action = `/backend/treatments/${treatmentId}/cancel`;
            document.getElementById('cancelModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('cancellation_reason').value = '';
        }

        function submitCancelForm() {
            document.getElementById('cancelForm').submit();
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCancelModal();
            }
        });

        // Export functionality
        function exportTreatments(format) {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route('backend.treatments.export') }}';

            const formatInput = document.createElement('input');
            formatInput.type = 'hidden';
            formatInput.name = 'format';
            formatInput.value = format;
            form.appendChild(formatInput);

            // Add current filter parameters
            const filters = ['search', 'status', 'patient_id', 'doctor_id'];
            filters.forEach(filter => {
                const value = new URLSearchParams(window.location.search).get(filter);
                if (value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = filter;
                    input.value = value;
                    form.appendChild(input);
                }
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
@endsection
