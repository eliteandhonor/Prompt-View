{{--
    Danger Button Component
    - Renders a destructive action button with red styling
    - All dynamic content except trusted slot is escaped for security
    - KISS: minimal, semantic markup
--}}
{{-- DEBUG: Dump $slot for validation (remove after check) --}}
@php // dump($slot); @endphp
<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => '
        inline-flex items-center px-5 py-3 min-h-[44px]
        bg-gradient-to-r from-neon-violet via-red-600 to-fuchsia-glow
        border-2 border-fuchsia-glow
        shadow-futuristic
        rounded-md font-semibold text-xs text-white uppercase tracking-widest
        hover:from-fuchsia-glow hover:via-red-500 hover:to-electric-blue
        hover:border-neon-violet hover:shadow-neon-violet
        hover:scale-105 active:scale-95
        focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2
        transition-all duration-150 ease-in-out
    '
]) }}>
    {{ $slot }}
</button>

