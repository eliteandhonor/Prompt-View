{{--
    Main Content Layout
    - Responsive container for main content area
    - Uses @yield('content') for page injection
    - All dynamic content is escaped for security
    - KISS: minimal markup
--}}
<div class="p-4 md:pl-60 pt-20 min-h-screen bg-transparent">
    <div class="max-w-5xl mx-auto rounded-2xl bg-gradient-to-br from-indigo-950 via-purple-950 to-gray-950/90 border-2 border-neon-violet shadow-futuristic backdrop-blur-lg">
        <div class="p-6 sm:p-8">
            @yield('content')
        </div>
    </div>
</div>