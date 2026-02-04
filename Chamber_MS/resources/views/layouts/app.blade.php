<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    <style>
        .group:hover .tooltip {
            display: block;
        }

        .tooltip {
            transition: opacity 0.15s ease-in-out;
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    {{-- YOUR CUSTOM APP SHELL --}}
    {{ $slot }}


    <!-- Before closing </body> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>

</html>
