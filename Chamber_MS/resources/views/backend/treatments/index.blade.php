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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient / code"
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

        <!-- Treatments Table -->
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
                            <td class="px-3 py-2 text-center flex justify-center gap-1">
                                <!-- Show -->
                                <a href="{{ route('backend.treatments.show', $treatment) }}"
                                    class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                    title="View Treatment">
                                    @include('partials.sidebar-icon', ['name' => 'B_View', 'class' => 'w-4 h-4'])
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('backend.treatments.edit', $treatment) }}"
                                    class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs"
                                    title="Edit Treatment">
                                    @include('partials.sidebar-icon', ['name' => 'B_Edit', 'class' => 'w-4 h-4'])
                                </a>

                                <!-- Complete (if not completed) -->
                                @if($treatment->status != 'completed')
                                    <form method="POST" action="{{ route('backend.treatments.complete', $treatment) }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center bg-green-600 hover:bg-green-700 text-white rounded text-xs"
                                            title="Mark as Completed">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </td>

                        </tr>
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
        <x-pagination :paginator="$treatments" />
    </div>

@endsection