@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Doctor Profile</h1>
                <p class="text-gray-600 mt-1">
                    Update doctor information and professional details
                </p>
                <div class="mt-2 flex items-center gap-2 text-sm">
                    <span class="px-2 py-1 rounded-full {{ $doctor->status_color }}">
                        {{ ucfirst(str_replace('_', ' ', $doctor->status)) }}
                    </span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">Code: {{ $doctor->doctor_code }}</span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-600">{{ $doctor->full_name }}</span>
                </div>
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
            <form action="{{ route('backend.doctors.update', $doctor) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    <!-- BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Doctor Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Doctor Code *
                                </label>
                                <input type="text" name="doctor_code"
                                    value="{{ old('doctor_code', $doctor->doctor_code) }}" required maxlength="20"
                                    placeholder="e.g., DOC001"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Specialization -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Specialization
                                </label>
                                <input type="text" name="specialization"
                                    value="{{ old('specialization', $doctor->specialization) }}"
                                    placeholder="e.g., Orthodontics, Cardiology" maxlength="100"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Qualification -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Qualification
                                </label>
                                <textarea name="qualification" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., BDS, MDS, MBBS, MD">{{ old('qualification', $doctor->qualification) }}</textarea>
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
                                        value="{{ old('consultation_fee', $doctor->consultation_fee) }}" required
                                        min="0"
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
                                        value="{{ old('commission_percent', $doctor->commission_percent) }}" required
                                        min="0" max="100"
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
                                    <option value="active" @selected(old('status', $doctor->status) == 'active')>Active</option>
                                    <option value="inactive" @selected(old('status', $doctor->status) == 'inactive')>Inactive</option>
                                    <option value="on_leave" @selected(old('status', $doctor->status) == 'on_leave')>On Leave</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- DEFAULT WORKING SCHEDULE -->
                    <div class="border-t pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Current Working Schedule</h3>
                            <a href="{{ route('backend.doctors.schedule-management', $doctor) }}"
                                class="px-3 py-1 text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-md border border-blue-200 transition">
                                Edit Schedule
                            </a>
                        </div>
                        <p class="text-sm text-gray-500 mb-6">
                            Current working schedule configuration
                        </p>

                        @if ($doctor->activeSchedules && $doctor->activeSchedules->count() > 0)
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
                                    @php
                                        $schedule = $doctor->schedules->where('day_of_week', $key)->first();
                                        $isActive = $schedule && $schedule->is_active;
                                    @endphp
                                    <div class="border rounded-xl shadow-sm p-4 hover:shadow-md transition bg-gray-50">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center space-x-3">
                                                <input type="checkbox" id="day_{{ $key }}" name="schedule_days[]"
                                                    value="{{ $key }}" disabled
                                                    class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                    @checked($isActive)>
                                                <label for="day_{{ $key }}"
                                                    class="text-sm font-medium text-gray-800">
                                                    {{ $day }}
                                                    @if ($isActive)
                                                        <span class="ml-2 text-xs text-green-600">(Active)</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-500">Working Hours:</span>
                                                <input type="time" name="schedule_start_{{ $key }}"
                                                    value="{{ $schedule ? $schedule->start_time : '09:00' }}" disabled
                                                    class="w-24 border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-gray-400">to</span>
                                                <input type="time" name="schedule_end_{{ $key }}"
                                                    value="{{ $schedule ? $schedule->end_time : '17:00' }}" disabled
                                                    class="w-24 border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>

                                        @if ($isActive)
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 pl-7">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Max
                                                        Appointments</label>
                                                    <input type="number" name="schedule_max_{{ $key }}"
                                                        value="{{ $schedule->max_appointments }}" disabled
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Slot
                                                        Duration</label>
                                                    <select name="schedule_duration_{{ $key }}" disabled
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                                        <option value="15" @selected($schedule->slot_duration == 15)>15 minutes
                                                        </option>
                                                        <option value="30" @selected($schedule->slot_duration == 30)>30 minutes
                                                        </option>
                                                        <option value="45" @selected($schedule->slot_duration == 45)>45 minutes
                                                        </option>
                                                        <option value="60" @selected($schedule->slot_duration == 60)>60 minutes
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        @else
                                            <div class="pl-7 mt-2">
                                                <span class="text-xs text-gray-400 italic">Day off</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <p class="text-sm text-yellow-800">
                                    <span class="font-medium">No schedule configured.</span>
                                    <a href="{{ route('backend.doctors.schedule-management', $doctor) }}"
                                        class="ml-1 font-medium underline hover:text-yellow-900">
                                        Click here to set up working schedule.
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- UPCOMING LEAVES -->
                    @php
                        $upcomingLeaves = $doctor
                            ->leaves()
                            ->whereDate('leave_date', '>=', now())
                            ->where('status', 'approved')
                            ->orderBy('leave_date')
                            ->take(3)
                            ->get();
                    @endphp

                    @if ($upcomingLeaves->count() > 0)
                        <div class="border-t pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Upcoming Approved Leaves</h3>
                                <a href="{{ route('backend.doctors.leave-requests', ['doctor_id' => $doctor->id]) }}"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    View All
                                </a>
                            </div>
                            <div class="space-y-3">
                                @foreach ($upcomingLeaves as $leave)
                                    <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-red-800">
                                                        {{ $leave->formatted_date }}
                                                    </span>
                                                    <span class="text-xs px-2 py-1 bg-red-100 text-red-800 rounded">
                                                        {{ $leave->formatted_type }}
                                                    </span>
                                                </div>
                                                <div class="text-sm text-red-600 mt-1">{{ $leave->reason }}</div>
                                                @if ($leave->notes)
                                                    <div class="text-xs text-red-500 mt-1">{{ $leave->notes }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- FORM ACTIONS -->
                <div class="px-6 pb-4 bg-gray-50 border-t border-gray-200">
                    <x-back-submit-buttons back-url="{{ route('backend.doctors.show', $doctor) }}"
                        submit-text="Update Doctor" delete-modal-id="deleteModal" submit-color="blue" />
                </div>
            </form>
        </div>

        <!-- QUICK LINKS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('backend.doctors.schedule-management', $doctor) }}"
                class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-blue-300 hover:shadow transition">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Manage Schedule</h3>
                        <p class="text-sm text-gray-500 mt-1">Set working hours and days</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('backend.doctors.calendar', $doctor) }}"
                class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-green-300 hover:shadow transition">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">View Calendar</h3>
                        <p class="text-sm text-gray-500 mt-1">Check appointments and availability</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('backend.doctors.leave-requests', ['doctor_id' => $doctor->id]) }}"
                class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-yellow-300 hover:shadow transition">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Leave Requests</h3>
                        <p class="text-sm text-gray-500 mt-1">Manage leave applications</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Doctor</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to delete Dr. {{ $doctor->full_name }}?
                    This action cannot be undone. All related appointments and data will be permanently removed.
                </p>
                <form action="{{ route('backend.doctors.destroy', $doctor) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('DELETE')
                    <div>
                        <label for="confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Type "DELETE" to confirm:
                        </label>
                        <input type="text" id="confirmation" name="confirmation" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder="Type DELETE here">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideDeleteModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 border border-transparent rounded-md text-sm font-medium text-white">
                            Delete Doctor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('confirmation').value = '';
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

        // Close modal on outside click
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });
    </script>
@endsection
