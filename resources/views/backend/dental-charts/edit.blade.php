@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">
                Edit Dental Chart: {{ $dentalChart->patient->full_name ?? 'N/A' }}
            </h2>

            <a href="{{ route('backend.dental-charts.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-3 bg-red-100 text-red-700 rounded mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Patient Info -->
        <div class="p-4 bg-blue-50 rounded border">
            <p><strong>Patient:</strong> {{ $dentalChart->patient->full_name ?? 'N/A' }}</p>
            <p><strong>Patient Code:</strong> {{ $dentalChart->patient->patient_code ?? 'N/A' }}</p>
        </div>

        <!-- Form -->
        <form action="{{ route('backend.dental-charts.update', $dentalChart->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Patient -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                    <select name="patient_id"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('patient_id') border-red-500 @enderror"
                        required>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}"
                                {{ old('patient_id', $dentalChart->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->patient_code }} - {{ $patient->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Chart Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chart Date *</label>
                    <input type="date" name="chart_date"
                        value="{{ old('chart_date', $dentalChart->chart_date->format('Y-m-d')) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('chart_date') border-red-500 @enderror"
                        required>
                </div>

                <!-- Tooth Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tooth Number *</label>
                    <select name="tooth_number"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('tooth_number') border-red-500 @enderror"
                        required>
                        <optgroup label="Permanent Teeth">
                            @foreach (App\Models\DentalChart::adultTeeth() as $tooth)
                                <option value="{{ $tooth }}"
                                    {{ old('tooth_number', $dentalChart->tooth_number) == $tooth ? 'selected' : '' }}>
                                    Tooth {{ $tooth }}
                                </option>
                            @endforeach
                        </optgroup>

                        <optgroup label="Primary Teeth (Children)">
                            @foreach (App\Models\DentalChart::childTeeth() as $tooth)
                                <option value="{{ $tooth }}"
                                    {{ old('tooth_number', $dentalChart->tooth_number) == $tooth ? 'selected' : '' }}>
                                    Tooth {{ $tooth }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <!-- Surface -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Surface</label>
                    <select name="surface"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('surface') border-red-500 @enderror">
                        <option value="">Select Surface</option>
                        @foreach (App\Models\DentalChart::surfaces() as $key => $value)
                            <option value="{{ $key }}"
                                {{ old('surface', $dentalChart->surface) == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Condition -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Condition *</label>
                    <select name="condition"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('condition') border-red-500 @enderror"
                        required>
                        @foreach (App\Models\DentalChart::conditions() as $key => $value)
                            <option value="{{ $key }}"
                                {{ old('condition', $dentalChart->condition) == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Procedure -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Procedure Done</label>
                    <select name="procedure_done"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('procedure_done') border-red-500 @enderror">
                        <option value="">Select Procedure</option>
                        @foreach (App\Models\DentalChart::procedures() as $key => $value)
                            <option value="{{ $key }}"
                                {{ old('procedure_done', $dentalChart->procedure_done) == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Next Checkup -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Next Checkup</label>
                    <input type="date" name="next_checkup"
                        value="{{ old('next_checkup', optional($dentalChart->next_checkup)->format('Y-m-d')) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('next_checkup') border-red-500 @enderror">
                </div>
            </div>

            <!-- Remarks -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" rows="3"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('remarks') border-red-500 @enderror">{{ old('remarks', $dentalChart->remarks) }}</textarea>
            </div>

            <!-- Submit -->
            <x-edit-page-buttons back-url="{{ route('backend.dental-charts.index') }}" submit-text="Update Record"
                delete-modal-id="deleteModal" submit-color="blue" />

        </form>
    </div>

    <x-delete-modal id="deleteModal" title="Delete Dental Chart"
        message="Are you sure you want to delete this dental chart record?" :route="route('backend.dental-charts.destroy', $dentalChart->id)" />

@endsection
