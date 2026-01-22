<x-app-layout>
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100 overflow-hidden">

        <!-- SIDEBAR (with overlay on small screens) -->
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r flex-shrink-0 flex flex-col transform transition-transform duration-300 md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'">
            <div class="flex-1 flex flex-col overflow-y-auto">
                @include('backend.layout.sidebar')
            </div>

            <!-- Profile & Logout -->
            <div class="border-t p-4 flex flex-col gap-2">
                @include('backend.layout.sidebar-profile')
            </div>
        </aside>

        <!-- Overlay for small screens -->
        <div class="fixed inset-0 bg-black bg-opacity-30 z-20 md:hidden" x-show="sidebarOpen" x-transition.opacity
            @click="sidebarOpen = false"></div>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col overflow-hidden md:pl-64">

            <!-- Navbar -->
            <div class="flex-shrink-0">
                @include('backend.layout.navbar')
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <div class="flex-shrink-0">
                @include('backend.layout.footer')
            </div>
        </div>
    </div>
</x-app-layout>
