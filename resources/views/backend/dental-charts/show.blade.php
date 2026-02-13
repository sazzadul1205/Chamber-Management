@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                Dental Chart Details: Tooth {{ $dentalChart->tooth_number }}
            </h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.dental-charts.edit', $dentalChart->id) }}"
                    class="flex justify-center items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition px-4 py-2 w-40">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Edit',
                        'class' => 'w-4 h-4',
                    ])
                    <span class="text-center flex-1">Edit Record</span>
                </a>

                <a href="{{ route('backend.dental-charts.patient-chart', $dentalChart->patient_id) }}"
                    class="flex justify-center items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow transition px-4 py-2 w-40">
                    @include('partials.sidebar-icon', [
                        'name' => 'Patient',
                        'class' => 'w-4 h-4',
                    ])
                    <span class="text-center flex-1">Patient Chart</span>
                </a>

                <a href="{{ route('backend.dental-charts.index') }}"
                    class="flex justify-center items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition px-4 py-2 w-40">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Back',
                        'class' => 'w-4 h-4',
                    ])
                    <span class="text-center flex-1">Back to List</span>
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Dental Chart Info Card -->
            <div class="bg-white border rounded-xl shadow p-6 space-y-4">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-cyan-100 flex items-center justify-center mb-3">
                        @include('partials.sidebar-icon', [
                            'name' => 'Tooth',
                            'class' => 'w-8 h-8 text-cyan-600',
                        ])
                    </div>

                    <h3 class="text-xl font-semibold">Tooth {{ $dentalChart->tooth_number }}</h3>

                    <div class="flex items-center justify-center gap-2 mt-2">
                        {!! $dentalChart->condition_badge !!}
                    </div>
                </div>

                <table class="w-full text-sm text-gray-700 mt-4">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="py-2 text-left font-medium w-1/3">Chart Date:</th>
                            <td>{{ $dentalChart->chart_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Tooth Name:</th>
                            <td>{{ $dentalChart->tooth_name }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Surface:</th>
                            <td>{{ $dentalChart->surface_text ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Condition:</th>
                            <td>{{ $dentalChart->condition_text }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Procedure:</th>
                            <td>{{ $dentalChart->procedure_done ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 text-left font-medium">Next Checkup:</th>
                            <td>{{ $dentalChart->next_checkup ? $dentalChart->next_checkup->format('d/m/Y') : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Patient Information -->
            <div class="space-y-6">
                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        @include('partials.sidebar-icon', [
                            'name' => 'Patient',
                            'class' => 'w-5 h-5 text-blue-600',
                        ])
                        Patient Information
                    </h4>

                    @if ($dentalChart->patient)
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-semibold">
                                    {{ substr($dentalChart->patient->full_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <a href="{{ route('backend.patients.show', $dentalChart->patient_id) }}"
                                    class="font-semibold text-gray-800 hover:text-blue-600 transition block">
                                    {{ $dentalChart->patient->full_name }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $dentalChart->patient->patient_code }}</p>
                            </div>
                        </div>

                        <table class="w-full text-sm text-gray-700">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <th class="py-1 text-left font-medium w-1/2">Phone:</th>
                                    <td>{{ $dentalChart->patient->phone ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="py-1 text-left font-medium">Email:</th>
                                    <td>{{ $dentalChart->patient->email ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="py-1 text-left font-medium">Gender:</th>
                                    <td>{{ $dentalChart->patient->gender_text ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="py-1 text-left font-medium">Age:</th>
                                    <td>{{ $dentalChart->patient->age_text ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-400">Patient information not available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline / Meta -->
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-500 text-white rounded-xl shadow p-4 text-center">
                        <h6 class="text-xs font-medium">Tooth</h6>
                        <h3 class="text-xl font-bold">{{ $dentalChart->tooth_number }}</h3>
                    </div>
                    <div class="bg-green-500 text-white rounded-xl shadow p-4 text-center">
                        <h6 class="text-xs font-medium">Condition</h6>
                        <h3 class="text-sm font-bold leading-tight">{{ $dentalChart->condition_text }}</h3>
                    </div>
                </div>

                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4">Record Timeline</h4>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <span>1</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-800">Created</span>
                                    <span class="text-xs text-gray-500">{{ $dentalChart->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <span>2</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-800">Last Updated</span>
                                    <span class="text-xs text-gray-500">{{ $dentalChart->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <span>3</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-800">Next Checkup</span>
                                    <span class="text-xs text-gray-500">
                                        {{ $dentalChart->next_checkup ? $dentalChart->next_checkup->format('d/m/Y') : 'Not set' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold mb-4">Updated By</h4>
                    <p class="text-gray-700">{{ $dentalChart->updater->full_name ?? '-' }}</p>
                </div>
            </div>
        </div>

        @if ($dentalChart->remarks)
            <div class="bg-white border rounded-xl shadow p-6">
                <h4 class="text-lg font-semibold mb-2">Remarks</h4>
                <p class="text-gray-700 whitespace-pre-line">{{ $dentalChart->remarks }}</p>
            </div>
        @endif
    </div>
@endsection
