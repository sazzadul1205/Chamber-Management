<x-app-layout>
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-gray-100">

        @include('backend.layout.sidebar')

        <!-- MAIN WRAPPER -->
        <div class="flex flex-col flex-1">

            @include('backend.layout.navbar')

            <!-- CONTENT -->
            @yield('content')

            @include('backend.layout.footer')

        </div>
    </div>
</x-app-layout>
