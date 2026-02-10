@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Medicine Details</h1>
                <p class="text-gray-600 mt-1">
                    Complete information for {{ $medicine->brand_name }} ({{ $medicine->medicine_code }})
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.medicines.edit', $medicine->id) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Edit',
                        'class' => 'w-4 h-4 text-white',
                    ])
                    Edit Medicine
                </a>
                <a href="{{ route('backend.medicines.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow transition">
                    @include('partials.sidebar-icon', [
                        'name' => 'B_Back',
                        'class' => 'w-4 h-4',
                    ])
                    Back to Medicines
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- BASIC INFORMATION CARD -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Basic Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Medicine Code -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Medicine Code</span>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900 p-3 bg-blue-50 rounded-md">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ $medicine->medicine_code }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Brand Name -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Brand Name</span>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900 p-3 bg-gray-50 rounded-md">
                                        {{ $medicine->brand_name }}
                                    </div>
                                </div>

                                <!-- Generic Name -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Generic Name</span>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900 p-3 bg-purple-50 rounded-md">
                                        {{ $medicine->generic_name }}
                                    </div>
                                </div>

                                <!-- Strength -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Strength</span>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900 p-3 bg-red-50 rounded-md">
                                        {{ $medicine->strength ?? 'Not specified' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Dosage Form -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Dosage Form</span>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900 p-3 bg-green-50 rounded-md">
                                        {{ $medicine->dosage_form_name }}
                                    </div>
                                </div>

                                <!-- Unit -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Unit</span>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900 p-3 bg-yellow-50 rounded-md">
                                        {{ $medicine->unit }}
                                    </div>
                                </div>

                                <!-- Manufacturer -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Manufacturer</span>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900 p-3 bg-indigo-50 rounded-md">
                                        {{ $medicine->manufacturer ?? 'Not specified' }}
                                    </div>
                                </div>

                                <!-- Category -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Category</span>
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900 p-3 bg-gray-50 rounded-md">
                                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                                            {{ $medicine->category_name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SIDEBAR INFORMATION CARDS -->
            <div class="space-y-6">
                <!-- STATUS CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status Information</h3>

                        <!-- Status -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700">Current Status</span>
                            </div>
                            <div
                                class="text-lg font-semibold p-3 rounded-md 
                                {{ $medicine->status == 'active'
                                    ? 'bg-green-50 text-green-800'
                                    : ($medicine->status == 'inactive'
                                        ? 'bg-yellow-50 text-yellow-800'
                                        : 'bg-red-50 text-red-800') }}">
                                <span class="flex items-center gap-2">
                                    <span
                                        class="w-3 h-3 rounded-full 
                                        {{ $medicine->status == 'active'
                                            ? 'bg-green-500'
                                            : ($medicine->status == 'inactive'
                                                ? 'bg-yellow-500'
                                                : 'bg-red-500') }}"></span>
                                    {{ ucfirst($medicine->status) }}
                                </span>
                                <p
                                    class="text-sm font-normal mt-1 
                                    {{ $medicine->status == 'active'
                                        ? 'text-green-600'
                                        : ($medicine->status == 'inactive'
                                            ? 'text-yellow-600'
                                            : 'text-red-600') }}">
                                    @if ($medicine->status == 'active')
                                        Available for prescription
                                    @elseif($medicine->status == 'inactive')
                                        Temporarily unavailable
                                    @else
                                        No longer manufactured
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="space-y-3 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Created</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $medicine->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Last Updated</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $medicine->updated_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Time Created</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $medicine->created_at->format('h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- USAGE STATISTICS CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Usage Statistics</h3>

                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 mb-2">
                                {{ $medicine->usage_count }}
                            </div>
                            <p class="text-sm text-gray-600">Total Prescriptions</p>
                        </div>

                        @if ($medicine->usage_count > 0)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Average per month</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ round($medicine->usage_count / max(1, $medicine->created_at->diffInMonths(now())), 1) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Usage rate</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        @php
                                            $months = max(1, $medicine->created_at->diffInMonths(now()));
                                            $rate = round($medicine->usage_count / $months, 1);
                                        @endphp
                                        {{ $rate }} / month
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- FULL NAME CARD -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Complete Medicine Name</h3>

                        <div class="p-4 bg-gray-50 rounded-md">
                            <p class="text-gray-900 font-medium text-center">
                                {{ $medicine->full_name }}
                            </p>
                            <p class="text-xs text-gray-500 text-center mt-2">
                                This is how the medicine appears in prescriptions and reports
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT PRESCRIPTIONS SECTION -->
        @if ($medicine->prescriptionItems && $medicine->prescriptionItems->count())
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Prescriptions</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Patient
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Doctor
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dosage
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Duration
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($medicine->prescriptionItems->take(10) as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ optional($item->prescription)->prescription_date?->format('M d, Y') ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ optional($item->prescription?->treatment?->patient)->full_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            Dr.
                                            {{ optional($item->prescription?->treatment?->doctor?->user)->full_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->dosage ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->duration ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full 
                                                {{ optional($item->prescription)->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ optional($item->prescription)->status ?? 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($medicine->prescriptionItems->count() > 10)
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500">
                                Showing 10 of {{ $medicine->prescriptionItems->count() }} prescriptions
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
@endsection
