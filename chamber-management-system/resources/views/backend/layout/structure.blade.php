<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Page Title - With descriptive fallback --}}
    {{-- Page Title --}}
    <title>
        @hasSection('title')
            @yield('title') | {{ config('app.name', 'Chamber Management System') }}
        @else
            {{ config('app.name', 'Chamber Management System') }}
        @endif
    </title>


    {{-- AdminLTE CSS --}}
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        .aside-bar-logo {
            max-width: 180px;
            height: auto;
            object-fit: contain;
        }
    </style>

    {{-- Custom Styles From View --}}
    @yield('styles')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

    {{-- Main Wrapper --}}
    <div class="app-wrapper">

        {{-- Header --}}
        @include('backend.layout.navbar')

        {{-- Sidebar --}}
        @include('backend.layout.sidebar')

        {{-- Main Content --}}
        <main class="app-main">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('backend.layout.footer')
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/adminlte.js') }}"></script>

    
    {{-- OverlayScrollbars Configuration --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector('.sidebar-wrapper');
            const isMobile = window.innerWidth <= 992;

            if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined && !isMobile) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: 'os-theme-light',
                        autoHide: 'leave',
                        clickScroll: true,
                    },
                });
            }
        });
    </script>

    {{-- Custom Scripts From View --}}
    @yield('scripts')
</body>

</html>
