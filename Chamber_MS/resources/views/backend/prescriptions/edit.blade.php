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
                class="bg-gray-300 p-2 rounded text-gray-600 hover:text-gray-900 items-center">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Details
            </a>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-4 bg-red-100 text-red-700 rounded-lg">
                <strong class="font-semibold">Please fix the following errors:</strong>
                <ul class="list-disc list-inside mt-2 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Debug: Show loaded items count -->
        {{-- <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <p class="text-blue-700 font-medium">Loading {{ count($existingItems ?? []) }} prescription items...</p>
        </div> --}}

        <!-- Form -->
        <form action="{{ route('backend.prescriptions.update', $prescription) }}" method="POST" id="prescription-form">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow-lg p-6 space-y-6">

                <!-- Treatment Info (Read-only) -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <span
                            class="bg-blue-100 text-blue-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">1</span>
                        Treatment Information
                    </h3>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-blue-700 font-medium">Patient:</span>
                                <span class="font-semibold block">{{ $prescription->treatment->patient->full_name }}</span>
                            </div>
                            <div>
                                <span class="text-blue-700 font-medium">Patient Code:</span>
                                <span
                                    class="font-semibold block">{{ $prescription->treatment->patient->patient_code }}</span>
                            </div>
                            <div>
                                <span class="text-blue-700 font-medium">Treatment:</span>
                                <span class="font-semibold block">{{ $prescription->treatment->treatment_code }}</span>
                            </div>
                            <div>
                                <span class="text-blue-700 font-medium">Doctor:</span>
                                <span
                                    class="font-semibold block">{{ $prescription->treatment->doctor->user->full_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prescription Details -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <span
                            class="bg-blue-100 text-blue-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">2</span>
                        Prescription Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prescription Date *</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <input type="date" name="prescription_date" required
                                    value="{{ old('prescription_date', $prescription->prescription_date->format('Y-m-d')) }}"
                                    class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Validity (Days) *</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <input type="number" name="validity_days" min="1" max="30" required
                                    value="{{ old('validity_days', $prescription->validity_days) }}"
                                    class="w-full border border-gray-300 rounded-lg pl-10 pr-12 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <span class="absolute right-3 top-3 text-gray-500">days</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <textarea name="notes" rows="2"
                                    class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('notes', $prescription->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @foreach(['active', 'expired', 'cancelled', 'filled'] as $status)
                                <option value="{{ $status }}" {{ old('status', $prescription->status) == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Medicines Section -->
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <span
                                class="bg-blue-100 text-blue-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">3</span>
                            Prescribed Medicines
                        </h3>
                        <button type="button" onclick="addMedicineRow()"
                            class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-5 py-2.5 rounded-lg hover:from-green-600 hover:to-emerald-700 transition shadow-md flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Medicine
                        </button>
                    </div>

                    <!-- Medicine Items Container -->
                    <div id="medicine-items" class="space-y-4">
                        <!-- Existing items will be loaded here by JavaScript -->
                    </div>

                    <!-- No Medicine Warning -->
                   <div id="no-medicine-warning" class="p-6 border-2 border-dashed border-gray-300 rounded-lg text-center">
    <div id="loading-spinner" class="w-16 h-16 mx-auto mb-3 flex items-center justify-center">
        <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
    </div>
    <p class="text-gray-500" id="loading-text">Loading prescription items...</p>
</div>

                    <!-- Medicine Row Template (Hidden) -->
                    <template id="medicine-row-template">
                        <div
                            class="medicine-item border border-gray-200 rounded-xl p-5 bg-gradient-to-r from-gray-50 to-white shadow-sm hover:shadow transition">
                            <div class="flex justify-between items-center mb-4 pb-3 border-b">
                                <div class="flex items-center">
                                    <div
                                        class="bg-blue-50 text-blue-700 w-7 h-7 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">Medicine Item <span
                                            class="item-number text-blue-600"></span></h4>
                                </div>
                                <button type="button" onclick="removeMedicineRow(this)"
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 w-9 h-9 rounded-full flex items-center justify-center transition"
                                    title="Delete this medicine">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                                <!-- Medicine Search -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Medicine *</label>
                                    <div class="relative">
                                        <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <select name="items[INDEX][medicine_id]" required
                                            class="medicine-select w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                            <option value="">Search and select medicine...</option>
                                            @foreach($medicines as $medicine)
                                                <option value="{{ $medicine->id }}" data-strength="{{ $medicine->strength }}"
                                                    data-form="{{ $medicine->dosage_form }}" data-unit="{{ $medicine->unit }}"
                                                    data-generic="{{ $medicine->generic_name }}">
                                                    {{ $medicine->brand_name }} ({{ $medicine->generic_name }}) -
                                                    {{ $medicine->strength }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="medicine-info mt-3 p-3 bg-blue-50 rounded-lg text-sm text-blue-700 hidden">
                                        <div class="flex flex-wrap gap-3">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                                </svg>
                                                <span class="strength font-medium"></span>
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="form"></span>
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                                </svg>
                                                <span class="unit"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dosage -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Dosage *</label>
                                    <div class="relative">
                                        <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <input type="text" name="items[INDEX][dosage]" required placeholder="e.g., 1 tablet"
                                            class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                    </div>
                                </div>

                                <!-- Frequency -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Frequency *</label>
                                    <div class="relative">
                                        <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <select name="items[INDEX][frequency]" required
                                            class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                            <option value="">Select...</option>
                                            <option value="Once daily">Once daily</option>
                                            <option value="Twice daily">Twice daily</option>
                                            <option value="Three times daily">Three times daily</option>
                                            <option value="Four times daily">Four times daily</option>
                                            <option value="Every 6 hours">Every 6 hours</option>
                                            <option value="Every 8 hours">Every 8 hours</option>
                                            <option value="Every 12 hours">Every 12 hours</option>
                                            <option value="As needed">As needed</option>
                                            <option value="At bedtime">At bedtime</option>
                                            <option value="With meals">With meals</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration *</label>
                                    <div class="relative">
                                        <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 01118 0z" />
                                        </svg>
                                        <input type="text" name="items[INDEX][duration]" required
                                            placeholder="e.g., 7 days, 2 weeks"
                                            class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                    </div>
                                </div>

                                <!-- Route -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Route</label>
                                    <div class="relative">
                                        <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <select name="items[INDEX][route]"
                                            class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                            <option value="oral">Oral</option>
                                            <option value="topical">Topical</option>
                                            <option value="inhalation">Inhalation</option>
                                            <option value="injection">Injection</option>
                                            <option value="rectal">Rectal</option>
                                            <option value="vaginal">Vaginal</option>
                                            <option value="ophthalmic">Ophthalmic</option>
                                            <option value="otic">Otic</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                                    <div class="flex items-center">
                                        <button type="button" onclick="decrementQuantity(this)"
                                            class="bg-gray-100 text-gray-700 w-10 h-10 rounded-l-lg border border-gray-300 hover:bg-gray-200 transition flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <div class="relative flex-1">
                                            <input type="number" name="items[INDEX][quantity]" min="1" required value="1"
                                                class="w-full border-y border-gray-300 px-4 py-3 text-center focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <button type="button" onclick="incrementQuantity(this)"
                                            class="bg-gray-100 text-gray-700 w-10 h-10 rounded-r-lg border border-gray-300 hover:bg-gray-200 transition flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Instructions -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                                    <div class="relative">
                                        <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <textarea name="items[INDEX][instructions]" rows="2"
                                            placeholder="Special instructions for patient..."
                                            class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-8 border-t">
                    <a href="{{ route('backend.prescriptions.show', $prescription) }}"
                        class="px-7 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition shadow-sm">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit"
                        class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg">
                        <i class="fas fa-save mr-2"></i>Update Prescription
                    </button>
                </div>
            </div>
        </form>

        <!-- Danger Zone -->
        @if(!$prescription->items()->where('status', 'dispensed')->exists())
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-red-800 mb-4">Danger Zone</h3>
                <p class="text-red-600 mb-4">Once you delete a prescription, there is no going back. Please be certain.</p>
                <form action="{{ route('backend.prescriptions.destroy', $prescription) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this prescription? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Delete Prescription
                    </button>
                </form>
            </div>
        @endif

    </div>

    <style>
        .medicine-item {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            height: 48px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px !important;
            padding-left: 42px !important;
            padding-right: 20px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 10px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }
    </style>

    <script>
        let medicineRows = [];
        let nextItemNumber = 1;

        // Debug: Log that script is loaded
        console.log('Prescription edit script loaded');

        // Initialize medicine search selects
        function initializeMedicineSearch() {
            console.log('Initializing medicine search...');
            $('.medicine-select').select2({
                placeholder: 'Search and select medicine...',
                allowClear: true,
                width: '100%',
                theme: 'classic',
                dropdownParent: $('.medicine-item')
            });
        }

        // Add new medicine row with optional data
        function addMedicineRow(data = {}) {
            console.log('addMedicineRow called with data:', data);

            try {
                const template = document.getElementById('medicine-row-template');
                if (!template) {
                    console.error('Template not found!');
                    return;
                }

                const container = document.getElementById('medicine-items');
                const warning = document.getElementById('no-medicine-warning');

                const newRow = template.content.cloneNode(true);
                const newItem = newRow.querySelector('.medicine-item');

                // Update all inputs with current index
                const currentIndex = medicineRows.length;
                console.log(`Current index: ${currentIndex}`);

                newItem.querySelectorAll('[name]').forEach(input => {
                    input.name = input.name.replace('INDEX', currentIndex);
                });

                // Set item number and store it
                const itemNumber = nextItemNumber++;
                newItem.dataset.itemNumber = itemNumber;
                newItem.querySelector('.item-number').textContent = '#' + itemNumber;

                // Add to container FIRST
                container.appendChild(newItem);
                medicineRows.push(newItem);

                // Hide warning
                warning.classList.add('hidden');
                container.classList.remove('hidden');

                // Set field values from data
                if (data.dosage) {
                    newItem.querySelector('input[name*="dosage"]').value = data.dosage;
                }

                if (data.frequency) {
                    newItem.querySelector('select[name*="frequency"]').value = data.frequency;
                }

                if (data.duration) {
                    newItem.querySelector('input[name*="duration"]').value = data.duration;
                }

                if (data.route) {
                    newItem.querySelector('select[name*="route"]').value = data.route;
                }

                if (data.quantity) {
                    newItem.querySelector('input[name*="quantity"]').value = data.quantity;
                }

                if (data.instructions) {
                    newItem.querySelector('textarea[name*="instructions"]').value = data.instructions;
                }

                // Get the select element
                const selectElement = newItem.querySelector('.medicine-select');

                // Set medicine ID if provided
                if (data.medicine_id) {
                    console.log(`Setting medicine_id to ${data.medicine_id}`);
                    selectElement.value = data.medicine_id;
                }

                // Initialize Select2 after a short delay to ensure DOM is ready
                setTimeout(() => {
                    $(selectElement).select2({
                        placeholder: 'Search and select medicine...',
                        allowClear: true,
                        width: '100%',
                        theme: 'classic',
                        dropdownParent: newItem
                    });

                    // Trigger change to update medicine info
                    if (data.medicine_id) {
                        $(selectElement).trigger('change');
                        updateMedicineInfo(selectElement);
                    }
                }, 100);

                // Add change event
                $(selectElement).on('change', function () {
                    updateMedicineInfo(this);
                });

                console.log(`Successfully added medicine row ${currentIndex + 1}`);

            } catch (error) {
                console.error('Error in addMedicineRow:', error);
            }
        }

        // Remove medicine row
        function removeMedicineRow(button) {
            const item = button.closest('.medicine-item');
            const container = document.getElementById('medicine-items');
            const warning = document.getElementById('no-medicine-warning');

            if (item) {
                const index = medicineRows.indexOf(item);
                if (index > -1) {
                    medicineRows.splice(index, 1);
                    updateItemNames();
                }

                item.style.opacity = '0';
                item.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    item.remove();
                    if (medicineRows.length === 0) {
                        container.classList.add('hidden');
                        warning.classList.remove('hidden');
                    }
                    updateItemNumbersDisplay();
                }, 300);
            }
        }

        // Update item names after deletion
        function updateItemNames() {
            medicineRows.forEach((item, index) => {
                item.querySelectorAll('[name]').forEach(input => {
                    const oldName = input.name;
                    const newName = oldName.replace(/items\[\d+\]/, `items[${index}]`);
                    input.name = newName;
                });
            });
        }

        // Update item numbers display
        function updateItemNumbersDisplay() {
            medicineRows.forEach((item, index) => {
                const numberSpan = item.querySelector('.item-number');
                if (numberSpan) {
                    numberSpan.textContent = '#' + (index + 1);
                }
            });
        }

        // Update medicine info display
        function updateMedicineInfo(select) {
            const selectedOption = select.options[select.selectedIndex];
            const infoDiv = select.closest('.medicine-item').querySelector('.medicine-info');

            if (selectedOption && selectedOption.value && selectedOption.dataset.strength) {
                infoDiv.classList.remove('hidden');
                infoDiv.querySelector('.strength').textContent = selectedOption.dataset.strength;
                infoDiv.querySelector('.form').textContent = selectedOption.dataset.form;
                infoDiv.querySelector('.unit').textContent = selectedOption.dataset.unit;
            } else {
                infoDiv.classList.add('hidden');
            }
        }

        // Quantity increment/decrement
        function incrementQuantity(button) {
            const input = button.parentElement.querySelector('input[type="number"]');
            input.value = parseInt(input.value) + 1;
        }

        function decrementQuantity(button) {
            const input = button.parentElement.querySelector('input[type="number"]');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        // Form validation
        document.getElementById('prescription-form').addEventListener('submit', function (e) {
            const items = document.querySelectorAll('.medicine-item');
            if (items.length === 0) {
                e.preventDefault();
                alert('Please add at least one medicine item.');
                return false;
            }

            let isValid = true;
            items.forEach(item => {
                const medicineSelect = item.querySelector('.medicine-select');
                if (!medicineSelect.value) {
                    isValid = false;
                    medicineSelect.style.borderColor = '#ef4444';
                    setTimeout(() => {
                        medicineSelect.style.borderColor = '';
                    }, 2000);
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please select a medicine for all items.');
                return false;
            }
        });

        // Load existing prescription items
        function loadExistingItems() {
            console.log('loadExistingItems function called');

            // Get the data from PHP
            const existingItems = @json($existingItems ?? []);
            console.log('Existing items data:', existingItems);
            console.log('Number of items:', existingItems.length);

            if (existingItems.length > 0) {
                console.log('Adding existing items...');
                existingItems.forEach((item, index) => {
                    console.log(`Adding item ${index + 1}:`, item);
                    addMedicineRow(item);
                });
            } else {
                console.log('No existing items, adding empty row');
                addMedicineRow();
            }
        }

        // SIMPLE INITIALIZATION - This should definitely work
        window.onload = function () {
            console.log('Window fully loaded, starting initialization...');

            // First, load existing items
            loadExistingItems();

            // Then initialize Select2 after items are loaded
            setTimeout(() => {
                console.log('Initializing Select2...');
                initializeMedicineSearch();
                console.log('Select2 initialized');
                console.log(`Total medicine rows: ${medicineRows.length}`);
            }, 500);
        };

        // Also try DOMContentLoaded as backup
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOMContentLoaded fired');
        });
    </script>
@endsection