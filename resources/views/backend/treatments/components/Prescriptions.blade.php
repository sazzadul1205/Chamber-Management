<!-- Prescriptions Section -->
@if ($treatment->prescriptions->count())
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-prescription text-blue-600"></i>
                    Prescriptions ({{ $treatment->prescriptions->count() }})
                </h3>
                <!-- Only show edit button for existing prescription -->
                @if ($treatment->prescriptions->first())
                    <a href="{{ route('backend.prescriptions.edit', $treatment->prescriptions->first()) }}"
                        class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                        <i class="fas fa-edit"></i> Edit Prescription
                    </a>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Prescription Code
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Medicines
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($treatment->prescriptions as $prescription)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $prescription->prescription_code }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-user-circle mr-1"></i>
                                    {{ $prescription->creator->full_name ?? 'System' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-2">
                                    @foreach ($prescription->items->take(3) as $item)
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <span class="font-medium text-sm text-gray-800">
                                                    {{ $item->medicine->brand_name ?? 'Unknown' }}
                                                </span>
                                                <div class="text-xs text-gray-500 flex items-center mt-1">
                                                    <span class="mr-2">{{ $item->dosage }}</span>
                                                    <span class="mr-2">•</span>
                                                    <span>{{ $item->frequency }}</span>
                                                    <span class="mr-2">•</span>
                                                    <span>{{ $item->duration }}</span>
                                                    @if ($item->quantity > 1)
                                                        <span class="ml-2 text-blue-600">
                                                            <i class="fas fa-layer-group mr-1"></i>
                                                            {{ $item->quantity }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @php
                                                $statusClass = 'bg-yellow-100 text-yellow-800'; 
                                                if ($item->status === 'dispensed') {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                } elseif ($item->status === 'cancelled') {
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                }
                                            @endphp

                                            <span class="text-xs px-2 py-1 rounded-full {{ $statusClass }}">
                                                {{ ucfirst($item->status) }}
                                            </span>

                                        </div>
                                    @endforeach

                                    @if ($prescription->items->count() > 3)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-ellipsis-h mr-1"></i>
                                            +{{ $prescription->items->count() - 3 }} more medicines
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $prescription->prescription_date->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Valid:
                                    {{ $prescription->prescription_date->addDays($prescription->validity_days)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'expired' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                        'filled' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $statusIcons = [
                                        'active' => 'fas fa-clock',
                                        'expired' => 'fas fa-hourglass-end',
                                        'cancelled' => 'fas fa-ban',
                                        'filled' => 'fas fa-check-circle',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$prescription->status] ?? 'bg-gray-100 text-gray-800' }} flex items-center">
                                    <i class="{{ $statusIcons[$prescription->status] ?? 'fas fa-question' }} mr-1"></i>
                                    {{ ucfirst($prescription->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('backend.prescriptions.show', $prescription) }}"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition-colors"
                                        title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <!-- Edit Button (Now in the header, but keep here too for consistency) -->
                                    <a href="{{ route('backend.prescriptions.edit', $prescription) }}"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-600 bg-yellow-50 rounded hover:bg-yellow-100 transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <!-- Print Button -->
                                    <a href="{{ route('backend.prescriptions.print', $prescription) }}" target="_blank"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-purple-600 bg-purple-50 rounded hover:bg-purple-100 transition-colors"
                                        title="Print">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>

                                    <!-- Quick Dispense Button -->
                                    @if ($prescription->status === 'active')
                                        <form action="{{ route('backend.prescriptions.dispense-all', $prescription) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 rounded hover:bg-green-100 transition-colors"
                                                title="Dispense All"
                                                onclick="return confirm('Dispense all items in this prescription?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 12l2 2 4-4" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete Button -->
                                    @if (!$prescription->items()->where('status', 'dispensed')->exists())
                                        <form action="{{ route('backend.prescriptions.destroy', $prescription) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100 transition-colors"
                                                title="Delete" onclick="return confirm('Delete this prescription?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <!-- Empty State - Only show "New Prescription" button when no prescription exists -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-prescription text-blue-600"></i>
                    Prescriptions
                </h3>
                <a href="{{ route('backend.prescriptions.create', ['treatment' => $treatment->id]) }}"
                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg flex items-center gap-1 transition-colors">
                    <i class="fas fa-plus"></i> New Prescription
                </a>
            </div>
        </div>
        <div class="p-12 text-center">
            <div class="mx-auto w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-prescription-bottle-alt text-blue-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Prescriptions Yet</h3>
            <p class="text-gray-500 mb-6">Create prescriptions to prescribe medications for this treatment</p>
            <a href="{{ route('backend.prescriptions.create', ['treatment' => $treatment->id]) }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create First Prescription
            </a>
        </div>
    </div>
@endif
