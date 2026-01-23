@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Edit Dental Chair: {{ $dentalChair->name }}</h2>

            <a href="{{ route('backend.dental-chairs.index') }}"
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

        <!-- Form -->
        <form action="{{ route('backend.dental-chairs.update', $dentalChair->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Chair Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chair Code *</label>
                    <input type="text" name="chair_code" value="{{ old('chair_code', $dentalChair->chair_code) }}"
                        readonly
                        class="w-full border rounded px-3 py-2 bg-gray-100 focus:ring-2 focus:ring-blue-400 @error('chair_code') border-red-500 @enderror">
                    <p class="text-gray-500 text-xs mt-1">Chair code cannot be changed</p>
                </div>

                <!-- Chair Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chair Name *</label>
                    <input type="text" name="name" value="{{ old('name', $dentalChair->name) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror"
                        required maxlength="50">
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" value="{{ old('location', $dentalChair->location) }}"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('location') border-red-500 @enderror"
                        maxlength="100">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('status') border-red-500 @enderror"
                        required>
                        <option value="">Select Status</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('status', $dentalChair->status) == $key ? 'selected' : '' }}>{{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 @error('notes') border-red-500 @enderror">{{ old('notes', $dentalChair->notes) }}</textarea>
                </div>
            </div>

            <!-- Chair Information -->
            <div class="border rounded-md shadow-sm p-4 bg-gray-50">
                <h3 class="text-md font-medium mb-3">Chair Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Used</label>
                        <input type="text" value="{{ $dentalChair->formatted_last_used }}" readonly
                            class="w-full border rounded px-3 py-2 bg-gray-100 focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Appointments</label>
                        <input type="text" value="{{ $dentalChair->appointments->count() }} total" readonly
                            class="w-full border rounded px-3 py-2 bg-gray-100 focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Status</label>
                        <input type="text" value="{{ $dentalChair->status_name }}" readonly
                            class="w-full border rounded px-3 py-2"
                            style="color: {{ $dentalChair->status_color == 'success' ? 'green' : ($dentalChair->status_color == 'warning' ? 'orange' : 'red') }}">
                    </div>
                </div>
            </div>

            <!-- Submit & Delete -->
            <x-edit-page-buttons back-url="{{ route('backend.dental-chairs.index') }}" submit-text="Update Chair"
                delete-modal-id="deleteModal" submit-color="blue" />

        </form>
    </div>

    <x-delete-modal id="deleteModal" title="Delete Dental Chair"
        message="Are you sure you want to delete dental chair '{{ $dentalChair->name }}'?" :route="route('backend.dental-chairs.destroy', $dentalChair->id)" />
@endsection
