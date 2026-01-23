<div class="flex justify-start gap-2 mt-6">

    {{-- Back / Cancel Button --}}
    <a href="{{ $backUrl }}"
        class="inline-flex items-center justify-center gap-2 px-6 py-2 rounded-md
              bg-gray-300 text-gray-800 font-medium
              hover:bg-gray-400 transition w-44 h-10 text-sm">
        <!-- SVG Back Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Cancel
    </a>

    {{-- Submit Button --}}
    <button type="submit"
        class="inline-flex items-center justify-center gap-2 px-6 py-2 rounded-md
                   bg-{{ $submitColor }}-600 text-white font-medium
                   hover:bg-{{ $submitColor }}-700 focus:outline-none focus:ring-2 focus:ring-{{ $submitColor }}-400 transition w-44 h-10 text-sm">
        <!-- SVG Save Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        {{ $submitText }}
    </button>

    {{-- Delete Button --}}
    <button type="button" data-modal-target="{{ $deleteModalId }}"
        class="inline-flex items-center justify-center gap-2 px-6 py-2 rounded-md
                   bg-red-600 text-white font-medium
                   hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition w-44 h-10 text-sm">
        <!-- SVG Trash Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v0H8a2 2 0 012-2z" />
        </svg>
        Delete
    </button>

</div>
