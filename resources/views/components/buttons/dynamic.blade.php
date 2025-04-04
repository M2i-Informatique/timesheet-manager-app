@props([
    'tag' => '',
    'type' => '',
    'color' => '',
    'href' => '',
    'route' => '',
    'routeParams' => []
])

@php
    $baseClasses = "inline-block px-5 py-1.5 border rounded-sm text-sm leading-normal transition duration-150 ease-in-out cursor-pointer";

    $colorClasses = [
        'blue' => "border-blue-400 hover:border-blue-500 text-blue-400 hover:text-blue-500 hover:bg-blue-50",
        'red' => "border-red-400 hover:border-red-500 text-red-400 hover:text-red-500 hover:bg-red-50",
        'green' => "border-green-400 hover:border-green-500 text-green-400 hover:text-green-500 hover:bg-green-50",
        'gray' => "border-gray-400 hover:border-gray-500 text-gray-400 hover:text-gray-500 hover:bg-gray-50",
        'purple' => "border-purple-400 hover:border-purple-500 text-purple-400 hover:text-purple-500 hover:bg-purple-50"
    ];

    $classes = $baseClasses . ' ' . ($colorClasses[$color] ?? $colorClasses['blue']);

    if ($route) {
        $href = route($route, $routeParams);
    }
@endphp

@if ($tag === 'a')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
