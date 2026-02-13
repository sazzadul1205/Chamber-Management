@php
    $user = Auth::user();
    $roleMap = [
        1 => 'Super Admin',
        2 => 'Admin',
        3 => 'Doctor',
        4 => 'Receptionist',
        5 => 'Accountant',
    ];
    $userRoleName = $roleMap[$user->role_id] ?? 'User';
@endphp

<header class="py-2 bg-white border-b flex items-center justify-between px-6 relative">
    <!-- Left: Sidebar Toggle + Fullscreen + App Name -->
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" id="sidebarToggleBtn"
            class="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring" aria-label="Toggle Sidebar">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Full Screen Button -->
        <button onclick="toggleFullScreen()" id="fullscreenBtn"
            class="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring" aria-label="Toggle Full Screen">
            <svg id="fullscreenIcon" class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 8V4m0 0h4M4 4l5 5M11 4h4m0 0v4m0-4l5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
            </svg>
        </button>
    </div>

    <!-- Right: User Info with Dropdown -->
    <div class="relative flex items-center gap-3">
        <button onclick="toggleDropdown()" id="userDropdownBtn"
            class="flex items-center gap-3 hover:opacity-80 focus:outline-none">
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/Default_User.png') }}"
                class="w-10 h-10 rounded-full border border-gray-300 object-cover" alt="{{ $user->full_name }} Avatar">
            <div class="leading-tight text-right hidden sm:block">
                <div class="text-sm font-medium text-gray-700 truncate max-w-[150px]">
                    {{ $user->full_name }}
                </div>
                <div class="text-xs text-left text-gray-500 truncate max-w-[150px]">
                    {{ $userRoleName }}
                </div>
            </div>
            <svg id="dropdownArrow" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div id="userDropdown"
            class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50 hidden">
            <div class="py-1">
                <a href="{{ route('backend.user.show', $user->id) }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors">
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

<script>
    // Full Screen Function
    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            document.getElementById('fullscreenIcon').innerHTML =
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0l5 5M4 4v5m0-5h5m6 0h5m0 0v5m0-5l-5 5M4 20h5m-5 0l5-5m-5 5v-5m16 0v5m0 0h-5m5 0l-5-5" />';
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
                document.getElementById('fullscreenIcon').innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5M11 4h4m0 0v4m0-4l5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />';
            }
        }
    }

    // Dropdown Functions
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        const arrow = document.getElementById('dropdownArrow');
        dropdown.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    // Close dropdown if clicked outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const button = document.getElementById('userDropdownBtn');
        const arrow = document.getElementById('dropdownArrow');

        if (!button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    });
</script>

<style>
    .rotate-180 {
        transform: rotate(180deg);
    }
</style>
