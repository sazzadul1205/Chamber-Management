<div class="flex items-center gap-4 mt-6">
    {{-- Submit Button --}}
    <button type="submit"
        class="inline-flex items-center justify-center gap-2
               w-40 h-11
               rounded-lg font-semibold text-sm
               bg-indigo-600 text-white
               hover:bg-indigo-700
               focus:outline-none focus:ring-2 focus:ring-indigo-400
               transition">

        {{-- Check SVG --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>

        <span>{{ $submitText }}</span>
    </button>

    {{-- Back Button --}}
    <a href="{{ $backUrl }}"
        class="inline-flex items-center justify-center gap-2
              w-40 h-11
              rounded-lg font-medium text-sm
              bg-gray-300 text-gray-700
              hover:bg-gray-400 transition">

        {{-- Back SVG --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>

        <span>Back</span>
    </a>

</div>
