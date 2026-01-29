    @extends('backend.layout.structure')

    @section('content')
        <div class="space-y-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-semibold">Edit Prescription</h2>
                    <p class="text-gray-600">{{ $prescription->prescription_code }}</p>
                </div>
                <a href="{{ route('backend.prescriptions.show', $prescription) }}" 
                class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Details
                </a>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-700 rounded">
                    <strong class="font-semibold">Please fix the following errors:</strong>
                    <ul class="list-disc list-inside mt-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('backend.prescriptions.update', $prescription) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white rounded shadow p-6 space-y-6">
                    
                    <!-- Treatment Info (Read-only) -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Treatment Information</h3>
                        <div class="bg-gray-50 p-4 rounded">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Patient:</span>
                                    <span class="font-semibold">{{ $prescription->treatment->patient->full_name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Patient Code:</span>
                                    <span class="font-semibold">{{ $prescription->treatment->patient->patient_code }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Treatment:</span>
                                    <span class="font-semibold">{{ $prescription->treatment->treatment_code }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Doctor:</span>
                                    <span class="font-semibold">{{ $prescription->treatment->doctor->user->full_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prescription Details -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Prescription Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prescription Date *</label>
                                <input type="date" name="prescription_date" required
                                    value="{{ old('prescription_date', $prescription->prescription_date->format('Y-m-d')) }}"
                                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Validity (Days) *</label>
                                <input type="number" name="validity_days" min="1" max="30" required
                                    value="{{ old('validity_days', $prescription->validity_days) }}"
                                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="3"
                                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('notes', $prescription->notes) }}</textarea>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" required
                                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                                @foreach(['active', 'expired', 'cancelled', 'filled'] as $status)
                                    <option value="{{ $status }}" 
                                            {{ old('status', $prescription->status) == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Current Medicines (Read-only) -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Prescribed Medicines</h3>
                        
                        @if($prescription->items->count() > 0)
                            <div class="space-y-4">
                                @foreach($prescription->items as $item)
                                    <div class="border rounded p-4 bg-gray-50">
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Medicine</label>
                                                <p class="font-semibold">{{ $item->medicine->brand_name }}</p>
                                                <p class="text-sm text-gray-600">{{ $item->medicine->generic_name }}</p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Dosage</label>
                                                <p>{{ $item->dosage }}</p>
                                                <p class="text-sm text-gray-600">{{ ucfirst($item->route) }}</p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Frequency</label>
                                                <p>{{ $item->frequency }}</p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Status</label>
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
                                                    <p class="text-sm text-gray-600 mt-1">Qty: {{ $item->quantity }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($item->instructions)
                                            <div class="mt-3 pt-3 border-t">
                                                <label class="text-sm font-medium text-gray-700">Instructions</label>
                                                <p class="text-gray-600">{{ $item->instructions }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-pills text-4xl mb-3 text-gray-300"></i>
                                <p>No medicines prescribed</p>
                            </div>
                        @endif
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('backend.prescriptions.show', $prescription) }}" 
                        class="px-6 py-2 border rounded text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Update Prescription
                        </button>
                    </div>
                </div>
            </form>

            <!-- Danger Zone -->
            @if(!$prescription->items()->where('status', 'dispensed')->exists())
                <div class="bg-red-50 border border-red-200 rounded p-6">
                    <h3 class="text-lg font-semibold text-red-800 mb-4">Danger Zone</h3>
                    <p class="text-red-600 mb-4">Once you delete a prescription, there is no going back. Please be certain.</p>
                    <form action="{{ route('backend.prescriptions.destroy', $prescription) }}" 
                        method="POST" 
                        onsubmit="return confirm('Are you sure you want to delete this prescription? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i>Delete Prescription
                        </button>
                    </form>
                </div>
            @endif

        </div>
    @endsection