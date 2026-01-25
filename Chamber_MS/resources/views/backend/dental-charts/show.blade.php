@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Dental Chart Record</h2>

            <div class="flex gap-2">
                <a href="{{ route('backend.dental-charts.edit', $dentalChart->id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Edit', 'class' => 'w-4 h-4'])
                    Edit
                </a>

                <a href="{{ route('backend.dental-charts.patient-chart', $dentalChart->patient_id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_User', 'class' => 'w-4 h-4'])
                    Patient Chart
                </a>

                <a href="{{ route('backend.dental-charts.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <!-- Patient Summary -->
        <div class="p-4 bg-blue-50 rounded border">
            <p><strong>Patient:</strong> {{ $dentalChart->patient->full_name ?? 'N/A' }}</p>
            <p><strong>Patient Code:</strong> {{ $dentalChart->patient->patient_code ?? 'N/A' }}</p>
        </div>

        <!-- Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Left Column -->
            <div class="bg-white rounded shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 w-40">Chart Date</th>
                            <td class="px-4 py-3">{{ $dentalChart->chart_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Tooth</th>
                            <td class="px-4 py-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                    {{ $dentalChart->tooth_number }}
                                </span>
                                <span class="text-gray-500 text-sm">({{ $dentalChart->tooth_name }})</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Surface</th>
                            <td class="px-4 py-3">{{ $dentalChart->surface_text ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Condition</th>
                            <td class="px-4 py-3">{!! $dentalChart->condition_badge !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Right Column -->
            <div class="bg-white rounded shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 w-40">Procedure Done</th>
                            <td class="px-4 py-3">{{ $dentalChart->procedure_done ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Next Checkup</th>
                            <td class="px-4 py-3">
                                {{ $dentalChart->next_checkup ? $dentalChart->next_checkup->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Updated By</th>
                            <td class="px-4 py-3">{{ $dentalChart->updater->full_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Created</th>
                            <td class="px-4 py-3">{{ $dentalChart->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Last Updated</th>
                            <td class="px-4 py-3">{{ $dentalChart->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Remarks -->
        @if ($dentalChart->remarks)
            <div class="bg-white rounded shadow">
                <div class="px-4 py-3 border-b">
                    <h3 class="text-lg font-semibold">Remarks</h3>
                </div>
                <div class="px-4 py-4 text-gray-700">
                    {{ $dentalChart->remarks }}
                </div>
            </div>
        @endif

    </div>
@endsection
