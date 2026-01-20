@switch($name)
    @case('home')
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10h6v-6h4v6h6V10" />
        </svg>
    @break

    @case('users')
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1
                     m6-4a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    @break

    @case('cog')
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.983 13.93a1.95 1.95 0 100-3.9 1.95 1.95 0 000 3.9z
                     M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06
                     a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21
                     a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51
                     1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06
                     a1.65 1.65 0 00.33-1.82
                     1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09
                     a1.65 1.65 0 001.51-1
                     1.65 1.65 0 00-.33-1.82l-.06-.06
                     a2 2 0 112.83-2.83l.06.06
                     a1.65 1.65 0 001.82.33h.01
                     a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09
                     a1.65 1.65 0 001 1.51h.01
                     a1.65 1.65 0 001.82-.33l.06-.06
                     a2 2 0 112.83 2.83l-.06.06
                     a1.65 1.65 0 00-.33 1.82v.01
                     a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09
                     a1.65 1.65 0 00-1.51 1z" />
        </svg>
    @break

    @case('folder')
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h5l2 3h11v9H3z" />
        </svg>
    @break
@endswitch
