@props(['active' => false, 'icon' => null])

@php
    $classes =
        $active ?? false
            ? 'border-blue-500 text-blue-500 bg-blue-100'
            : 'text-gray-700 bg-gray-200 hover:text-blue-500 hover:bg-blue-100 border-transparent';

    $classes .= ' ' . 'flex items-center gap-2 text-sm rounded-sm px-5 py-1.5 transition-colors duration-200 border';

@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
