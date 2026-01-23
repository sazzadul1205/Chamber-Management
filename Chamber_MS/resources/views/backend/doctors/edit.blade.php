@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Edit Doctor: {{ $doctor->user->full_name ?? 'N/A' }}</h2>

            <a href="{{ route('backend.doctors.index') }}"
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
            <div class="p-3 bg-red-100 text-red-700 rounded mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Doctor Info -->
        <div class="p-4 bg-blue-50 rounded border">
            <p><strong>Doctor:</strong> {{ $doctor->user->full_name ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $doctor->user->phone ?? 'N/A' }}</p>
        </div>

        <!-- Form -->
        <form action="{{ route('backend.doctors.update', $doctor->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Doctor Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor Code *</label>
                    <input type="text" name="doctor_code" value="{{ old('doctor_code', $doctor->doctor_code) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('doctor_code') border-red-500 @enderror"
                        required>
                </div>

                <!-- Specialization -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                    <input type="text" name="specialization" value="{{ old('specialization', $doctor->specialization) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('specialization') border-red-500 @enderror">
                </div>

                <!-- Qualification -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qualification</label>
                    <textarea name="qualification" rows="2"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('qualification') border-red-500 @enderror">{{ old('qualification', $doctor->qualification) }}</textarea>
                </div>

                <!-- Consultation Fee -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Consultation Fee (BDT) *</label>
                    <input type="number" step="0.01" name="consultation_fee"
                        value="{{ old('consultation_fee', $doctor->consultation_fee) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('consultation_fee') border-red-500 @enderror"
                        required>
                </div>

                <!-- Commission -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commission (%) *</label>
                    <input type="number" step="0.01" name="commission_percent"
                        value="{{ old('commission_percent', $doctor->commission_percent) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('commission_percent') border-red-500 @enderror"
                        required min="0" max="100">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('status') border-red-500 @enderror"
                        required>
                        <option value="active" {{ old('status', $doctor->status) == 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="inactive" {{ old('status', $doctor->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive</option>
                        <option value="on_leave" {{ old('status', $doctor->status) == 'on_leave' ? 'selected' : '' }}>On
                            Leave</option>
                    </select>
                </div>
            </div>

            <!-- Submit -->
            <x-edit-page-buttons back-url="{{ route('backend.doctors.index') }}" submit-text="Update Doctor"
                delete-modal-id="deleteModal" submit-color="blue" />

        </form>
    </div>

    <x-delete-modal id="deleteModal" title="Delete Doctor"
        message="Are you sure you want to delete Doctor '{{ $doctor->name }}'?" :route="route('backend.doctors.destroy', $doctor->id)" />

@endsection
