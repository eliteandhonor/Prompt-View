{{--
    Auth Session Status Component
    - Displays a session status message on authentication pages
    - All dynamic content is escaped for security
    - KISS: minimal markup
--}}
{{-- DEBUG: Dump $status for validation (remove after check) --}}
@php // dump($status); @endphp
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
