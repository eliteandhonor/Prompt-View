{{--
    Dropdown Link Component
    - Renders a link inside a dropdown menu
    - All dynamic content is escaped for security
    - KISS: minimal markup
--}}
<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out']) }}>{{ $slot }}</a>
