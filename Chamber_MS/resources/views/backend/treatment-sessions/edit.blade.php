@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Edit Session</h2>
                <p class="text-gray-600 mt-1">
                    Session {{ $treatmentSession->session_number }}: {{ $treatmentSession->session_title }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('backend.treatment-sessions.show', $treatmentSession) }}"
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    Back to Session
                </a>
                <a href="{{ route('backend.treatments.show', $treatmentSession->treatment_id) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                    <i class="fas fa-stethoscope"></i>
                    View Treatment
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-6 py-4 border-b">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-edit text-yellow-600"></i>
                    Edit Session Information
                </h3>
            </div>
            <div class="p-6">
                <form action="{{ route('backend.treatment-sessions.update', $treatmentSession) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Session Details -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Session Number *</label>
                            <input type="number" name="session_number"
                                value="{{ old('session_number', $treatmentSession->session_number) }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required min="1">
                            @error('session_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Session Title *</label>
                            <input type="text" name="session_title"
                                value="{{ old('session_title', $treatmentSession->session_title) }}"
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
                                value="{{ old('scheduled_date', $treatmentSession->scheduled_date->format('Y-m-d')) }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('scheduled_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Actual Date</label>
                            <input type="date" name="actual_date"
                                value="{{ old('actual_date', $treatmentSession->actual_date ? $treatmentSession->actual_date->format('Y-m-d') : '') }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('actual_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Planned Duration (minutes) *</label>
                            <input type="number" name="duration_planned"
                                value="{{ old('duration_planned', $treatmentSession->duration_planned) }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required min="1" max="480">
                            @error('duration_planned')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Actual Duration (minutes)</label>
                            <input type="number" name="duration_actual"
                                value="{{ old('duration_actual', $treatmentSession->duration_actual) }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="1" max="480">
                            @error('duration_actual')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Appointment Link -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Link to Appointment</label>
                            <select name="appointment_id"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- No Appointment --</option>
                                @foreach($appointments as $appointment)
                                    <option value="{{ $appointment->id }}" {{ old('appointment_id', $treatmentSession->appointment_id) == $appointment->id ? 'selected' : '' }}>
                                        {{ $appointment->appointment_code }} -
                                        {{ $appointment->appointment_date->format('d/m/Y') }}
                                        at {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                    </option>
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
                                @foreach($chairs as $chair)
                                    <option value="{{ $chair->id }}" {{ old('chair_id', $treatmentSession->chair_id) == $chair->id ? 'selected' : '' }}>
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
                                @foreach(App\Models\TreatmentSession::statuses() as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $treatmentSession->status) == $key ? 'selected' : '' }}>
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
                                value="{{ old('cost_for_session', $treatmentSession->cost_for_session) }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('cost_for_session')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Procedure Details -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Procedure Details</label>
                        <textarea name="procedure_details" rows="4"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('procedure_details', $treatmentSession->procedure_details) }}</textarea>
                        @error('procedure_details')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Doctor's Notes</label>
                            <textarea name="doctor_notes" rows="3"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('doctor_notes', $treatmentSession->doctor_notes) }}</textarea>
                            @error('doctor_notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assistant's Notes</label>
                            <textarea name="assistant_notes" rows="3"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('assistant_notes', $treatmentSession->assistant_notes) }}</textarea>
                            @error('assistant_notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Materials Used -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Materials Used</label>
                        <textarea name="materials_used" rows="3"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('materials_used', $treatmentSession->materials_used) }}</textarea>
                        @error('materials_used')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Next Session Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Next Session Date</label>
                            <input type="date" name="next_session_date"
                                value="{{ old('next_session_date', $treatmentSession->next_session_date ? $treatmentSession->next_session_date->format('Y-m-d') : '') }}"
                                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('next_session_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Next Session Notes</label>
                            <input type="text" name="next_session_notes"
                                value="{{ old('next_session_notes', $treatmentSession->next_session_notes) }}"
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