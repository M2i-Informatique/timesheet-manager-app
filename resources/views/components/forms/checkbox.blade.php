@props([
    'id' => null,
    'name' => null,
    'label' => '',
    'checked' => false,
    'disabled' => false,
    'error' => false,
    'labelPosition' => 'right', // Options: 'right', 'left'
])

@php
    $id = $id ?? $name;
    $checkboxClasses = 'h-4 w-4 text-blue-500 focus:ring-blue-500 border-gray-300 rounded';
    $labelClasses = 'block text-sm text-gray-900';
    $wrapperClasses = 'flex items-center';

    // Ajout des classes conditionnelles en fonction des erreurs
    if ($error) {
        $checkboxClasses .= ' border-red-500';
        $labelClasses .= ' text-red-500';
    }

    // Ajout des classes conditionnelles en fonction de l'état désactivé
if ($disabled) {
    $checkboxClasses .= ' opacity-60 cursor-not-allowed';
    $labelClasses .= ' opacity-60';
}

// Ajustement des marges en fonction de la position du label
$labelSpacing = $labelPosition === 'right' ? 'ml-2' : 'mr-2';
$labelClasses .= ' ' . $labelSpacing;

// Ordre des éléments en fonction de la position du label
$orderFirst = $labelPosition === 'left' ? 'order-first' : '';
@endphp

<div class="">
    <div class="{{ $wrapperClasses }}">
        @if ($labelPosition === 'left')
            <label for="{{ $id }}" class="{{ $labelClasses }} {{ $orderFirst }}">
                {{ $label }}
            </label>
        @endif

        <input type="checkbox" id="{{ $id }}" name="{{ $name }}" {{ $checked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $checkboxClasses]) }}>

        @if ($labelPosition === 'right')
            <label for="{{ $id }}" class="{{ $labelClasses }}">
                {{ $label }}
            </label>
        @endif
    </div>

    @if ($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif

    {{ $slot ?? '' }}
</div>
