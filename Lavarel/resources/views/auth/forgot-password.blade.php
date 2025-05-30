{{--
    Forgot Password View
    - Displays password reset request form for users
    - Uses guest layout and component-based inputs
    - All dynamic content is escaped for security
    - KISS: minimal markup
--}}
{{-- DEBUG: Dump route('password.email') for validation (remove after check) --}}
@php // dump(route('password.email')); @endphp
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" x-bind:status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" x-bind:value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" x-bind:value="old('email')" required autofocus />
            <x-input-error x-bind:messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
