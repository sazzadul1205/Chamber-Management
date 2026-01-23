@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">
                Edit Patient: {{ $patient->full_name }}
            </h2>

            <a href="{{ route('backend.patients.index') }}"
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

        <!-- Patient Info -->
        <div class="p-4 bg-blue-50 rounded border space-y-1">
            <p><strong>Patient Code:</strong> {{ $patient->patient_code }}</p>
            <p><strong>Phone:</strong> {{ $patient->phone }}</p>
        </div>

        <!-- Form -->
        <form action="{{ route('backend.patients.update', $patient->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">Full Name *</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $patient->full_name) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('full_name') border-red-500 @enderror"
                        required>
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium mb-1">Gender</label>
                    <select name="gender"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('gender') border-red-500 @enderror">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Male
                        </option>
                        <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Female
                        </option>
                        <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>Other
                        </option>
                    </select>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-sm font-medium mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth"
                        value="{{ old('date_of_birth', optional($patient->date_of_birth)->format('Y-m-d')) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium mb-1">Phone *</label>
                    <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('phone') border-red-500 @enderror"
                        required>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $patient->email) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Emergency Contact -->
                <div>
                    <label class="block text-sm font-medium mb-1">Emergency Contact</label>
                    <input type="text" name="emergency_contact"
                        value="{{ old('emergency_contact', $patient->emergency_contact) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Address</label>
                    <textarea name="address" rows="2" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('address', $patient->address) }}</textarea>
                </div>

                <!-- Referred By -->
                <div>
                    <label class="block text-sm font-medium mb-1">Referred By</label>
                    <select name="referred_by" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="">Select Patient</option>
                        @foreach ($patients as $ref)
                            <option value="{{ $ref->id }}"
                                {{ old('referred_by', $patient->referred_by) == $ref->id ? 'selected' : '' }}>
                                {{ $ref->patient_code }} - {{ $ref->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium mb-1">Status *</label>
                    <select name="status" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
                        required>
                        <option value="active" {{ old('status', $patient->status) == 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="inactive" {{ old('status', $patient->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive</option>
                        <option value="deceased" {{ old('status', $patient->status) == 'deceased' ? 'selected' : '' }}>
                            Deceased</option>
                    </select>
                </div>

                <!-- Medical History -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Medical History</label>
                    <textarea name="medical_history" rows="3"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('medical_history', $patient->medical_history) }}</textarea>
                </div>

                <!-- Allergies -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Allergies</label>
                    <textarea name="allergies" rows="3" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('allergies', $patient->allergies) }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <x-edit-page-buttons back-url="{{ route('backend.patients.index') }}" submit-text="Update Patient"
                submit-color="blue" />
        </form>
    </div>
@endsection
