@props([
    'type' => 'text',
    'id' => null,
    'name' => null,
    'label' => 'Label',
    'value' => '',
    'placeholder' => ' ',
    'required' => false,
    'autocomplete' => null,
    'disabled' => false,
    'readonly' => false,
    'error' => false,
    'inputClass' => '', // Nouvelle propriété pour les classes spécifiques à l'input
    'labelClass' => '', // Nouvelle propriété pour les classes spécifiques au label
])

@php
    $id = $id ?? $name;
    $inputClasses =
        'block p-2.5 w-full text-sm text-gray-900 bg-transparent rounded-lg border appearance-none focus:outline-none focus:ring-0 peer ' .
        $inputClass;
    $labelClasses =
        'absolute text-sm duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] px-2 peer-focus:px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-2 ' .
        $labelClass;

    // Ajout des classes conditionnelles en fonction des erreurs
    if ($error) {
        $inputClasses .= ' border-red-500 focus:border-red-500';
        $labelClasses .= ' text-red-500 peer-focus:text-red-500';
    } else {
        $inputClasses .= ' border-gray-300 focus:border-blue-500';
        $labelClasses .= ' text-gray-500 peer-focus:text-blue-500';
    }

    // Ajout des classes conditionnelles en fonction de l'état désactivé
if ($disabled) {
    $inputClasses .= ' opacity-60 cursor-not-allowed';
    }
@endphp

<div class="relative">
    <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
        placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }} {{ $autocomplete ? "autocomplete=$autocomplete" : '' }}
        {{ $attributes->merge(['class' => $inputClasses]) }}>
    <label for="{{ $id }}" class="{{ $labelClasses }}">
        {{ $label }}
    </label>

    {{ $slot ?? '' }}
</div>
