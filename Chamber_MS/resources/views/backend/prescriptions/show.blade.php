@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold">Prescription Details</h2>
                <p class="text-gray-600">{{ $prescription->prescription_code }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('backend.prescriptions.print', $prescription) }}" target="_blank"
                    class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                    <i class="fas fa-print mr-2"></i>Print
                </a>
                <a href="{{ route('backend.prescriptions.edit', $prescription) }}"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('backend.prescriptions.index') }}"
                    class="border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Prescription Info -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Prescription Header -->
                <div class="bg-white rounded shadow p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Prescription Information</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Prescription Code:</dt>
                                    <dd class="font-semibold">{{ $prescription->prescription_code }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Date:</dt>
                                    <dd class="font-semibold">{{ $prescription->prescription_date->format('F d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Valid Until:</dt>
                                    <dd class="font-semibold">
                                        {{ $prescription->prescription_date->addDays($prescription->validity_days)->format('F d, Y') }}
                                        <span class="text-sm text-gray-500">({{ $prescription->validity_days }} days)</span>
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Status:</dt>
                                    <dd>
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'expired' => 'bg-red-100 text-red-800',
                                                'cancelled' => 'bg-gray-100 text-gray-800',
                                                'filled' => 'bg-blue-100 text-blue-800',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 text-sm rounded-full {{ $statusColors[$prescription->status] }}">
                                            {{ ucfirst($prescription->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Patient Information</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Patient:</dt>
                                    <dd class="font-semibold">{{ $prescription->treatment->patient->full_name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Patient Code:</dt>
                                    <dd>{{ $prescription->treatment->patient->patient_code }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Treatment:</dt>
                                    <dd>{{ $prescription->treatment->treatment_code }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Doctor:</dt>
                                    <dd class="font-semibold">
                                        {{ $prescription->treatment->doctor->user->full_name ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Created By:</dt>
                                    <dd>{{ $prescription->creator->full_name ?? 'System' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($prescription->notes)
                        <div class="mt-6 pt-6 border-t">
                            <h4 class="font-semibold text-gray-700 mb-2">Notes:</h4>
                            <p class="text-gray-600 bg-gray-50 p-4 rounded">{{ $prescription->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Prescribed Medicines -->
                <div class="bg-white rounded shadow">
                    <div class="px-6 py-4 border-b flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Prescribed Medicines</h3>
                        @if($prescription->status === 'active')
                            <form action="{{ route('backend.prescriptions.dispenseAll', $prescription) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                                    onclick="return confirm('Dispense all items?')">
                                    <i class="fas fa-check-double mr-2"></i>Dispense All
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicine
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dosage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frequency
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($prescription->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold">{{ $item->medicine->brand_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->medicine->generic_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $item->medicine->strength }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium">{{ $item->dosage }}</div>
                                            <div class="text-sm text-gray-500">{{ ucfirst($item->route) }}</div>
                                        </td>
                                        <td class="px-6 py-4">{{ $item->frequency }}</td>
                                        <td class="px-6 py-4">{{ $item->duration }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $itemStatusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'dispensed' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $itemStatusColors[$item->status] }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                            @if($item->quantity)
                                                <div class="text-xs text-gray-500 mt-1">Qty: {{ $item->quantity }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                                @if($prescription->status === 'active')
                                                    @if($item->status === 'pending')
                                                        <form action="{{ route('backend.prescriptions.dispenseItem', $item) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-800"
                                                                title="Dispense">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('backend.prescriptions.removeItem', $item) }}" method="POST"
                                                            class="inline" onsubmit="return confirm('Remove this item?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800"
                                                                title="Remove">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($item->status === 'dispensed')
                                                        <form action="{{ route('backend.prescriptions.cancelItem', $item) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Cancel"
                                                                onclick="return confirm('Cancel this dispensed item?')">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @if($item->instructions)
                                        <tr class="bg-gray-50">
                                            <td colspan="6" class="px-6 py-3">
                                                <div class="text-sm">
                                                    <span class="font-semibold">Instructions:</span>
                                                    <span class="text-gray-600 ml-2">{{ $item->instructions }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Actions & Status -->
            <div class="space-y-6">

                <!-- Prescription Actions -->
                <div class="bg-white rounded shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Prescription Actions</h3>
                    <div class="space-y-3">
                        @if($prescription->status === 'active')
                            <form action="{{ route('backend.prescriptions.expire', $prescription) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700"
                                    onclick="return confirm('Mark as expired?')">
                                    <i class="fas fa-clock mr-2"></i>Mark as Expired
                                </button>
                            </form>

                            <form action="{{ route('backend.prescriptions.cancel', $prescription) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                                    onclick="return confirm('Cancel this prescription?')">
                                    <i class="fas fa-ban mr-2"></i>Cancel Prescription
                                </button>
                            </form>

                            <form action="{{ route('backend.prescriptions.markAsFilled', $prescription) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                                    onclick="return confirm('Mark as filled?')">
                                    <i class="fas fa-check-circle mr-2"></i>Mark as Filled
                                </button>
                            </form>
                        @endif

                        @if(!$prescription->items()->where('status', 'dispensed')->exists())
                            <form action="{{ route('backend.prescriptions.destroy', $prescription) }}" method="POST"
                                onsubmit="return confirm('Delete this prescription?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
                                    <i class="fas fa-trash mr-2"></i>Delete Prescription
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Add New Medicine -->
                @if($prescription->status === 'active')
                    <div class="bg-white rounded shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Medicine</h3>
                        <form action="{{ route('backend.prescriptions.addItem', $prescription) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Medicine</label>
                                    <select name="medicine_id" required class="w-full border rounded px-3 py-2">
                                        <option value="">Select Medicine</option>
                                        @foreach(App\Models\Medicine::active()->get() as $medicine)
                                            <option value="{{ $medicine->id }}">
                                                {{ $medicine->brand_name }} ({{ $medicine->generic_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                                        <input type="text" name="dosage" required placeholder="e.g., 1 tablet"
                                            class="w-full border rounded px-3 py-2">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                                        <select name="frequency" required class="w-full border rounded px-3 py-2">
                                            <option value="Once daily">Once daily</option>
                                            <option value="Twice daily">Twice daily</option>
                                            <option value="Three times daily">Three times daily</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                                        <input type="text" name="duration" required placeholder="e.g., 7 days"
                                            class="w-full border rounded px-3 py-2">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                        <input type="number" name="quantity" required min="1" value="1"
                                            class="w-full border rounded px-3 py-2">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                                    <textarea name="instructions" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                                </div>

                                <button type="submit"
                                    class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                    <i class="fas fa-plus mr-2"></i>Add Medicine
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Quick Info -->
                <div class="bg-blue-50 border border-blue-200 rounded p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">Prescription Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center text-blue-700">
                            <i class="fas fa-calendar mr-3"></i>
                            <span>Created: {{ $prescription->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center text-blue-700">
                            <i class="fas fa-history mr-3"></i>
                            <span>Last updated: {{ $prescription->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center text-blue-700">
                            <i class="fas fa-pills mr-3"></i>
                            <span>Total items: {{ $prescription->items->count() }}</span>
                        </div>
                        <div class="flex items-center text-blue-700">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span>Dispensed: {{ $prescription->items->where('status', 'dispensed')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection