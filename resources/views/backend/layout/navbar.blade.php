@php
    $user = Auth::user();
    $roleMap = [
        1 => 'Super Admin',
        2 => 'Admin',
        3 => 'Doctor',
        4 => 'Receptionist',
        5 => 'Accountant'
    ];
    $userRoleName = $roleMap[$user->role_id] ?? 'User';
@endphp

<!-- NAVBAR -->
<header class="py-2 bg-white border-b flex items-center justify-between px-6">

    <!-- Left: Sidebar Toggle + App Name -->
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring"
            aria-label="Toggle Sidebar">
            <!-- Hamburger Icon -->
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Right: User Info with Dropdown -->
    <div class="relative flex items-center gap-3" x-data="{ dropdownOpen: false }"
        @click.outside="dropdownOpen = false">
        <button @click="dropdownOpen = !dropdownOpen"
            class="flex items-center gap-3 hover:opacity-80 focus:outline-none">
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/Default_User.png') }}"
                class="w-10 h-10 rounded-full border border-gray-300" alt="{{ $user->name }} Avatar">
            <div class="leading-tight text-right">
                <div class="text-sm font-medium text-gray-700 truncate">
                    {{ $user->full_name }}
                </div>
                <div class="text-xs text-left text-gray-500 truncate">
                    {{ $userRoleName }}
                </div>
            </div>
            <!-- Dropdown Arrow -->
            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
            style="display: none;">
            <div class="py-1">
                <!-- Profile Option -->
                <a href="{{ route('backend.user.show', $user->id) }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>

                <!-- Logout Option -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

</header>