@props(['href' => null])
{{-- DEBUG: Log how the slot is being rendered --}}
@php \Log::info('[secondary-button] slot=' . $slot) @endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->except('href')->merge([
            'class' => '
                inline-flex items-center px-5 py-3 min-h-[44px]
                bg-gradient-to-r from-indigo-950 via-purple-950 to-gray-950
                border-2 border-electric-blue
                shadow-glow-purple
                rounded-md font-semibold text-xs text-violet-accent uppercase tracking-widest
                hover:from-purple-950 hover:via-electric-blue hover:to-fuchsia-glow
                hover:border-neon-violet hover:shadow-neon-violet
                hover:scale-105 active:scale-95
                focus:outline-none focus:ring-2 focus:ring-electric-blue focus:ring-offset-2
                disabled:opacity-25
                transition-all duration-150 ease-in-out
            '
        ]) }}
    >
        {!! $slot !!}
    </a>
@else
    <button
        {{ $attributes->merge([
            'type' => 'button',
            'class' => '
                inline-flex items-center px-5 py-3 min-h-[44px]
                bg-gradient-to-r from-indigo-950 via-purple-950 to-gray-950
                border-2 border-electric-blue
                shadow-glow-purple
                rounded-md font-semibold text-xs text-violet-accent uppercase tracking-widest
                hover:from-purple-950 hover:via-electric-blue hover:to-fuchsia-glow
                hover:border-neon-violet hover:shadow-neon-violet
                hover:scale-105 active:scale-95
                focus:outline-none focus:ring-2 focus:ring-electric-blue focus:ring-offset-2
                disabled:opacity-25
                transition-all duration-150 ease-in-out
            '
        ]) }}
    >
        {!! $slot !!}
    </button>
@endif
