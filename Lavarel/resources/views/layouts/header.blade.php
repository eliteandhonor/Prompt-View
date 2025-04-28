{{--
    Header Layout Partial
    - Sticky header bar, includes mobile hamburger for sidebar toggle and app title
    - All dynamic content is escaped for security
    - KISS: minimal, semantic, and accessible markup
--}}
{{-- DEBUG: Dump user/auth state for validation (future extension, remove after check) --}}
@php // dump(auth()->user()); @endphp
<header class="sticky top-0 z-20 w-full h-16 bg-gradient-to-r from-indigo-950 via-purple-950 to-gray-950 border-b-2 border-neon-violet shadow-futuristic flex items-center px-4 md:pl-60">
    <!-- Mobile Hamburger Button -->
    <button
        class="md:hidden mr-3 text-violet-accent hover:text-neon-violet focus:outline-none focus:ring-2 focus:ring-neon-violet transition-all duration-200"
        @click="open = !open"
        x-bind:aria-expanded="open"
        aria-controls="sidebar"
        aria-label="Open sidebar"
    >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <span class="text-lg font-semibold tracking-wide text-neon-violet drop-shadow-[0_0_8px_#c084fc]">Prompts Dashboard</span>
    <div class="ml-auto flex items-center space-x-4">
        <!-- Future: user/account/actions -->
    </div>
</header>