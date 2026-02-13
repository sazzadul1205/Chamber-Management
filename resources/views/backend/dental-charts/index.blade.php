@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Dental Charts Management</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.dental-charts.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4 text-white'])
                    <span>New Dental Chart</span>
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
        <form method="GET" action="{{ route('backend.dental-charts.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search patient / code / tooth / condition"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-2">
                <select name="condition"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                    <option value="">All Conditions</option>
                    @foreach (App\Models\DentalChart::conditions() as $key => $value)
                        <option value="{{ $key }}" {{ request('condition') === $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <input type="text" name="tooth_number" value="{{ request('tooth_number') }}" placeholder="Tooth Number"
                    class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
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

            <div class="md:col-span-1">
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full border rounded-md px-2 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-1">
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full border rounded-md px-2 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            </div>

            <div class="md:col-span-1">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium">
                    Filter
                </button>
            </div>

            <div class="md:col-span-1">
                <a href="{{ route('backend.dental-charts.index') }}"
                    class="w-full inline-flex justify-center items-center bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md px-4 py-2 font-medium">
                    Reset
                </a>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Patient</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Tooth</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Condition</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Procedure</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Next Checkup</th>
                        <th class="px-4 py-3 text-left text-sm font-medium">Updated By</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($charts as $chart)
                        <tr class="hover:bg-gray-50 even:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                {{ ($charts->currentPage() - 1) * $charts->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="font-medium">{{ $chart->chart_date->format('d/m/Y') }}</span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('backend.patients.show', $chart->patient_id) }}"
                                    class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                    {{ $chart->patient->full_name ?? '-' }}
                                </a>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="bg-cyan-100 text-cyan-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $chart->tooth_number }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">{{ $chart->tooth_name }}</div>
                            </td>

                            <td class="px-4 py-3 text-sm">{!! $chart->condition_badge !!}</td>

                            <td class="px-4 py-3 text-sm">{{ $chart->procedure_done ? $chart->procedure_done : '-' }}</td>

                            <td class="px-4 py-3 text-sm">
                                {{ $chart->next_checkup ? $chart->next_checkup->format('d/m/Y') : '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm">{{ $chart->updater->full_name ?? '-' }}</td>

                            <td class="px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-1">
                                    @php
                                        $btnBaseClasses =
                                            'relative flex items-center justify-center px-2 py-1 text-white rounded text-xs w-8 h-8 group';
                                    @endphp

                                    <a href="{{ route('backend.dental-charts.show', $chart) }}"
                                        class="{{ $btnBaseClasses }} bg-blue-500 hover:bg-blue-600">
                                        @include('partials.sidebar-icon', ['name' => 'B_View', 'class' => 'w-4 h-4'])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50">
                                            View
                                        </span>
                                    </a>

                                    <a href="{{ route('backend.dental-charts.edit', $chart) }}"
                                        class="{{ $btnBaseClasses }} bg-yellow-400 hover:bg-yellow-500">
                                        @include('partials.sidebar-icon', ['name' => 'B_Edit', 'class' => 'w-4 h-4'])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50">
                                            Edit
                                        </span>
                                    </a>

                                    <button type="button" data-modal-target="deleteModal"
                                        data-route="{{ route('backend.dental-charts.destroy', $chart) }}"
                                        data-name="{{ $chart->patient->full_name ?? 'this record' }}"
                                        class="{{ $btnBaseClasses }} bg-red-600 hover:bg-red-700">
                                        @include('partials.sidebar-icon', ['name' => 'B_Delete', 'class' => 'w-4 h-4'])
                                        <span
                                            class="absolute bottom-full mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-50">
                                            Delete
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500 text-sm">
                                No dental chart records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            <x-pagination :paginator="$charts" />
        </div>

    </div>

    <x-delete-modal id="deleteModal" title="Delete Dental Chart"
        message="Are you sure you want to delete this dental chart record?" :route="null" />
@endsection
