@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Import Diagnosis Codes (CSV)</h2>
            <a href="{{ route('backend.diagnosis-codes.index') }}"
                class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to List
            </a>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Import Form -->
        <form action="{{ route('backend.diagnosis-codes.import.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 font-medium">Select CSV File <span class="text-red-500">*</span></label>
                <input type="file" name="csv_file" accept=".csv"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                @error('csv_file') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                <p class="text-gray-500 text-sm mt-1">CSV must have columns: <strong>code, description, category,
                        status</strong>.</p>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('backend.diagnosis-codes.index') }}"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded flex items-center">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v16h16V4H4zM4 4l16 16" />
                    </svg>
                    Import CSV
                </button>
            </div>

            <div class="mt-4">
                <p class="text-gray-600 text-sm">
                    Need a template? <a href="{{ route('backend.diagnosis-codes.import.template') }}"
                        class="text-blue-600 hover:underline">Download CSV Template</a>
                </p>
            </div>
        </form>
    </div>
@endsection