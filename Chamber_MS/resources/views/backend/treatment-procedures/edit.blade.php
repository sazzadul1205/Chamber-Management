@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Procedure</h1>
                <p class="text-gray-600 mt-1">Update procedure information</p>
            </div>
            <div class="text-sm bg-blue-50 px-4 py-2 rounded-lg">
                <span class="font-medium">Patient:</span> {{ $treatmentProcedure->treatment->patient->name }}
                <span class="mx-2">|</span>
                <span class="font-medium">Treatment #{{ $treatmentProcedure->treatment_id }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.treatment-procedures.update', $treatmentProcedure) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">
                    <!-- Procedure Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Common Procedures Quick Select -->
                            @if($commonProcedures->isNotEmpty())
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Select from Common
                                        Procedures:</label>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($commonProcedures as $common)
                                            <button type="button"
                                                class="text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150"
                                                onclick="selectCommonProcedure('{{ $common->procedure_code }}', '{{ $common->procedure_name }}', {{ $common->cost }}, {{ $common->duration }})">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <span class="font-medium text-gray-900">{{ $common->procedure_code }}</span>
                                                        <span class="text-gray-600 ml-2">{{ $common->procedure_name }}</span>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="font-medium">${{ number_format($common->cost, 2) }}</div>
                                                        <div class="text-sm text-gray-500">{{ $common->duration }} min</div>
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Procedure Information -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Procedure Information</h3>

                                <div class="space-y-4">
                                    <div>
                                        <label for="procedure_code" class="block text-sm font-medium text-gray-700 mb-1">
                                            Procedure Code *
                                        </label>
                                        <input type="text" id="procedure_code" name="procedure_code" required
                                            value="{{ old('procedure_code', $treatmentProcedure->procedure_code) }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('procedure_code')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="procedure_name" class="block text-sm font-medium text-gray-700 mb-1">
                                            Procedure Name *
                                        </label>
                                        <input type="text" id="procedure_name" name="procedure_name" required
                                            value="{{ old('procedure_name', $treatmentProcedure->procedure_name) }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('procedure_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Dental Details -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Dental Details</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="tooth_number" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tooth Number
                                        </label>
                                        <input type="text" id="tooth_number" name="tooth_number"
                                            value="{{ old('tooth_number', $treatmentProcedure->tooth_number) }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="e.g., 14">
                                        @error('tooth_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="surface" class="block text-sm font-medium text-gray-700 mb-1">
                                            Surface
                                        </label>
                                        <select id="surface" name="surface"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Surface</option>
                                            <option value="Occlusal" {{ old('surface', $treatmentProcedure->surface) == 'Occlusal' ? 'selected' : '' }}>Occlusal
                                            </option>
                                            <option value="Mesial" {{ old('surface', $treatmentProcedure->surface) == 'Mesial' ? 'selected' : '' }}>Mesial</option>
                                            <option value="Distal" {{ old('surface', $treatmentProcedure->surface) == 'Distal' ? 'selected' : '' }}>Distal</option>
                                            <option value="Buccal" {{ old('surface', $treatmentProcedure->surface) == 'Buccal' ? 'selected' : '' }}>Buccal</option>
                                            <option value="Lingual" {{ old('surface', $treatmentProcedure->surface) == 'Lingual' ? 'selected' : '' }}>Lingual
                                            </option>
                                            <option value="Multiple" {{ old('surface', $treatmentProcedure->surface) == 'Multiple' ? 'selected' : '' }}>Multiple
                                            </option>
                                        </select>
                                        @error('surface')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Details -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Financial & Time</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-1">
                                            Cost ($) *
                                        </label>
                                        <input type="number" id="cost" name="cost" required step="0.01" min="0"
                                            value="{{ old('cost', $treatmentProcedure->cost) }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('cost')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">
                                            Duration (min) *
                                        </label>
                                        <input type="number" id="duration" name="duration" required min="1" max="480"
                                            value="{{ old('duration', $treatmentProcedure->duration) }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('duration')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select id="status" name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="planned" {{ old('status', $treatmentProcedure->status) == 'planned' ? 'selected' : '' }}>Planned</option>
                                    <option value="in_progress" {{ old('status', $treatmentProcedure->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $treatmentProcedure->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $treatmentProcedure->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Notes
                                </label>
                                <textarea id="notes" name="notes" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $treatmentProcedure->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end items-center px-6 py-4 bg-gray-50 border-t border-gray-200 space-x-3">
                    <a href="{{ route('backend.treatment-procedures.show', $treatmentProcedure) }}"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Procedure
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function selectCommonProcedure(code, name, cost, duration) {
                if (confirm('Replace current procedure details with ' + code + '?')) {
                    document.getElementById('procedure_code').value = code;
                    document.getElementById('procedure_name').value = name;
                    document.getElementById('cost').value = cost;
                    document.getElementById('duration').value = duration;
                }
            }
        </script>
    @endpush
@endsection