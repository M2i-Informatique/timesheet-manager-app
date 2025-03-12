@props(['active' => false])

@php
    $classes =
        $active ?? false
            ? 'md:text-blue-600 md:dark:text-blue-600 focus:outline-none'
            : 'hover:text-gray-700 focus:outline-none focus:text-blue-700';

    $classes .= ' ' . 'inline-flex items-center px-1 h-full text-sm font-medium leading-5 text-gray-600 transition duration-150 ease-in-out relative';

@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
