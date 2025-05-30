{{--
    Dropdown Component
    - Renders a dropdown menu with configurable alignment and width
    - All dynamic content is escaped for security
    - KISS: minimal, semantic markup
--}}
{{-- DEBUG: Dump $align/$width for validation (remove after check) --}}
@php // dump($align, $width); @endphp
@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transitionx-bind:enter="transition ease-out duration-200"
            x-transitionx-bind:enter-start="opacity-0 scale-95"
            x-transitionx-bind:enter-end="opacity-100 scale-100"
            x-transitionx-bind:leave="transition ease-in duration-75"
            x-transitionx-bind:leave-start="opacity-100 scale-100"
            x-transitionx-bind:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
