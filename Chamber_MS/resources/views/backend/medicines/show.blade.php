@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Medicine Details</h2>

            <div class="flex gap-2">
                <a href="{{ route('backend.medicines.edit', $medicine->id) }}"
                    class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-sm font-medium">
                    Edit
                </a>

                <a href="{{ route('backend.medicines.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-sm font-medium">
                    Back to List
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Medicine Info -->
            <div class="bg-white rounded-lg shadow p-5">
                <h4 class="font-semibold text-gray-700 mb-4">Basic Information</h4>

                <div class="overflow-hidden border rounded-lg">
                    <table class="w-full text-sm">
                        <tbody class="divide-y">
                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left w-1/3">Medicine Code</th>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">
                                        {{ $medicine->medicine_code }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Brand Name</th>
                                <td class="px-4 py-2">{{ $medicine->brand_name }}</td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Generic Name</th>
                                <td class="px-4 py-2">{{ $medicine->generic_name }}</td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Strength</th>
                                <td class="px-4 py-2">{{ $medicine->strength ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Dosage Form</th>
                                <td class="px-4 py-2">{{ $medicine->dosage_form_name }}</td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Unit</th>
                                <td class="px-4 py-2">{{ $medicine->unit }}</td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Manufacturer</th>
                                <td class="px-4 py-2">{{ $medicine->manufacturer ?? 'Not specified' }}</td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Category</th>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs bg-gray-100 rounded">
                                        {{ $medicine->category_name }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Status</th>
                                <td class="px-4 py-2">
                                    @php
                                        $statusColor = match ($medicine->status) {
                                            'active' => 'green',
                                            'inactive' => 'yellow',
                                            default => 'red',
                                        };
                                    @endphp
                                    <span
                                        class="px-2 py-1 text-xs bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 rounded">
                                        {{ ucfirst($medicine->status) }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Created At</th>
                                <td class="px-4 py-2">{{ $medicine->created_at->format('d M Y, h:i A') }}</td>
                            </tr>

                            <tr>
                                <th class="px-4 py-2 bg-gray-50 text-left">Updated At</th>
                                <td class="px-4 py-2">{{ $medicine->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stats + Full Name -->
            <div class="space-y-6">

                <!-- Usage Stats -->
                <div class="bg-white rounded-lg shadow p-5 text-center">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Usage Statistics</h4>
                    <div class="text-5xl font-bold text-blue-600">
                        {{ $medicine->usage_count }}
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Total Prescriptions</p>
                </div>

                <!-- Full Name -->
                <div class="bg-white rounded-lg shadow p-5">
                    <h4 class="font-semibold text-gray-700 mb-2">Medicine Full Name</h4>
                    <p class="text-lg font-medium">{{ $medicine->full_name }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        This is how the medicine appears in prescriptions
                    </p>
                </div>

            </div>
        </div>

        <!-- Recent Prescriptions -->
        @if ($medicine->prescriptionItems && $medicine->prescriptionItems->count())
            <div class="bg-white rounded-lg shadow p-5">
                <h4 class="font-semibold text-gray-700 mb-4">Recent Prescriptions</h4>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm border rounded-lg overflow-hidden">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Patient</th>
                                <th class="px-4 py-2 text-left">Doctor</th>
                                <th class="px-4 py-2 text-left">Dosage</th>
                                <th class="px-4 py-2 text-left">Duration</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($medicine->prescriptionItems as $item)
                                <tr>
                                    <td class="px-4 py-2">
                                        {{ optional($item->prescription)->prescription_date?->format('d/m/Y') ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ optional($item->prescription?->treatment?->patient)->full_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ optional($item->prescription?->treatment?->doctor?->user)->full_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2">{{ $item->dosage ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $item->duration ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
@endsection
