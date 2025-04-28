@props(['href' => null])

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->except('href')->merge([
            'class' => '
                inline-flex items-center px-5 py-3 min-h-[44px]
                bg-gradient-to-r from-violet-accent via-neon-violet to-fuchsia-glow
                border-2 border-neon-violet
                shadow-neon-violet
                rounded-md font-semibold text-xs text-white uppercase tracking-widest
                hover:from-neon-violet hover:via-fuchsia-glow hover:to-electric-blue
                hover:border-electric-blue hover:shadow-neon-violet
                hover:scale-105 active:scale-95
                focus:outline-none focus:ring-2 focus:ring-electric-blue focus:ring-offset-2
                transition-all duration-150 ease-in-out
            '
        ]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        {{ $attributes->merge([
            'type' => 'submit',
            'class' => '
                inline-flex items-center px-5 py-3 min-h-[44px]
                bg-gradient-to-r from-violet-accent via-neon-violet to-fuchsia-glow
                border-2 border-neon-violet
                shadow-neon-violet
                rounded-md font-semibold text-xs text-white uppercase tracking-widest
                hover:from-neon-violet hover:via-fuchsia-glow hover:to-electric-blue
                hover:border-electric-blue hover:shadow-neon-violet
                hover:scale-105 active:scale-95
                focus:outline-none focus:ring-2 focus:ring-electric-blue focus:ring-offset-2
                transition-all duration-150 ease-in-out
            '
        ]) }}
    >
        {{ $slot }}
    </button>
@endif
