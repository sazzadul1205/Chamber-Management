@props(['paginator'])

@if ($paginator->lastPage() > 1)
    <div
        {{ $attributes->merge(['class' => 'flex justify-center items-center space-x-4 px-4 py-3 bg-white border rounded shadow-sm']) }}>

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">
                Previous
            </a>
        @endif

        {{-- Current Page / Last Page --}}
        <span class="text-sm font-medium text-gray-700">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </span>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">
                Next
            </a>
        @else
            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
        @endif

    </div>
@endif
