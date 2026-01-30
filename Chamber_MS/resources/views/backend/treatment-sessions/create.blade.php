@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Create Treatment Session</h2>
                @if ($treatment)
                    <p class="text-gray-600 mt-1">
                        For Treatment: <span class="font-semibold text-blue-700">{{ $treatment->treatment_code }}</span> -
                        Patient: <span class="font-medium">{{ $treatment->patient->full_name }}</span>
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($treatment)
                    <a href="{{ route('backend.treatments.show', $treatment) }}"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                        Back to Treatment
                    </a>
                @endif
                <a href="{{ route('backend.treatment-sessions.index') }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                    <i class="fas fa-list"></i>
                    All Sessions
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-4 border-b">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-orange-600"></i>
                    Session Information
                </h3>
            </div>
            <div class="p-6">
                <form action="{{ route('backend.treatment-sessions.store') }}" method="POST">
                    @csrf

                    @if ($treatment)
                        <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-700">Treatment:</p>
                            <p class="font-semibold text-blue-700">{{ $treatment->treatment_code }} -
                                {{ $treatment->patient->full_name }}</p>
                            <p class="text-xs text-gray-500 mt-1">Diagnosis: {{ $treatment->diagnosis }}</p>
                        </div>
                    @else
                        <!-- Treatment Selection (if not pre-selected) -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Treatment *</label>
                            <select name="treatment_id"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">-- Select Treatment --</option>
                                @foreach ($treatments as $t)
                                    <option value="{{ $t->id }}"
                                        {{ old('treatment_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->treatment_code }} - {{ $t->patient->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('treatment_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Session Details -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Session Number *</label>
                            <input type="number" name="session_number"
                                value="{{ $sessionNumber ?? old('session_number') }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required min="1">
                            @error('session_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Session Title *</label>
                            <input type="text" name="session_title" value="{{ old('session_title') }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('session_title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date & Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date *</label>
                            <input type="date" name="scheduled_date"
                                value="{{ old('scheduled_date') ?? date('Y-m-d') }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('scheduled_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) *</label>
                            <input type="number" name="duration_planned" value="{{ old('duration_planned') ?? 30 }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required min="1" max="480">
                            @error('duration_planned')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Appointment Link -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Link to Appointment</label>
                            <select name="appointment_id"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- No Appointment --</option>
                                @foreach ($appointments as $appointment)
                                    @if (!$appointment->treatment_session_id)
                                        {{-- Only show appointments not linked to a session
                                        --}}
                                        <option value="{{ $appointment->id }}"
                                            {{ old('appointment_id') == $appointment->id ? 'selected' : '' }}>
                                            {{ $appointment->appointment_code }} -
                                            {{ $appointment->appointment_date->format('d/m/Y') }}
                                            at {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                            ({{ $appointment->patient->full_name }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('appointment_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dental Chair -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dental Chair</label>
                            <select name="chair_id"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- No Chair Assigned --</option>
                                @foreach ($chairs as $chair)
                                    <option value="{{ $chair->id }}"
                                        {{ old('chair_id') == $chair->id ? 'selected' : '' }}>
                                        {{ $chair->name }} {{ $chair->is_available ? '(Available)' : '(Occupied)' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('chair_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                @foreach (App\Models\TreatmentSession::statuses() as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status') == $key ? 'selected' : ($key == 'scheduled' ? 'selected' : '') }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cost -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cost for Session (৳)</label>
                            <input type="number" step="0.01" name="cost_for_session"
                                value="{{ old('cost_for_session') }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('cost_for_session')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Procedure Details -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Procedure Details</label>
                        <textarea name="procedure_details" rows="3"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('procedure_details') }}</textarea>
                        @error('procedure_details')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Next Session Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Next Session Date</label>
                            <input type="date" name="next_session_date" value="{{ old('next_session_date') }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('next_session_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Next Session Notes</label>
                            <input type="text" name="next_session_notes" value="{{ old('next_session_notes') }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('next_session_notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class=" mt-8 pb-6">
                        <x-back-submit-buttons back-url="{{ route('backend.treatments.index') }}"
                            submit-text="Save Treatment" />

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
