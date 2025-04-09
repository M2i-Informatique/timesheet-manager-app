<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '') }} - @yield('title', '')</title>

    @livewireStyles

    <!-- Fonts -->

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex flex-col h-full min-h-screen">
    <!-- Navigation -->
    <livewire:components.navigation />

    <!-- En-tête de page -->
    @hasSection('header')
    <header>
        @yield('header')
    </header>
    @endif

    <!-- Contenu principal -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Pied de page -->
    @include('partials.footer')

    <!-- Messages flash -->
    {{-- <livewire:components.flash-messages /> --}}

    <!-- Stack pour tous les scripts additionnels -->
    @stack('scripts')

    @livewireScripts
</body>

</html>