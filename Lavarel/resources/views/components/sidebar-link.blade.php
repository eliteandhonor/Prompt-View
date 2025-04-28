{{--
    Sidebar Link Component
    - Renders a navigation link for the sidebar, with optional icon and active state
    - All dynamic content except trusted SVG icons is escaped for security
    - KISS: minimal, semantic markup
--}}
{{-- DEBUG: Dump $href/$active for validation (remove after check) --}}
@php // dump($href, $active); @endphp
@props([
    'href',
    'active' => false,
    'icon' => null,
])

<li>
    <a href="{{ $href }}"
       {{ $attributes->merge([
           'class' =>
                'flex items-center px-6 py-2 rounded-md transition-colors ' .
                ($active
                    ? 'bg-neon-violet/20 text-neon-violet font-semibold shadow-neon-violet'
                    : 'hover:bg-purple-950/70 hover:text-violet-accent')
       ]) }}>
        @if($icon)
            {!! $icon !!}
        @else
            @if (isset($slot))
                {{ $slot }}
            @endif
        @endif
        {{ $label ?? '' }}
    </a>
</li>