{{--
    Login View
    - Displays user login form with remember me option and password reset link
    - Uses guest layout and component-based inputs
    - All dynamic content is escaped for security
    - KISS: minimal markup
--}}
{{-- DEBUG: Dump route('login') for validation (remove after check) --}}
@php // dump(route('login')); @endphp
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" x-bind:status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" x-bind:value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" x-bind:value="old('email')" required autofocus autocomplete="username" />
            <x-input-error x-bind:messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" x-bind:value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error x-bind:messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
