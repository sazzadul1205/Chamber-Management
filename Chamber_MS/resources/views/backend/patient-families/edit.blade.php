@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">
                Edit Family: {{ $patientFamily->family_name }}
            </h2>

            <a href="{{ route('backend.patient-families.index') }}"
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
            <div class="p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Family Info -->
        <div class="p-4 bg-blue-50 rounded border space-y-1">
            <p><strong>Family Code:</strong> {{ $patientFamily->family_code }}</p>
        </div>

        <!-- Form -->
        <form action="{{ route('backend.patient-families.update', $patientFamily) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">

                <!-- Family Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">Family Name *</label>
                    <input type="text" name="family_name" value="{{ old('family_name', $patientFamily->family_name) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('family_name') border-red-500 @enderror"
                        required>
                </div>

                <!-- Head of Family -->
                <div>
                    <label class="block text-sm font-medium mb-1">Head of Family (Patient) *</label>
                    <select name="head_patient_id"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('head_patient_id') border-red-500 @enderror"
                        required>
                        <option value="">Select Patient</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('head_patient_id', $patientFamily->head_patient_id) == $patient->id)>
                                {{ $patient->patient_code }} - {{ $patient->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Submit -->
            <x-edit-page-buttons back-url="{{ route('backend.patient-families.index') }}" submit-text="Update Family"
                submit-color="blue" />
        </form>
    </div>
@endsection
