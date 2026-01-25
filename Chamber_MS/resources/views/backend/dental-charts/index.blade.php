@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Dental Charts</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.dental-charts.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    <span>Add Record</span>
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.dental-charts.index') }}"
            class="grid grid-cols-1 md:grid-cols-8 gap-3 mt-4">
            <div class="md:col-span-3">
                <select name="patient_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Patients</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->patient_code }} - {{ $patient->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <input type="text" name="tooth_number" value="{{ request('tooth_number') }}" placeholder="Tooth #"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <select name="condition" class="w-full border rounded px-3 py-2">
                    <option value="">All Conditions</option>
                    @foreach (App\Models\DentalChart::conditions() as $key => $value)
                        <option value="{{ $key }}" {{ request('condition') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-2">
                    Filter
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm">Date</th>
                        <th class="px-3 py-2 text-left text-sm">Patient</th>
                        <th class="px-3 py-2 text-left text-sm">Tooth</th>
                        <th class="px-3 py-2 text-left text-sm">Condition</th>
                        <th class="px-3 py-2 text-left text-sm">Procedure</th>
                        <th class="px-3 py-2 text-left text-sm">Next Checkup</th>
                        <th class="px-3 py-2 text-left text-sm">Updated By</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($charts as $chart)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $chart->chart_date->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route('backend.patients.show', $chart->patient_id) }}"
                                    class="text-blue-600 hover:underline">
                                    {{ $chart->patient->full_name }}
                                </a>
                            </td>
                            <td class="px-3 py-2">
                                <span
                                    class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $chart->tooth_number }}</span>
                                <small class="text-gray-500">{{ $chart->tooth_name }}</small>
                            </td>
                            <td class="px-3 py-2">{!! $chart->condition_badge !!}</td>
                            <td class="px-3 py-2">{{ $chart->procedure_done ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $chart->next_checkup ? $chart->next_checkup->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2">{{ $chart->updater->full_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.dental-charts.show', $chart) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>
                                    <a href="{{ route('backend.dental-charts.edit', $chart) }}"
                                        class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>
                                    <button type="button" data-modal-target="deleteModal"
                                        data-route="{{ route('backend.dental-charts.destroy', $chart) }}"
                                        data-name="{{ $chart->patient->full_name }}"
                                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
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
                            <td colspan="8" class="px-3 py-6 text-center text-gray-500">No dental chart records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$charts" class="mt-3" />

    </div>

    <!-- Delete Modal Component -->
    <x-delete-modal id="deleteModal" title="Delete Dental Chart" message="Are you sure?" :route="null" />
@endsection
