@extends('backend.layout.structure')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Import Medicines</h2>

            <a href="{{ route('backend.medicines.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
            <h4 class="font-semibold mb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 16h-1v-4h-1m1-4h.01M12 18a9 9 0 100-18 9 9 0 000 18z" />
                </svg>
                CSV Format Requirements
            </h4>

            <ul class="list-disc list-inside space-y-1">
                <li><strong>Required columns:</strong> brand_name, generic_name</li>
                <li><strong>Optional columns:</strong> medicine_code, strength, dosage_form, unit, manufacturer, status</li>
                <li>Medicine code will be auto-generated if missing</li>
                <li>
                    Dosage form must be one of:
                    <span class="font-mono">
                        tablet, capsule, syrup, injection, gel, ointment, mouthwash, spray, drops, powder, cream, other
                    </span>
                </li>
                <li>Status: <strong>active</strong>, <strong>inactive</strong>, <strong>discontinued</strong>
                    (default: active)
                </li>
            </ul>
        </div>

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
        <form action="{{ route('backend.medicines.process-import') }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf

            <!-- CSV File -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    CSV File <span class="text-red-500">*</span>
                </label>
                <input type="file" name="csv_file" accept=".csv,.txt" required
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Update Existing -->
            <div class="flex items-center gap-2">
                <input type="checkbox" id="update_existing" name="update_existing" value="1" checked
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="update_existing" class="text-sm text-gray-700">
                    Update existing medicines with same code
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('backend.medicines.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-sm font-medium">
                    Cancel
                </a>

                <button type="submit"
                    class="flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v7m0 0l-3-3m3 3l3-3M4 8h16" />
                    </svg>
                    Import Medicines
                </button>
            </div>
        </form>

        <!-- Sample CSV -->
        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <h4 class="font-semibold text-gray-700">Sample CSV Format</h4>

            <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto">
brand_name,generic_name,strength,dosage_form,unit,manufacturer,status
Napa,Paracetamol,500mg,tablet,strip,Beximco Pharmaceuticals,active
Amoclav,Amoxicillin + Clavulanic Acid,500mg+125mg,tablet,strip,Square Pharmaceuticals,active
Xylocaine,Lidocaine,2%,injection,ampoule,AstraZeneca,active
        </pre>

            <div class="text-center">
                <a href="{{ asset('sample/medicines_sample.csv') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:bg-gray-100 rounded-md text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v7m0 0l-3-3m3 3l3-3M4 8h16" />
                    </svg>
                    Download Sample CSV
                </a>
            </div>
        </div>

    </div>
@endsection
