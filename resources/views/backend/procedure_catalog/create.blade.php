@extends('backend.layout.structure')

@section('content')
    <div class=" space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Add New Dental Procedure</h2>
            <a href="{{ route('backend.procedure-catalog.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                <!-- Back Icon -->
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

        <!-- Full-width Form -->
        <form action="{{ route('backend.procedure-catalog.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Procedure Code -->
                <div>
                    <label for="procedure_code" class="block text-sm font-medium text-gray-700 mb-1">Procedure Code</label>
                    <input type="text" name="procedure_code" id="procedure_code" value="{{ old('procedure_code') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <!-- Procedure Name -->
                <div>
                    <label for="procedure_name" class="block text-sm font-medium text-gray-700 mb-1">Procedure Name</label>
                    <input type="text" name="procedure_name" id="procedure_name" value="{{ old('procedure_name') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Select Category</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Standard Duration -->
                <div>
                    <label for="standard_duration" class="block text-sm font-medium text-gray-700 mb-1">Standard Duration
                        (minutes)</label>
                    <input type="number" name="standard_duration" id="standard_duration"
                        value="{{ old('standard_duration') }}" min="1"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <!-- Standard Cost -->
                <div>
                    <label for="standard_cost" class="block text-sm font-medium text-gray-700 mb-1">Standard Cost</label>
                    <input type="number" step="0.01" name="standard_cost" id="standard_cost"
                        value="{{ old('standard_cost') }}" min="0"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Description (full width) -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description') }}</textarea>
                </div>

            </div>

            <!-- Submit -->
            <x-back-submit-buttons back-url="{{ route('backend.procedure-catalog.index') }}"
                submit-text="Save Procedure" />


        </form>
    </div>
@endsection
