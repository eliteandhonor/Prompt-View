@props([
    'type' => 'success', // 'success' or 'error'
    'message' => '',
    'ariaLive' => null,
    'extraClass' => ''
])
@php
    $isSuccess = $type === 'success';
    $bg = $isSuccess ? 'bg-green-700 border-green-400/50 shadow-green-900/30' : 'bg-red-700 border-red-400/50 shadow-red-900/30';
    $ariaLive = $ariaLive ?? ($isSuccess ? 'polite' : 'assertive');
@endphp

@if(trim($message) !== '')
<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 3000)"
    x-show="show"
    x-transitionx-bind:enter="transition-all duration-300 ease-in-out"
    x-transitionx-bind:enter-start="opacity-0 scale-90 translate-x-8"
    x-transitionx-bind:enter-end="opacity-100 scale-100 translate-x-0"
    x-transitionx-bind:leave="transition-all duration-200 ease-in"
    x-transitionx-bind:leave-start="opacity-100 scale-100 translate-x-0"
    x-transitionx-bind:leave-end="opacity-0 scale-90 translate-x-8"
    class="toast fixed bottom-6 right-6 z-40 pointer-events-auto flex items-center gap-3 font-sans font-semibold text-base sm:text-lg {{ $bg }} border-2 px-4 py-2 rounded-lg shadow-lg max-w-[300px] {{ $extraClass }}"
    role="alert"
    x-bind:aria-live="'{{ $ariaLive }}'"
    tabindex="0"
>
    @if($isSuccess)
        <svg class="w-6 h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
    @else
        <svg class="w-6 h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    @endif
    <span class="break-words flex-1">
        {!! $message !!}
    </span>
    <button
        @click="show = false"
        class="ml-2 text-white/70 hover:text-white focus:outline-none focus:ring-2 focus:ring-white rounded-full transition flex-shrink-0"
        aria-label="Close notification"
        type="button"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
@endif