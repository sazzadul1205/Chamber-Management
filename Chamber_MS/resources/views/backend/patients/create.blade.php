@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Patient</h1>
                <p class="text-gray-600 mt-1">
                    Create a new patient profile with contact and medical information
                </p>
            </div>
        </div>

        <!-- VALIDATION ERRORS -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM CARD -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('backend.patients.store') }}" method="POST">
                @csrf

                <div class="p-6 space-y-6">

                    <!-- BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Name *
                                </label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Phone Number *
                                </label>

                                <div class="flex">
                                    <!-- Fixed country code -->
                                    <span
                                        class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-100 text-gray-700 text-sm select-none">
                                        +880
                                    </span>

                                    <!-- User input -->
                                    <input type="text" name="phone_local" value="{{ old('phone_local') }}" required
                                        placeholder="1XXXXXXXXX"
                                        class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Hidden full phone value -->
                            <input type="hidden" name="phone" id="phone_full">


                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Gender
                                </label>
                                <select name="gender"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Gender</option>
                                    <option value="male" @selected(old('gender') == 'male')>Male</option>
                                    <option value="female" @selected(old('gender') == 'female')>Female</option>
                                    <option value="other" @selected(old('gender') == 'other')>Other</option>
                                </select>
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Date of Birth
                                </label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Emergency Contact -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Emergency Contact
                                </label>
                                <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Referred By -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Referred By
                                </label>
                                <select name="referred_by"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status *
                                </label>
                                <select name="status" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active" @selected(old('status') == 'active')>Active</option>
                                    <option value="inactive" @selected(old('status') == 'inactive')>Inactive</option>
                                    <option value="deceased" @selected(old('status') == 'deceased')>Deceased</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- ADDITIONAL DETAILS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Address
                                </label>
                                <textarea name="address" rows="2"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('address') }}</textarea>
                            </div>

                            <!-- Medical History -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Medical History
                                </label>
                                <textarea name="medical_history" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('medical_history') }}</textarea>
                            </div>

                            <!-- Allergies -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Allergies
                                </label>
                                <textarea name="allergies" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('allergies') }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons back-url="{{ route('backend.patients.index') }}" submit-text="Save Patient" />
                </div>
            </form>
        </div>
    </div>


    {{-- SCRIPTS --}}
    <script>
        // Generate phone number
        document.addEventListener('DOMContentLoaded', function () {
            const localInput = document.querySelector('[name="phone_local"]');
            const fullInput = document.getElementById('phone_full');

            function updatePhone() {
                fullInput.value = '+880' + localInput.value.replace(/\D/g, '');
            }

            localInput.addEventListener('input', updatePhone);
            updatePhone();
        });
    </script>

@endsection