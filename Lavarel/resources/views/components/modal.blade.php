{{--
    Modal Component
    - Renders a modal dialog with focus trap and accessibility features
    - All dynamic content is escaped for security
    - KISS: minimal, semantic, and accessible markup
--}}
{{-- DEBUG: Dump $name/$show for validation (remove after check) --}}
@php // dump($name, $show); @endphp
@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        focusables() {
            // All focusable element types...
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                // All non-disabled elements...
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: {{ $show ? 'block' : 'none' }};"
>
    <div
        x-show="show"
        class="fixed inset-0"
        x-onx-bind:click="show = false"
        x-transitionx-bind:enter="transition-all duration-300 ease-in-out"
        x-transitionx-bind:enter-start="opacity-0"
        x-transitionx-bind:enter-end="opacity-100"
        x-transitionx-bind:leave="transition-all duration-200 ease-in"
        x-transitionx-bind:leave-start="opacity-100"
        x-transitionx-bind:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div
        x-show="show"
        class="mb-6 bg-gradient-to-br from-indigo-950 via-purple-950 to-gray-950 text-gray-100 border-2 border-neon-violet shadow-futuristic rounded-lg overflow-hidden sm:w-full {{ $maxWidth }} sm:mx-auto"
        x-transitionx-bind:enter="transition-all duration-300 ease-in-out"
        x-transitionx-bind:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transitionx-bind:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transitionx-bind:leave="transition-all duration-200 ease-in"
        x-transitionx-bind:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transitionx-bind:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        {{ $slot }}
    </div>
</div>
