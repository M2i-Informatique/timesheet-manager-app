<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '') }} - @yield('title', 'Administration')</title>

    @livewireStyles

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gray-50">
    <!-- Sidebar fixe à gauche -->
    <x-admin-sidebar />

    <!-- Conteneur principal avec marge égale à la largeur du sidebar -->
    <div class="ml-64">
        <main class="p-6 min-h-screen">
            <!-- Titre de page (optionnel) -->
            @hasSection('page-title')
            <div class="flex justify-between mb-6 container mx-auto">
                <h1 class="text-2xl font-bold text-gray-900">@yield('page-title')</h1>
                @if(isset($showBackButton) && $showBackButton)
                <x-button-dynamic tag="a"
                    route="admin.reporting.index"
                    routeParams="['view' => 'dashboard']"
                    color="blue"
                    class="rounded-lg shadow-sm hover:shadow transition-all duration-200">
                    Retour
                </x-button-dynamic>
                @endif
            </div>
            @endif

            <!-- Contenu spécifique à chaque page -->
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    @livewireScripts
</body>

</html>