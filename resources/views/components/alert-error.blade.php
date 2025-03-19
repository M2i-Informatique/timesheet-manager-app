@props([
    'messages' => null,
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
            '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />',
        'warning' =>
            '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />',
        'success' =>
            '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
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

@if ($messages)
    <div class="alert-wrapper">
        <div role="alert"
            class="rounded-sm border-s-4 {{ $currentColors['border'] }} {{ $currentColors['bg'] }} p-4 shadow-md alert-container"
            {{ $attributes }}>
            <div class="flex items-center gap-1 {{ $currentColors['text'] }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    stroke-width="1.5" class="w-4 h-4">
                    {!! $currentIcon !!}
                </svg>
                <strong class="block font-medium">{{ $title }}</strong>
            </div>

            @if (is_array($messages) || is_object($messages))
                @foreach (is_object($messages) && method_exists($messages, 'all') ? $messages->all() : (array) $messages as $message)
                    <p class="mt-2 text-sm {{ $currentColors['textContent'] }}">
                        {{ is_array($message) ? implode(', ', $message) : $message }}
                    </p>
                @endforeach
            @else
                <p class="mt-2 text-sm {{ $currentColors['textContent'] }}">
                    {{ $messages }}
                </p>
            @endif
        </div>
    </div>
@endif
