<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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

<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <livewire:components.navigation />

        <!-- En-tête de page -->
        @hasSection('header')
            <header>
                <div>
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Contenu principal -->
        <main>
            @yield('content')
        </main>

    </div>

    <!-- Stack pour tous les scripts additionnels -->
    @stack('scripts')

    @livewireScripts
</body>

</html>
