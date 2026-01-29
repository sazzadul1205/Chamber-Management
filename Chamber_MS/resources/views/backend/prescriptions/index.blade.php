@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold mb-3 md:mb-0">Prescriptions</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.prescriptions.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    @include('partials.sidebar-icon', ['name' => 'B_Add', 'class' => 'w-4 h-4'])
                    New Prescription
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $stats = [
                    'active' => [
                        'color' => 'bg-green-100 text-green-800',
                        'icon' => 'B_Tick',
                    ],
                    'expired' => [
                        'color' => 'bg-red-100 text-red-800',
                        'icon' => 'B_Cross',
                    ],
                    'cancelled' => [
                        'color' => 'bg-gray-100 text-gray-800',
                        'icon' => 'B_Cross',
                    ],
                    'filled' => [
                        'color' => 'bg-blue-100 text-blue-800',
                        'icon' => 'Prescription',
                    ],
                ];
            @endphp

            @foreach ($stats as $status => $style)
                <div class="bg-white p-4 rounded shadow hover:shadow-md transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Total {{ ucfirst($status) }}</p>
                            <p class="text-2xl font-semibold">
                                {{ App\Models\Prescription::where('status', $status)->count() }}</p>
                        </div>
                        <div class="{{ $style['color'] }} p-3 rounded-full">
                            @include('partials.sidebar-icon', [
                                'name' => $style['icon'],
                                'class' => 'w-6 h-6',
                            ])
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('backend.prescriptions.index') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search patient / prescription code" class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-2">
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">All Status</option>
                    @foreach (['active', 'expired', 'cancelled', 'filled'] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <select name="treatment_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Treatments</option>
                    @foreach ($treatments as $treatment)
                        <option value="{{ $treatment->id }}"
                            {{ request('treatment_id') == $treatment->id ? 'selected' : '' }}>
                            {{ $treatment->treatment_code }} - {{ $treatment->patient->full_name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <input type="date" name="date" value="{{ request('date') }}" class="w-full border rounded px-3 py-2">
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
                        <th class="px-3 py-2 text-left text-sm">Treatment</th>
                        <th class="px-3 py-2 text-left text-sm">Date</th>
                        <th class="px-3 py-2 text-left text-sm">Items</th>
                        <th class="px-3 py-2 text-left text-sm">Status</th>
                        <th class="px-3 py-2 text-center text-sm">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($prescriptions as $prescription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                    {{ $prescription->prescription_code }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">
                                    By: {{ $prescription->creator->full_name ?? 'System' }}
                                </div>
                            </td>

                            <td class="px-3 py-2">
                                <a href="{{ route('backend.patients.show', $prescription->treatment->patient_id) }}"
                                    class="text-blue-600 hover:underline">
                                    {{ $prescription->treatment->patient->full_name ?? 'N/A' }}
                                </a>
                                <div class="text-xs text-gray-500">
                                    {{ $prescription->treatment->patient->patient_code ?? '' }}
                                </div>
                            </td>

                            <td class="px-3 py-2">
                                <a href="{{ route('backend.treatments.show', $prescription->treatment_id) }}"
                                    class="text-blue-600 hover:underline">
                                    {{ $prescription->treatment->treatment_code }}
                                </a>
                                <div class="text-xs text-gray-500">
                                    {{ $prescription->treatment->service->name ?? 'N/A' }}
                                </div>
                            </td>

                            <td class="px-3 py-2">
                                {{ $prescription->prescription_date->format('d/m/Y') }}
                                <div class="text-xs text-gray-500">
                                    {{ $prescription->validity_days }} days validity
                                </div>
                            </td>

                            <td class="px-3 py-2">
                                <div class="flex items-center gap-1">
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">
                                        {{ $prescription->items->count() }} items
                                    </span>
                                    @if ($prescription->items->where('status', 'dispensed')->count() > 0)
                                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">
                                            {{ $prescription->items->where('status', 'dispensed')->count() }} dispensed
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-3 py-2">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'expired' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                        'filled' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $statusIcons = [
                                        'active' => 'B_Tick',
                                        'expired' => 'B_Cross',
                                        'cancelled' => 'B_Cross',
                                        'filled' => 'Prescription',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs rounded {{ $statusColors[$prescription->status] ?? 'bg-gray-100' }} flex items-center">
                                    @include('partials.sidebar-icon', [
                                        'name' => $statusIcons[$prescription->status] ?? '',
                                        'class' => 'w-3 h-3 mr-1',
                                    ])
                                    {{ ucfirst($prescription->status) }}
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('backend.prescriptions.show', $prescription) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs"
                                        title="View">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_View',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <a href="{{ route('backend.prescriptions.edit', $prescription) }}"
                                        class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs"
                                        title="Edit">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Edit',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    <a href="{{ route('backend.prescriptions.print', $prescription) }}" target="_blank"
                                        class="px-2 py-1 bg-purple-500 hover:bg-purple-600 text-white rounded text-xs"
                                        title="Print">
                                        @include('partials.sidebar-icon', [
                                            'name' => 'B_Print',
                                            'class' => 'w-4 h-4',
                                        ])
                                    </a>

                                    @if (!$prescription->items()->where('status', 'dispensed')->exists())
                                        <form action="{{ route('backend.prescriptions.destroy', $prescription) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Delete this prescription?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs"
                                                title="Delete">
                                                @include('partials.sidebar-icon', [
                                                    'name' => 'B_Delete',
                                                    'class' => 'w-4 h-4',
                                                ])
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                @include('partials.sidebar-icon', [
                                    'name' => 'Prescription',
                                    'class' => 'w-12 h-12 mx-auto text-gray-300 mb-3',
                                ])
                                <p class="text-sm">No prescriptions found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <x-pagination :paginator="$prescriptions" />
    </div>
@endsection
