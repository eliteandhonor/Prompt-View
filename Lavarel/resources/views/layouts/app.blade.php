{{--
    App Layout
    - Root HTML layout for all pages
    - Includes sidebar, header, and main content
    - Loads Alpine.js, Tailwind CSS, Google Fonts
    - All dynamic content is escaped for security
    - KISS: minimal, semantic, and accessible markup
--}}
{{-- DEBUG: Dump config/app.name for validation (remove after check) --}}
@php // dump(config('app.name')); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark font-sans">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Prompts Dashboard') }}</title>

        <!-- Inter Font via Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Tailwind and App CSS/JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js for sidebar toggle -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            html { font-family: 'Inter', 'Poppins', sans-serif; }
        </style>
        <style>
            body {
                /* Fallback for gradient background */
                background: linear-gradient(135deg, #1e1b4b 0%, #2a004f 60%, #09090b 100%);
                min-height: 100vh;
            }
        </style>
    </head>
    <body x-data="{ open: false }" class="font-sans antialiased bg-gradient-to-br from-indigo-950 via-purple-950 to-gray-950 min-h-screen text-gray-100">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Header -->
        @include('layouts.header')

        <!-- Main Content -->
        @include('layouts.main-content')
    </body>
</html>
