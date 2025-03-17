@props([
    'errors' => null,
    'title' => 'Erreur',
    'type' => 'error',
])

@php
// Définir les couleurs en fonction du type d'alerte
$colors = [
    'error' => [
        'border' => 'border-red-500',
        'bg' => 'bg-red-50',
        'text' => 'text-red-800',
        'textContent' => 'text-red-700',
    ],
    'warning' => [
        'border' => 'border-yellow-500',
        'bg' => 'bg-yellow-50',
        'text' => 'text-yellow-800',
        'textContent' => 'text-yellow-700',
    ],
    'success' => [
        'border' => 'border-green-500',
        'bg' => 'bg-green-50',
        'text' => 'text-green-800',
        'textContent' => 'text-green-700',
    ],
];

$currentColors = $colors[$type] ?? $colors['error'];
// Icônes par type d'alerte
    $icons = [
        'error' =>
            '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />',
        'warning' =>
            '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
        'success' =>
            '<path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />',
    ];

    $currentIcon = $icons[$type] ?? $icons['error'];
@endphp

<style>
    .alert-wrapper {
        position: fixed;
        top: 1rem;
        right: 1rem;
        overflow: hidden;
        max-width: 24rem;
        z-index: 9999;
    }

    .alert-container {
        animation: slideIn 0.3s ease-out forwards;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
        }

        to {
            transform: translateX(0);
        }
    }
</style>

@if ($errors && count($errors) > 0)
    <div class="alert-wrapper">
        <div role="alert"
            class="rounded-sm border-s-4 {{ $currentColors['border'] }} {{ $currentColors['bg'] }} p-4 shadow-md alert-container"
            {{ $attributes }}>
            <div class="flex items-center gap-1 {{ $currentColors['text'] }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                    class="w-5 h-5">
                    {!! $currentIcon !!}
                </svg>
                <strong class="block font-medium">{{ $title }}</strong>
            </div>

            @foreach ($errors->all() as $error)
                <p class="mt-2 text-sm {{ $currentColors['textContent'] }}">
                    {{ $error }}
                </p>
            @endforeach
        </div>
    </div>
@endif
