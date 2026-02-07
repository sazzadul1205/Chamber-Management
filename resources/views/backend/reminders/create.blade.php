@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h2 class="text-3xl font-bold text-gray-900">Schedule Appointment Reminder</h2>
            <a href="{{ route('backend.reminders.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium transition">
                @include('partials.sidebar-icon', ['name' => 'B_Back', 'class' => 'w-4 h-4'])
                <span>Back to Reminders</span>
            </a>
        </div>

        <!-- ALERT -->
        @if ($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-400 rounded-md mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Please correct the following errors:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- FORM -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('backend.reminders.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Appointment Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Appointment *
                            </label>
                            <select name="appointment_id" required
                                class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                                <option value="">Select Appointment</option>
                                @foreach ($appointments as $apt)
                                    <option value="{{ $apt->id }}"
                                        {{ $appointment && $appointment->id == $apt->id ? 'selected' : '' }}>
                                        #{{ $apt->id }} - {{ $apt->patient->name }}
                                        ({{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d') }}
                                        at {{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reminder Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Reminder Type *
                            </label>
                            <select name="reminder_type" required
                                class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Minutes Before -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Minutes Before Appointment *
                            </label>
                            <input type="number" name="minutes_before" value="1440" min="5" max="10080"
                                required
                                class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                            <p class="mt-1 text-sm text-gray-500">
                                1440 minutes = 24 hours. Max: 10080 minutes (7 days)
                            </p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Custom Message -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Custom Message (Optional)
                            </label>
                            <textarea name="custom_message" rows="6" placeholder="Leave empty to use default message"
                                class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400"></textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Max 500 characters
                            </p>
                        </div>

                        <!-- Send Immediately Checkbox -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="sendNow" name="send_immediately" type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="ml-3">
                                <label for="sendNow" class="text-sm font-medium text-gray-700">
                                    Send Immediately
                                </label>
                                <p class="text-sm text-gray-500">
                                    If checked, reminder will be sent now instead of scheduled time
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 pt-6 border-t flex justify-end gap-3">
                    <a href="{{ route('backend.reminders.index') }}"
                        class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Schedule Reminder
                    </button>
                </div>
            </form>
        </div>

        <!-- APPOINTMENT DETAILS (if preselected) -->
        @if ($appointment)
            <div class="bg-white rounded-lg shadow mt-6 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Selected Appointment Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-blue-800 mb-1">Patient</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $appointment->patient->name }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-green-800 mb-1">Doctor</p>
                            <p class="text-lg font-semibold text-gray-900">Dr. {{ $appointment->doctor->user->name }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-purple-800 mb-1">Date & Time</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                <br>
                                <span class="text-sm font-normal">
                                    at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </span>
                            </p>
                        </div>
                        <div class="bg-amber-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-amber-800 mb-1">Type</p>
                            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($appointment->appointment_type) }}
                            </p>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if ($appointment->expected_duration)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'B_Clock',
                                        'class' => 'w-4 h-4',
                                    ])
                                </span>
                                <div>
                                    <p class="text-sm text-gray-500">Duration</p>
                                    <p class="text-sm font-medium">{{ $appointment->expected_duration }} minutes</p>
                                </div>
                            </div>
                        @endif

                        @if ($appointment->chair)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'Bed',
                                        'class' => 'w-4 h-4',
                                    ])
                                </span>
                                <div>
                                    <p class="text-sm text-gray-500">Chair</p>
                                    <p class="text-sm font-medium">{{ $appointment->chair->name }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($appointment->priority)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">
                                    @include('partials.sidebar-icon', [
                                        'name' => 'B_Priority',
                                        'class' => 'w-4 h-4',
                                    ])
                                </span>
                                <div>
                                    <p class="text-sm text-gray-500">Priority</p>
                                    <p class="text-sm font-medium">{{ ucfirst($appointment->priority) }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- INFORMATION CARD -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">About Appointment Reminders</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Reminders are sent automatically at the scheduled time</li>
                            <li>SMS reminders are limited to 160 characters</li>
                            <li>Email reminders include appointment details and clinic information</li>
                            <li>You can manually send reminders from the reminders list</li>
                            <li>Failed reminders can be retried or rescheduled</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Real-time character counter for custom message
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.querySelector('textarea[name="custom_message"]');
            if (textarea) {
                const counter = document.createElement('p');
                counter.className = 'text-xs text-gray-500 mt-1';
                counter.textContent = '0/500 characters';
                textarea.parentNode.appendChild(counter);

                textarea.addEventListener('input', function() {
                    const length = this.value.length;
                    counter.textContent = `${length}/500 characters`;

                    if (length > 500) {
                        counter.classList.remove('text-gray-500');
                        counter.classList.add('text-red-500');
                    } else {
                        counter.classList.remove('text-red-500');
                        counter.classList.add('text-gray-500');
                    }
                });
            }

            // Auto-update minutes label based on selection
            const minutesInput = document.querySelector('input[name="minutes_before"]');
            if (minutesInput) {
                const updateLabel = () => {
                    const minutes = parseInt(minutesInput.value);
                    let label = '';

                    if (minutes >= 1440) {
                        const days = (minutes / 1440).toFixed(1);
                        label = `(${days} days before)`;
                    } else if (minutes >= 60) {
                        const hours = (minutes / 60).toFixed(1);
                        label = `(${hours} hours before)`;
                    } else {
                        label = `(${minutes} minutes before)`;
                    }

                    // Update or create label
                    let labelElement = document.querySelector('.minutes-label');
                    if (!labelElement) {
                        labelElement = document.createElement('span');
                        labelElement.className = 'minutes-label text-sm font-medium text-gray-600 ml-2';
                        minutesInput.parentNode.appendChild(labelElement);
                    }
                    labelElement.textContent = label;
                };

                minutesInput.addEventListener('input', updateLabel);
                updateLabel(); // Initial call
            }
        });
    </script>
@endsection
