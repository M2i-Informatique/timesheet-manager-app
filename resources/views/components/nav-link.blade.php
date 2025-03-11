@props(['active' => false, 'icon' => null])

@php
    $classes =
        $active ?? false
            ? 'inline-flex items-center px-1 h-full text-sm font-medium leading-5 text-gray-600 md:text-blue-600 md:dark:text-blue-600 focus:outline-none transition duration-150 ease-in-out relative'
            : 'inline-flex items-center px-1 h-full text-sm font-medium leading-5 text-gray-600 hover:text-gray-700 focus:outline-none focus:text-blue-700 transition duration-150 ease-in-out relative';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <span class="inline-block mr-2">
            <i class="{{ $icon }}"></i>
        </span>
    @endif
    {{ $slot }}
</a>
