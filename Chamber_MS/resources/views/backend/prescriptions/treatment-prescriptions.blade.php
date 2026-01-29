@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold">Treatment Prescriptions</h2>
                <p class="text-gray-600">
                    {{ $treatment->treatment_code }} - {{ $treatment->patient->full_name }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('backend.prescriptions.create', ['treatment_id' => $treatment->id]) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>New Prescription
                </a>
                <a href="{{ url()->previous() }}"
                    class="border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Treatment Info -->
        <div class="bg-white rounded shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Treatment Information</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Patient</p>
                    <p class="font-semibold">{{ $treatment->patient->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Patient Code</p>
                    <p class="font-semibold">{{ $treatment->patient->patient_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Doctor</p>
                    <p class="font-semibold">{{ $treatment->doctor->user->full_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Service</p>
                    <p class="font-semibold">{{ $treatment->service->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Prescriptions List -->
        <div class="bg-white rounded shadow">
            @if($prescriptions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prescription Code
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valid Until</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($prescriptions as $prescription)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold">{{ $prescription->prescription_code }}</div>
                                        <div class="text-sm text-gray-500">{{ $prescription->creator->full_name ?? 'System' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $prescription->prescription_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                                {{ $prescription->items->count() }} items
                                            </span>
                                            @if($prescription->items->where('status', 'dispensed')->count() > 0)
                                                <span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                    {{ $prescription->items->where('status', 'dispensed')->count() }} dispensed
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'expired' => 'bg-red-100 text-red-800',
                                                'cancelled' => 'bg-gray-100 text-gray-800',
                                                'filled' => 'bg-blue-100 text-blue-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$prescription->status] }}">
                                            {{ ucfirst($prescription->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $prescription->prescription_date->addDays($prescription->validity_days)->format('M d, Y') }}
                                        <div class="text-xs text-gray-500">
                                            {{ $prescription->validity_days }} days
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('backend.prescriptions.show', $prescription) }}"
                                                class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('backend.prescriptions.edit', $prescription) }}"
                                                class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('backend.prescriptions.print', $prescription) }}" target="_blank"
                                                class="text-purple-600 hover:text-purple-900">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-prescription text-5xl mb-4 text-gray-300"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Prescriptions Yet</h3>
                    <p class="text-gray-500 mb-6">This treatment doesn't have any prescriptions yet.</p>
                    <a href="{{ route('backend.prescriptions.create', ['treatment_id' => $treatment->id]) }}"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Create First Prescription
                    </a>
                </div>
            @endif
        </div>

    </div>
@endsection