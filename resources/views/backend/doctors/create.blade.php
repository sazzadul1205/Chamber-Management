@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Doctor</h1>
                <p class="text-gray-600 mt-1">
                    Create a new doctor profile with professional details and working schedule
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
            <form action="{{ route('backend.doctors.store') }}" method="POST">
                @csrf

                <div class="p-6 space-y-6">

                    <!-- BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Select User -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Select User *
                                </label>
                                <select name="user_id" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select User</option>
                                    @forelse($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                            {{ $user->full_name }} ({{ $user->phone }})
                                        </option>
                                    @empty
                                        <option value="" disabled>No available users found</option>
                                    @endforelse
                                </select>
                                @if ($users->isEmpty())
                                    <p class="mt-1 text-sm text-amber-600">
                                        No users available. Please ensure there are active users with role of Doctor who don't
                                        have a doctor profile.
                                    </p>
                                @endif
                            </div>

                            <!-- Doctor Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Doctor Code *
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" name="doctor_code" id="doctor_code"
                                        value="{{ old('doctor_code') }}" required maxlength="20" placeholder="e.g., DOC001"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" id="generateCode"
                                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Generate
                                    </button>
                                </div>
                            </div>

                            <!-- Specialization -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Specialization
                                </label>
                                <input type="text" name="specialization" value="{{ old('specialization') }}"
                                    placeholder="e.g., Orthodontics, Cardiology" maxlength="100"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Qualification -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Qualification
                                </label>
                                <input type="text" name="qualification" value="{{ old('qualification') }}"
                                    placeholder="e.g., BDS, MDS, MBBS, MD"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                        </div>
                    </div>

                    <!-- PROFESSIONAL DETAILS -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Professional Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Consultation Fee -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Consultation Fee (BDT) *
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">৳</span>
                                    </div>
                                    <input type="number" step="0.01" name="consultation_fee"
                                        value="{{ old('consultation_fee', 0) }}" required min="0"
                                        class="block w-full pl-7 pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <!-- Commission -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Commission (%) *
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" step="0.01" name="commission_percent"
                                        value="{{ old('commission_percent', 0) }}" required min="0" max="100"
                                        class="block w-full pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">%</span>
                                    </div>
                                </div>
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
                                    <option value="on_leave" @selected(old('status') == 'on_leave')>On Leave</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- DEFAULT WORKING SCHEDULE -->
                    <div class="border-t pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Default Working Schedule</h3>
                            <button type="button" id="toggleAllDays"
                                class="px-3 py-1 text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-md border border-blue-200 transition">
                                Toggle All Days
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mb-6">
                            Set default working days and hours. You can customize them later.
                        </p>

                        <div class="grid gap-4">
                            @php
                                $days = [
                                    'monday' => 'Monday',
                                    'tuesday' => 'Tuesday',
                                    'wednesday' => 'Wednesday',
                                    'thursday' => 'Thursday',
                                    'friday' => 'Friday',
                                    'saturday' => 'Saturday',
                                    'sunday' => 'Sunday',
                                ];
                            @endphp

                            @foreach ($days as $key => $day)
                                <div class="border rounded-xl shadow-sm p-4 hover:shadow-md transition bg-gray-50">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <input type="checkbox" id="day_{{ $key }}" name="schedule_days[]"
                                                value="{{ $key }}"
                                                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                @checked(in_array($key, old('schedule_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])))>
                                            <label for="day_{{ $key }}"
                                                class="text-sm font-medium text-gray-800">{{ $day }}</label>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500">Working Hours:</span>
                                            <input type="time" name="schedule_start_{{ $key }}"
                                                value="{{ old('schedule_start_' . $key, '09:00') }}"
                                                class="w-24 border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <span class="text-gray-400">to</span>
                                            <input type="time" name="schedule_end_{{ $key }}"
                                                value="{{ old('schedule_end_' . $key, '17:00') }}"
                                                class="w-24 border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 pl-7">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Max
                                                Appointments</label>
                                            <input type="number" name="schedule_max_{{ $key }}"
                                                value="{{ old('schedule_max_' . $key, 20) }}" min="1"
                                                max="50"
                                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Slot
                                                Duration</label>
                                            <select name="schedule_duration_{{ $key }}"
                                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="15" @selected(old('schedule_duration_' . $key, 30) == 15)>15 minutes</option>
                                                <option value="30" @selected(old('schedule_duration_' . $key, 30) == 30)>30 minutes</option>
                                                <option value="45" @selected(old('schedule_duration_' . $key, 30) == 45)>45 minutes</option>
                                                <option value="60" @selected(old('schedule_duration_' . $key, 30) == 60)>60 minutes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>


                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons back-url="{{ route('backend.doctors.index') }}" submit-text="Save Doctor" />
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate doctor code
            const generateBtn = document.getElementById('generateCode');
            const doctorCodeInput = document.getElementById('doctor_code');

            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    fetch("{{ route('backend.doctors.generate-code') }}")
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.code) {
                                doctorCodeInput.value = data.code;
                            }
                        })
                        .catch(error => {
                            console.error('Error generating code:', error);
                            alert('Error generating code. Please try again.');
                        });
                });
            }

            // Format consultation fee input
            const consultationFeeInput = document.querySelector('[name="consultation_fee"]');
            if (consultationFeeInput) {
                consultationFeeInput.addEventListener('blur', function() {
                    if (this.value) {
                        this.value = parseFloat(this.value).toFixed(2);
                    }
                });
            }

            // Format commission input
            const commissionInput = document.querySelector('[name="commission_percent"]');
            if (commissionInput) {
                commissionInput.addEventListener('blur', function() {
                    if (this.value) {
                        this.value = parseFloat(this.value).toFixed(2);
                    }
                });
            }

            // Auto-generate code if field is empty
            if (doctorCodeInput && !doctorCodeInput.value.trim()) {
                generateBtn?.click();
            }

            // Toggle all days checkbox
            const toggleAllBtn = document.getElementById('toggleAllDays');
            if (toggleAllBtn) {
                toggleAllBtn.addEventListener('click', function() {
                    const checkboxes = document.querySelectorAll('input[name="schedule_days[]"]');
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                    checkboxes.forEach(cb => {
                        cb.checked = !allChecked;
                        toggleTimeFields(cb);
                    });
                });
            }

            // Toggle time fields based on checkbox
            function toggleTimeFields(checkbox) {
                const day = checkbox.value;
                const startInput = document.querySelector(`[name="schedule_start_${day}"]`);
                const endInput = document.querySelector(`[name="schedule_end_${day}"]`);
                const maxInput = document.querySelector(`[name="schedule_max_${day}"]`);
                const durationSelect = document.querySelector(`[name="schedule_duration_${day}"]`);

                if (startInput && endInput && maxInput && durationSelect) {
                    const isDisabled = !checkbox.checked;
                    startInput.disabled = isDisabled;
                    endInput.disabled = isDisabled;
                    maxInput.disabled = isDisabled;
                    durationSelect.disabled = isDisabled;
                }
            }

            // Initialize time fields state
            const dayCheckboxes = document.querySelectorAll('input[name="schedule_days[]"]');
            dayCheckboxes.forEach(cb => {
                toggleTimeFields(cb);
                cb.addEventListener('change', function() {
                    toggleTimeFields(this);
                });
            });

            // Validate time inputs
            const timeInputs = document.querySelectorAll('input[type="time"]');
            timeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const day = this.name.split('_').pop();
                    const startInput = document.querySelector(`[name="schedule_start_${day}"]`);
                    const endInput = document.querySelector(`[name="schedule_end_${day}"]`);

                    if (startInput && endInput && startInput.value && endInput.value) {
                        if (startInput.value >= endInput.value) {
                            alert('End time must be after start time');
                            endInput.value = '';
                            endInput.focus();
                        }
                    }
                });
            });
        });
    </script>
@endsection
