@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <div class="max-w-screen-xl flex mx-auto gap-8 pt-4 min-h-[calc(100vh*1.5)]">
        <!-- Barre latérale -->
        <x-sidebars.menu>
            <x-links.menu href="{{ route('admin.reporting.index') }}" :active="request()->routeIs('admin.reporting.*')">
                <x-icons.reportings class="w-4 h-4" />
                Reportings
            </x-links.menu>
            <x-links.menu href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                <x-icons.users class="w-4 h-4" />
                Utilisateurs
            </x-links.menu>

            <x-links.menu href="{{ route('admin.workers.index') }}" :active="request()->routeIs('admin.workers.*')">
                <x-icons.employees class="w-4 h-4" />
                Salariés
            </x-links.menu>

            <x-links.menu href="{{ route('admin.interims.index') }}" :active="request()->routeIs('admin.interims.*')">
                <x-icons.employees class="w-4 h-4" />
                Intérimaires
            </x-links.menu>

            <x-links.menu href="{{ route('admin.projects.index') }}" :active="request()->routeIs('admin.projects.*')">
                <x-icons.projects class="w-4 h-4" />
                Chantiers
            </x-links.menu>

            <x-links.menu href="{{ route('admin.zones.index') }}" :active="request()->routeIs('admin.zones.*')">
                <x-icons.zones class="w-4 h-4" />
                Zones
            </x-links.menu>

            <x-links.menu href="{{ route('admin.settings.index') }}" :active="request()->routeIs('admin.settings.*')">
                <x-icons.settings class="w-4 h-4" />
                Paramétrages
            </x-links.menu>

            <!-- Autres liens -->
        </x-sidebars.menu>
        <!-- Contenu principal -->
        <div class="flex-1 space-y-24">
            <div class="max-w-screen-xl mx-auto">
                @yield('admin-content')
            </div>
        </div>
    </div>
    @push('scripts')
        <!-- Add your scripts here -->
    @endpush
@endsection
