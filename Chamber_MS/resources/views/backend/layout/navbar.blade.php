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

        <span class="text-lg font-semibold text-gray-700 tracking-tight">
            My App
        </span>
    </div>

    <!-- Right: User Info -->
    <div class="flex items-center gap-3">
        <img src="{{ asset('assets/Default_User.png') }}" class="w-10 h-10 rounded-full border" alt="User Avatar">
        <div class="leading-tight text-right">
            <div class="text-sm font-medium text-gray-700">
                John Doe
            </div>
            <div class="text-xs text-gray-500">
                Administrator
            </div>
        </div>
    </div>

</header>
