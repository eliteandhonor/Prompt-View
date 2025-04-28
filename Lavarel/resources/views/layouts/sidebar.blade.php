{{--
    Sidebar Layout Partial
    - Collapsible sidebar with navigation links (responsive, sticky, dark)
    - Uses <x-sidebar-link> for navigation
    - All dynamic content is escaped for security
    - KISS: minimal, semantic, and accessible markup
--}}
{{-- DEBUG: Dump current route for validation (remove after check) --}}
@php // dump(request()->route()->getName()); @endphp
<!-- Mobile overlay for sidebar (only on mobile & when open) -->
<div
    x-show="open"
    x-transitionx-bind:enter="transition-opacity duration-300 ease-out"
    x-transitionx-bind:enter-start="opacity-0"
    x-transitionx-bind:enter-end="opacity-100"
    x-transitionx-bind:leave="transition-opacity duration-200 ease-in"
    x-transitionx-bind:leave-start="opacity-100"
    x-transitionx-bind:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 z-20 md:hidden"
    @click="open = false"
    x-cloak
    aria-hidden="true"
    tabindex="-1"
></div>
<aside class="fixed left-0 top-0 h-screen w-60 bg-gradient-to-b from-indigo-950 via-purple-950 to-gray-950 text-gray-100 border-r-2 border-neon-violet shadow-futuristic z-30 flex flex-col transition-transform duration-300 ease-in-out transform"
      x-bind:class="{ '-translate-x-full': !open, 'translate-x-0': open }"
      x-cloak
      aria-label="Sidebar"
      tabindex="-1">
    <div class="h-16 flex items-center px-6 border-b border-neon-violet/30">
        <!-- Optionally add a compact logo here, or leave blank for minimalism -->
        <button class="ml-auto md:hidden text-gray-400 hover:text-white" @click="open = false" aria-label="Close sidebar">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-2">
            <x-sidebar-link
                href="{{ route('dashboard') }}"
                active="{{ request()->routeIs('dashboard') ? 'true' : 'false' }}"
                icon='<svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/></svg>'
            >Dashboard</x-sidebar-link>
            <x-sidebar-link
                href="{{ route('prompts.index') }}"
                active="{{ request()->routeIs('prompts.index') ? 'true' : 'false' }}"
                icon='<svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'
            >Prompts</x-sidebar-link>
            <!-- Add more navigation links here -->
        </ul>
    </nav>
    <div class="p-4 border-t border-neon-violet/30">
        <span class="text-xs text-violet-accent">&copy; {{ date('Y') }} Prompts Dashboard</span>
    </div>
</aside>