@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Add New Patient</h2>
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
        <form action="{{ route('backend.patients.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('full_name') border-red-500 @enderror"
                        required>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('phone') border-red-500 @enderror"
                        required>
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('gender') border-red-500 @enderror">
                        <option value="">Select Gender</option>
                        <option value="male" @selected(old('gender') == 'male')>Male</option>
                        <option value="female" @selected(old('gender') == 'female')>Female</option>
                        <option value="other" @selected(old('gender') == 'other')>Other</option>
                    </select>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('date_of_birth') border-red-500 @enderror">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror">
                </div>

                <!-- Emergency Contact -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('emergency_contact') border-red-500 @enderror">
                </div>

                <!-- Referred By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Referred By</label>
                    <select name="referred_by"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('referred_by') border-red-500 @enderror">
                        <option value="">Select Patient</option>
                        @foreach ($patients as $ref)
                            <option value="{{ $ref->id }}" @selected(old('referred_by') == $ref->id)>
                                {{ $ref->patient_code }} - {{ $ref->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('status') border-red-500 @enderror"
                        required>
                        <option value="active" @selected(old('status') == 'active')>Active</option>
                        <option value="inactive" @selected(old('status') == 'inactive')>Inactive</option>
                        <option value="deceased" @selected(old('status') == 'deceased')>Deceased</option>
                    </select>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                </div>

                <!-- Medical History -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medical History</label>
                    <textarea name="medical_history" rows="3"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('medical_history') border-red-500 @enderror">{{ old('medical_history') }}</textarea>
                </div>

                <!-- Allergies -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                    <textarea name="allergies" rows="3"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('allergies') border-red-500 @enderror">{{ old('allergies') }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <x-back-submit-buttons back-url="{{ route('backend.patients.index') }}" submit-text="Save Patient" />

        </form>
    </div>
@endsection
