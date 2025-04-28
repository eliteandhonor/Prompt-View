{{--
    Confirm Password View
    - Prompts the user to confirm their password before accessing secure areas
    - Uses guest layout and component-based inputs
    - All dynamic content is escaped for security
    - KISS: minimal markup
--}}
{{-- DEBUG: Dump route('password.confirm') for validation (remove after check) --}}
@php // dump(route('password.confirm')); @endphp
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" x-bind:value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error x-bind:messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
