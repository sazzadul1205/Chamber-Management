@extends('backend.layout.structure')

@section('content')
    <div class="p-6 space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Procedure Details</h2>
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

        <!-- Procedure Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">

            <div>
                <span class="font-medium">Procedure Code:</span>
                <div class="mt-1">{{ $procedureCatalog->procedure_code }}</div>
            </div>

            <div>
                <span class="font-medium">Procedure Name:</span>
                <div class="mt-1">{{ $procedureCatalog->procedure_name }}</div>
            </div>

            <div>
                <span class="font-medium">Category:</span>
                <div class="mt-1">{{ $procedureCatalog->category_name }}</div>
            </div>

            <div>
                <span class="font-medium">Duration:</span>
                <div class="mt-1">{{ $procedureCatalog->formatted_duration }}</div>
            </div>

            <div>
                <span class="font-medium">Standard Cost:</span>
                <div class="mt-1">{{ $procedureCatalog->formatted_cost }}</div>
            </div>

            <div>
                <span class="font-medium">Status:</span>
                <div class="mt-1">
                    <span
                        class="px-2 py-1 rounded text-xs font-medium bg-{{ $procedureCatalog->status == 'active' ? 'green-100 text-green-800' : 'red-100 text-red-800' }}">
                        {{ ucfirst($procedureCatalog->status) }}
                    </span>
                </div>
            </div>

            <div class="md:col-span-2">
                <span class="font-medium">Description:</span>
                <div class="mt-1 text-gray-600">{{ $procedureCatalog->description ?? '-' }}</div>
            </div>

        </div>

        <!-- Actions -->
        <div class="mt-6 flex gap-2">
            <a href="{{ route('backend.procedure-catalog.edit', $procedureCatalog->id) }}"
                class="flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md font-medium transition">
                <!-- Edit Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.232 5.232l3.536 3.536M9 11l6 6L21 9l-6-6-6 6z" />
                </svg>
                Edit Procedure
            </a>

            <form action="{{ route('backend.procedure-catalog.destroy', $procedureCatalog->id) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this procedure?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium transition">
                    <!-- Trash Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Delete Procedure
                </button>
            </form>
        </div>

    </div>
@endsection