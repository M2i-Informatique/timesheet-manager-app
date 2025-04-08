@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="max-w-screen-xl flex mx-auto gap-8 pt-4">
    <!-- Barre latérale -->
    <x-sidebars.menu>
        @if ((auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin')))
        <x-links.menu href="{{ route('admin.reporting.index') }}" :active="request()->routeIs('admin.reporting.*')">
            <x-icons.chart class="w-4 h-4" />
            Tableau de bord
        </x-links.menu>
        @endif
        @if ((auth()->user()->hasRole('super-admin')))
        <x-links.menu href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
            <x-icons.users class="w-4 h-4" />
            Utilisateurs
        </x-links.menu>
        @endif

        @if ((auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')))
        <x-links.menu href="{{ route('admin.workers.index') }}" :active="request()->routeIs('admin.workers.*')">
            <x-icons.employee class="w-4 h-4" />
            Salariés
        </x-links.menu>

        <x-links.menu href="{{ route('admin.interims.index') }}" :active="request()->routeIs('admin.interims.*')">
            <x-icons.employee class="w-4 h-4" />
            Intérimaires
        </x-links.menu>

        <x-links.menu href="{{ route('admin.projects.index') }}" :active="request()->routeIs('admin.projects.*')">
            <x-icons.electrical class="w-4 h-4" />
            Chantiers
        </x-links.menu>

        <x-links.menu href="{{ route('admin.zones.index') }}" :active="request()->routeIs('admin.zones.*')">
            <x-icons.map class="w-4 h-4" />
            Zones
        </x-links.menu>

        <x-links.menu href="{{ route('admin.non-working-days.index') }}" :active="request()->routeIs('admin.non-working-days.*')">
            <x-icons.calendar class="w-4 h-4" />
            Jours de fermeture
        </x-links.menu>

        <x-links.menu href="{{ route('admin.exports.index') }}" :active="request()->routeIs('admin.exports.*')">
            <x-icons.export class="w-4 h-4" />
            Exporter
        </x-links.menu>

        <x-links.menu href="{{ route('admin.settings.index') }}" :active="request()->routeIs('admin.settings.*')">
            <x-icons.setting class="w-4 h-4" />
            Paramétrages
        </x-links.menu>
        @endif

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