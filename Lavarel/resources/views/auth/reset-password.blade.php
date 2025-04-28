{{--
    Reset Password View
    - Displays password reset form for users with token and validation
    - Uses guest layout and component-based inputs
    - All dynamic content is escaped for security
    - KISS: minimal markup
--}}
{{-- DEBUG: Dump route('password.store') for validation (remove after check) --}}
@php // dump(route('password.store')); @endphp
<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" x-bind:value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" x-bind:value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error x-bind:messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" x-bind:value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error x-bind:messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" x-bind:value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error x-bind:messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
