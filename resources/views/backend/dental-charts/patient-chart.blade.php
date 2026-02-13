@extends('backend.layout.structure')

@section('content')
    @php
        $totalRecords = $charts->count();
        $treatedTeeth = $charts->pluck('tooth_number')->unique()->count();
        $upcomingCheckups = $charts->filter(
            fn($chart) => $chart->next_checkup && $chart->next_checkup->isFuture(),
        )->count();

        $upperTeeth = collect(App\Models\DentalChart::adultTeeth())->filter(
            fn($tooth) => (int) $tooth >= 11 && (int) $tooth <= 28,
        );
        $lowerTeeth = collect(App\Models\DentalChart::adultTeeth())->filter(
            fn($tooth) => (int) $tooth >= 31 && (int) $tooth <= 48,
        );

        $conditionColorMap = [
            'healthy' => 'green',
            'caries' => 'red',
            'filling' => 'blue',
            'crown' => 'purple',
            'bridge' => 'indigo',
            'implant' => 'cyan',
            'missing' => 'gray',
            'extracted' => 'gray',
            'root_canal' => 'yellow',
            'fractured' => 'red',
            'discolored' => 'yellow',
            'hypoplastic' => 'orange',
            'impacted' => 'pink',
            'partially_erupted' => 'amber',
        ];
    @endphp

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Patient Dental Chart</h1>
                <p class="text-gray-600 mt-1">
                    {{ $patient->full_name }} • {{ $patient->patient_code }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.dental-charts.create', ['patient_id' => $patient->id]) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    Add Record
                </a>
                <a href="{{ route('backend.patients.show', $patient->id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Back', 'class' => 'w-4 h-4'])
                    Back to Patient
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow text-white p-5">
                <p class="text-sm opacity-90">Total Records</p>
                <p class="text-3xl font-bold mt-1">{{ $totalRecords }}</p>
            </div>
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow text-white p-5">
                <p class="text-sm opacity-90">Treated Teeth</p>
                <p class="text-3xl font-bold mt-1">{{ $treatedTeeth }}</p>
            </div>
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl shadow text-white p-5">
                <p class="text-sm opacity-90">Upcoming Checkups</p>
                <p class="text-3xl font-bold mt-1">{{ $upcomingCheckups }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Tooth Map -->
            <div class="xl:col-span-2 bg-white rounded-xl shadow border p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">Tooth Map</h3>
                    <span class="text-sm text-gray-500">Click a tooth with record to open details</span>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 border">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 text-center">Upper Jaw (Maxillary)</h4>
                    <div class="grid grid-cols-8 gap-3 justify-items-center">
                        @foreach ($upperTeeth as $tooth)
                            @php
                                $chart = $charts->firstWhere('tooth_number', $tooth);
                                $color = $chart?->condition_color ?? 'gray';
                                $condition = $chart?->condition_text ?? 'No Record';
                                $toothName = App\Models\DentalChart::toothNames()[$tooth] ?? "Tooth {$tooth}";
                            @endphp

                            <a href="{{ $chart ? route('backend.dental-charts.show', $chart) : '#' }}"
                                title="Tooth {{ $tooth }} - {{ $toothName }}: {{ $condition }}"
                                class="group text-center {{ $chart ? '' : 'pointer-events-none' }}">
                                <div class="text-xs font-semibold text-gray-700 mb-1">{{ $tooth }}</div>
                                <div
                                    class="w-9 h-9 rounded-full border-2 border-white shadow
                                    bg-{{ $color }}-500 {{ $chart ? 'group-hover:scale-110' : 'opacity-40' }} transition-transform duration-150">
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 border">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 text-center">Lower Jaw (Mandibular)</h4>
                    <div class="grid grid-cols-8 gap-3 justify-items-center">
                        @foreach ($lowerTeeth as $tooth)
                            @php
                                $chart = $charts->firstWhere('tooth_number', $tooth);
                                $color = $chart?->condition_color ?? 'gray';
                                $condition = $chart?->condition_text ?? 'No Record';
                                $toothName = App\Models\DentalChart::toothNames()[$tooth] ?? "Tooth {$tooth}";
                            @endphp

                            <a href="{{ $chart ? route('backend.dental-charts.show', $chart) : '#' }}"
                                title="Tooth {{ $tooth }} - {{ $toothName }}: {{ $condition }}"
                                class="group text-center {{ $chart ? '' : 'pointer-events-none' }}">
                                <div
                                    class="w-9 h-9 rounded-full border-2 border-white shadow
                                    bg-{{ $color }}-500 {{ $chart ? 'group-hover:scale-110' : 'opacity-40' }} transition-transform duration-150">
                                </div>
                                <div class="text-xs font-semibold text-gray-700 mt-1">{{ $tooth }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Condition Legend</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach (App\Models\DentalChart::conditions() as $key => $label)
                            @php
                                $color = $conditionColorMap[$key] ?? 'gray';
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium text-white bg-{{ $color }}-500">
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Records -->
            <div class="bg-white rounded-xl shadow border p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Records</h3>
                    <span class="text-xs text-gray-500">Latest {{ min(10, $charts->count()) }}</span>
                </div>

                <div class="mb-3">
                    <input id="recordSearch" type="text" placeholder="Search tooth or condition..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div id="recordList" class="space-y-3 max-h-[640px] overflow-y-auto pr-1">
                    @forelse($charts->take(10) as $chart)
                        <a href="{{ route('backend.dental-charts.show', $chart) }}"
                            class="record-item block border rounded-lg p-3 hover:bg-gray-50 transition"
                            data-search="{{ strtolower($chart->tooth_number . ' ' . $chart->condition_text . ' ' . ($chart->procedure_done ?? '')) }}">
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <div class="font-semibold text-gray-800">
                                        Tooth {{ $chart->tooth_number }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ App\Models\DentalChart::toothNames()[$chart->tooth_number] ?? "Tooth {$chart->tooth_number}" }}
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $chart->chart_date->format('d/m/Y') }}</span>
                            </div>

                            <div class="mt-2 flex items-center gap-2">
                                <span
                                    class="px-2 py-0.5 text-xs rounded-full bg-{{ $chart->condition_color }}-500 text-white">
                                    {{ $chart->condition_text }}
                                </span>
                                @if ($chart->procedure_done)
                                    <span class="text-xs text-gray-600">Procedure: {{ $chart->procedure_done }}</span>
                                @endif
                            </div>

                            @if ($chart->remarks)
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ \Illuminate\Support\Str::limit($chart->remarks, 80) }}
                                </p>
                            @endif
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-6">
                            No dental records found.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('recordSearch');
            const items = document.querySelectorAll('.record-item');

            if (!searchInput || !items.length) return;

            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();

                items.forEach(item => {
                    const haystack = item.dataset.search || '';
                    item.style.display = haystack.includes(query) ? '' : 'none';
                });
            });
        });
    </script>
@endsection
