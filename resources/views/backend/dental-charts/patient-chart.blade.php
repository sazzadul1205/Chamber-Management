@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold">
                Dental Chart: {{ $patient->full_name }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('backend.dental-charts.create', ['patient_id' => $patient->id]) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Plus', 'class' => 'w-4 h-4'])
                    Add Record
                </a>

                <a href="{{ route('backend.patients.show', $patient->id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Patient
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Tooth Chart -->
            <div class="lg:col-span-2 bg-white rounded shadow p-6 space-y-6">

                <!-- Upper Jaw -->
                <div>
                    <h3 class="text-lg font-semibold text-center mb-4">Upper Jaw (Maxillary)</h3>

                    <div class="grid grid-cols-8 gap-3 justify-items-center">
                        @foreach (App\Models\DentalChart::adultTeeth() as $tooth)
                            @if ($tooth >= 11 && $tooth <= 28)
                                @php
                                    $chart = $charts->firstWhere('tooth_number', $tooth);
                                    $color = $chart?->condition_color ?? 'gray';
                                    $condition = $chart?->condition_text ?? 'No Record';
                                @endphp

                                <a href="{{ $chart ? route('backend.dental-charts.show', $chart) : '#' }}"
                                    title="Tooth {{ $tooth }}: {{ $condition }}" class="group text-center">
                                    <div class="text-xs font-semibold mb-1">{{ $tooth }}</div>
                                    <div
                                        class="w-8 h-8 rounded-full border border-gray-300
                                            bg-{{ $color }}-500
                                            group-hover:scale-110 transition">
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Lower Jaw -->
                <div>
                    <h3 class="text-lg font-semibold text-center mb-4">Lower Jaw (Mandibular)</h3>

                    <div class="grid grid-cols-8 gap-3 justify-items-center">
                        @foreach (App\Models\DentalChart::adultTeeth() as $tooth)
                            @if ($tooth >= 31 && $tooth <= 48)
                                @php
                                    $chart = $charts->firstWhere('tooth_number', $tooth);
                                    $color = $chart?->condition_color ?? 'gray';
                                    $condition = $chart?->condition_text ?? 'No Record';
                                @endphp

                                <a href="{{ $chart ? route('backend.dental-charts.show', $chart) : '#' }}"
                                    title="Tooth {{ $tooth }}: {{ $condition }}" class="group text-center">
                                    <div
                                        class="w-8 h-8 rounded-full border border-gray-300
                                            bg-{{ $color }}-500
                                            group-hover:scale-110 transition">
                                    </div>
                                    <div class="text-xs font-semibold mt-1">{{ $tooth }}</div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Legend -->
                <div>
                    <h4 class="text-sm font-semibold mb-2">Condition Legend</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach (App\Models\DentalChart::conditions() as $key => $label)
                            @php
                                $tmp = new App\Models\DentalChart();
                                $tmp->condition = $key;
                            @endphp
                            <span
                                class="px-2 py-1 rounded text-xs font-medium text-white bg-{{ $tmp->condition_color }}-500">
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Records -->
            <div class="bg-white rounded shadow p-4">
                <h3 class="text-lg font-semibold mb-4">Recent Records</h3>

                @forelse($charts->take(10) as $chart)
                    <a href="{{ route('backend.dental-charts.show', $chart) }}"
                        class="block border rounded p-3 mb-3 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-center mb-1">
                            <div class="font-medium">
                                Tooth {{ $chart->tooth_number }}
                                <span
                                    class="ml-2 px-2 py-0.5 text-xs rounded bg-{{ $chart->condition_color }}-500 text-white">
                                    {{ $chart->condition_text }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $chart->chart_date->format('d/m/Y') }}
                            </span>
                        </div>

                        @if ($chart->procedure_done)
                            <div class="text-sm text-gray-600">
                                Procedure: {{ $chart->procedure_done }}
                            </div>
                        @endif

                        @if ($chart->remarks)
                            <div class="text-xs text-gray-500">
                                {{ Str::limit($chart->remarks, 60) }}
                            </div>
                        @endif
                    </a>
                @empty
                    <p class="text-sm text-gray-500 text-center">
                        No dental records found.
                    </p>
                @endforelse
            </div>

        </div>
    </div>
@endsection
