@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Create New Family</h2>
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

        <!-- Form -->
        <form action="{{ route('backend.patient-families.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">

                <!-- Family Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Family Name *</label>
                    <input type="text" name="family_name" value="{{ old('family_name') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('family_name') border-red-500 @enderror"
                        required>
                </div>

                <!-- Head of Family -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Head of Family (Patient) *</label>
                    <select name="head_patient_id"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('head_patient_id') border-red-500 @enderror"
                        required>
                        <option value="">Select Patient</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('head_patient_id') == $patient->id)>
                                {{ $patient->patient_code }} - {{ $patient->full_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">This patient will be set as the family head</p>
                </div>

            </div>

            <!-- Submit -->
            <x-back-submit-buttons back-url="{{ route('backend.patient-families.index') }}" submit-text="Create Family" />

        </form>
    </div>
@endsection
