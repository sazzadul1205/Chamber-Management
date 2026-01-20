@php
    $menu = [
        // Grouped menu
        [
            'title' => 'Pages', // Group title
            'icon' => 'folder',
            'items' => [
                ['label' => 'Dashboard', 'route' => '#', 'icon' => 'home'],
                ['label' => 'Users', 'route' => '#', 'icon' => 'users'],
                ['label' => 'Settings', 'route' => '#', 'icon' => 'cog'],
            ],
        ],
        ['label' => 'Dashboard', 'route' => '#', 'icon' => 'home'],
        ['label' => 'Users', 'route' => '#', 'icon' => 'users'],
        ['label' => 'Settings', 'route' => '#', 'icon' => 'cog'],
    ];
@endphp

<aside class="bg-white w-64 border-r flex flex-col transition-all duration-300"
    :class="sidebarOpen ? 'block' : 'hidden md:block'">

    <!-- Brand -->
    <div class="h-16 flex items-center px-6 border-b text-lg font-semibold">
        My App
    </div>

    <!-- Menu Links -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

        @foreach ($menu as $item)
            {{-- Group --}}
            @if (isset($item['items']))
                <div x-data="{ open: true }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-gray-100">
                        <div class="flex items-center gap-2 font-semibold">
                            @include('partials.sidebar-icon', ['name' => $item['icon']])
                            <span>{{ $item['title'] }}</span>
                        </div>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" class="mt-2 space-y-1 ml-4">
                        @foreach ($item['items'] as $sub)
                            @php $active = false; @endphp
                            <a href="{{ $sub['route'] ?? '#' }}"
                                class="flex items-center gap-3 px-3 py-2 rounded transition
                                      {{ $active ? 'bg-gray-200 font-semibold text-gray-900' : 'hover:bg-gray-100 text-gray-700' }}">
                                @include('partials.sidebar-icon', ['name' => $sub['icon']])
                                <span>{{ $sub['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Independent item --}}
                @php $active = false; @endphp
                <a href="{{ $item['route'] ?? '#' }}"
                    class="flex items-center gap-3 px-3 py-2 rounded transition font-semibold hover:text-blue-400
                          {{ $active ? 'bg-gray-200 text-gray-900' : 'hover:bg-gray-100 text-gray-700' }}">
                    @include('partials.sidebar-icon', ['name' => $item['icon']])
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach

    </nav>

    <!-- Profile & Logout at bottom -->
    <div class="border-t p-4 flex flex-col gap-2">
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-100">
            <img src="{{ asset('assets/Default_User.png') }}" class="w-10 h-10 rounded-full border" alt="User">
            <div class="flex flex-col text-sm">
                <span class="font-semibold text-gray-800">John Doe</span>
                <span class="text-gray-500">Administrator</span>
            </div>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full text-left flex items-center gap-2 px-3 py-2 rounded hover:bg-red-100 text-red-600 font-medium">
                <!-- Logout Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-9V5" />
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
