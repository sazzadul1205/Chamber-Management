@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Treatments</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.treatments.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    New Treatment
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                    class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    @foreach(App\Models\Treatment::statuses() as $key => $value)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="patient_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Patients</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->patient_code }} - {{ $patient->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <select name="doctor_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->user->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
                    Filter
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">Code</th>
                        <th class="px-3 py-2 text-left text-sm">Patient</th>
                        <th class="px-3 py-2 text-left text-sm">Doctor</th>
                        <th class="px-3 py-2 text-left text-sm">Diagnosis</th>
                        <th class="px-3 py-2 text-left text-sm">Sessions</th>
                        <th class="px-3 py-2 text-left text-sm">Cost</th>
                        <th class="px-3 py-2 text-left text-sm">Status</th>
                        <th class="px-3 py-2 text-left text-sm">Date</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($treatments as $treatment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs rounded bg-cyan-100 text-cyan-800">
                                    {{ $treatment->treatment_code }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <a href="{{ route('backend.patients.show', $treatment->patient_id) }}"
                                    class="text-blue-600 hover:underline">
                                    {{ $treatment->patient->full_name }}
                                </a>
                            </td>
                            <td class="px-3 py-2">{{ $treatment->doctor->user->full_name ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <div class="text-sm text-gray-800">{{ Str::limit($treatment->diagnosis, 50) }}</div>
                                @if($treatment->appointment)
                                    <div class="text-xs text-gray-500">
                                        Appt: {{ $treatment->appointment->appointment_code }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="text-sm font-medium">{{ $treatment->session_progress_text }}</div>
                                <div class="mt-1">{!! $treatment->progress_bar !!}</div>
                            </td>
                            <td class="px-3 py-2">
                                <div class="text-sm font-medium">{{ $treatment->formatted_estimated_cost }}</div>
                                @if($treatment->total_actual_cost)
                                    <div class="text-xs text-gray-500">
                                        Actual: {{ $treatment->formatted_actual_cost }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                            @if($treatment->status == 'planned') bg-blue-100 text-blue-800
                                            @elseif($treatment->status == 'in_progress') bg-yellow-100 text-yellow-800
                                            @elseif($treatment->status == 'completed') bg-green-100 text-green-800
                                            @elseif($treatment->status == 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                    {{ $treatment->status_text }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-600">
                                {{ $treatment->treatment_date->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button type="button"
                                    class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs"
                                    data-modal-toggle="treatment-actions-modal-{{ $treatment->id }}">
                                    Actions
                                </button>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div id="treatment-actions-modal-{{ $treatment->id }}"
                            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
                                <h3 class="text-lg font-semibold mb-4">Treatment Actions</h3>

                                <div class="flex flex-col gap-2">
                                    @if($treatment->status == 'planned')
                                        <form method="POST" action="{{ route('backend.treatments.start', $treatment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                                                Start Treatment
                                            </button>
                                        </form>
                                    @endif

                                    @if($treatment->status == 'in_progress' && $treatment->canAddSession())
                                        <form method="POST" action="{{ route('backend.treatments.add-session', $treatment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded">
                                                Add Session
                                            </button>
                                        </form>
                                    @endif

                                    @if($treatment->status == 'in_progress')
                                        <form method="POST" action="{{ route('backend.treatments.complete', $treatment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
                                                Complete Treatment
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($treatment->status, ['planned', 'in_progress']))
                                        <form method="POST" action="{{ route('backend.treatments.hold', $treatment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded">
                                                Put on Hold
                                            </button>
                                        </form>
                                    @endif

                                    @if($treatment->status == 'on_hold')
                                        <form method="POST" action="{{ route('backend.treatments.resume', $treatment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                                                Resume Treatment
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($treatment->status, ['planned', 'in_progress', 'on_hold']))
                                        <form method="POST" action="{{ route('backend.treatments.cancel', $treatment) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                                                Cancel Treatment
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('backend.treatment-procedures.create-for-treatment', $treatment) }}"
                                        class="w-full block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-center">
                                        Add Procedure
                                    </a>

                                    @if(!$treatment->procedures()->exists() && !$treatment->invoices()->exists())
                                        <form method="POST" action="{{ route('backend.treatments.destroy', $treatment) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
                                                Delete Treatment
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="flex justify-end mt-4">
                                    <button type="button" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                        data-modal-hide="treatment-actions-modal-{{ $treatment->id }}">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No treatments found.
                                <a href="{{ route('backend.treatments.create') }}" class="text-blue-600 hover:underline ml-1">
                                    Create your first treatment
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Showing {{ $treatments->firstItem() ?? 0 }} to {{ $treatments->lastItem() ?? 0 }} of
                {{ $treatments->total() }} treatments
            </div>
            <div>
                {{ $treatments->links() }}
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('button[data-modal-toggle]').forEach(btn => {
                const modalId = btn.getAttribute('data-modal-toggle');
                const modal = document.getElementById(modalId);

                btn.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                });
            });

            document.querySelectorAll('button[data-modal-hide]').forEach(btn => {
                const modalId = btn.getAttribute('data-modal-hide');
                const modal = document.getElementById(modalId);

                btn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            });
        });
    </script>

@endsection