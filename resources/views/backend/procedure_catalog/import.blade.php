@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Import Dental Procedures CSV</h2>
            <a href="{{ route('backend.procedure-catalog.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                <!-- Back Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>

        <!-- Success / Error Messages -->
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Import Form -->
        <form action="{{ route('backend.procedure-catalog.import') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            <div>
                <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">Choose CSV File</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <p class="text-xs text-gray-500 mt-1">Upload a CSV file containing procedure_code, procedure_name, category,
                    standard_duration, standard_cost, description, status.</p>
            </div>

            <div>
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition">
                    <!-- Upload Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v8m0-8l-4 4m4-4l4 4M12 4v8" />
                    </svg>
                    Import CSV
                </button>
            </div>

            <!-- Sample CSV Download -->
            <div class="mt-4 text-sm text-gray-600">
                <p>Need a sample CSV format? <a href="{{ asset('sample/procedures_sample.csv') }}"
                        class="text-blue-600 hover:underline">Download here</a>.</p>
            </div>
        </form>

    </div>
@endsection