<x-app-layout>
    <div id="app" class="flex h-screen bg-gray-100 overflow-hidden">
        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-30 bg-white border-r flex-shrink-0 flex flex-col transform transition-all duration-300 md:translate-x-0 sidebar-expanded"
            data-collapsed="false">
            <div class="flex-1 flex flex-col overflow-y-auto">
                @include('backend.layout.sidebar')
            </div>

            <!-- Profile & Logout -->
            <div class="border-t p-4 flex flex-col gap-2">
                @include('backend.layout.sidebar-profile')
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-30 z-20 hidden md:hidden"
            onclick="closeMobileSidebar()"></div>

        <!-- MAIN CONTENT -->
        <div id="mainContent" class="flex-1 flex flex-col overflow-hidden transition-all duration-300 md:ml-64">
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

    <script>
        // Initialize sidebar state from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            if (collapsed && window.innerWidth >= 768) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.remove('md:ml-64');
                mainContent.classList.add('md:ml-20');
            }

            // Check for active group on load
            const activeGroup = localStorage.getItem('activeSidebarGroup');
            if (activeGroup) {
                openGroup(activeGroup);
            }
        });

        // Toggle Sidebar Function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            if (window.innerWidth >= 768) {
                // Desktop: toggle collapsed mode
                if (sidebar.classList.contains('sidebar-expanded')) {
                    sidebar.classList.remove('sidebar-expanded');
                    sidebar.classList.add('sidebar-collapsed');
                    mainContent.classList.remove('md:ml-64');
                    mainContent.classList.add('md:ml-20');
                    localStorage.setItem('sidebarCollapsed', 'true');

                    // Close all open groups
                    document.querySelectorAll('.submenu').forEach(submenu => {
                        submenu.style.maxHeight = '0px';
                        submenu.classList.remove('open');
                    });
                    document.querySelectorAll('.group-btn .arrow').forEach(arrow => {
                        arrow.classList.remove('rotate-180');
                    });
                } else {
                    sidebar.classList.remove('sidebar-collapsed');
                    sidebar.classList.add('sidebar-expanded');
                    mainContent.classList.remove('md:ml-20');
                    mainContent.classList.add('md:ml-64');
                    localStorage.setItem('sidebarCollapsed', 'false');

                    // Reopen previously active group
                    const activeGroup = localStorage.getItem('activeSidebarGroup');
                    if (activeGroup) {
                        openGroup(activeGroup);
                    }
                }
            } else {
                // Mobile: toggle sidebar visibility
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar.classList.contains('-translate-x-64')) {
                    sidebar.classList.remove('-translate-x-64');
                    overlay.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-64');
                    overlay.classList.add('hidden');
                }
            }
        }

        // Close mobile sidebar
        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.add('-translate-x-64');
            overlay.classList.add('hidden');
        }

        // Fullscreen event listeners
        document.addEventListener('fullscreenchange', updateFullscreenIcon);
        document.addEventListener('webkitfullscreenchange', updateFullscreenIcon);
        document.addEventListener('mozfullscreenchange', updateFullscreenIcon);
        document.addEventListener('MSFullscreenChange', updateFullscreenIcon);

        function updateFullscreenIcon() {
            const icon = document.getElementById('fullscreenIcon');
            if (document.fullscreenElement) {
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0l5 5M4 4v5m0-5h5m6 0h5m0 0v5m0-5l-5 5M4 20h5m-5 0l5-5m-5 5v-5m16 0v5m0 0h-5m5 0l-5-5" />';
            } else {
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5M11 4h4m0 0v4m0-4l5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />';
            }
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            if (window.innerWidth >= 768) {
                const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (collapsed) {
                    sidebar.classList.remove('sidebar-expanded', '-translate-x-64');
                    sidebar.classList.add('sidebar-collapsed');
                    mainContent.classList.remove('md:ml-64');
                    mainContent.classList.add('md:ml-20');
                } else {
                    sidebar.classList.remove('sidebar-collapsed', '-translate-x-64');
                    sidebar.classList.add('sidebar-expanded');
                    mainContent.classList.remove('md:ml-20');
                    mainContent.classList.add('md:ml-64');
                }
            } else {
                sidebar.classList.add('-translate-x-64');
                document.getElementById('sidebarOverlay').classList.add('hidden');
            }
        });
    </script>

    <style>
        /* Sidebar States */
        .sidebar-expanded {
            width: 16rem;
            /* 64 */
        }

        .sidebar-collapsed {
            width: 5rem;
            /* 20 */
        }

        .sidebar-collapsed .sidebar-text {
            display: none;
        }

        .sidebar-collapsed .group-btn {
            justify-content: center;
        }

        .sidebar-collapsed .group-btn svg:not(.icon-only) {
            margin-right: 0;
        }

        .sidebar-collapsed .brand-full {
            display: none;
        }

        .sidebar-collapsed .brand-icon {
            display: block;
        }

        /* Transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        /* Hide scrollbar */
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>
</x-app-layout>
