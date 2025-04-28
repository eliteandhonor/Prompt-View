{{--
    Input Label Component
    - Renders a form label for input fields
    - All dynamic content is escaped for security
    - KISS: minimal markup
--}}
{{-- DEBUG: Dump $value for validation (remove after check) --}}
@php // dump($value); @endphp
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
