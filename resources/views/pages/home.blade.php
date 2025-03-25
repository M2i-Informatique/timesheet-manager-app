@extends('layouts.app')

@section('title', 'Accueil')

@php
    $links = [
        ['id' => 'main-projects', 'text' => 'Mes Chantiers'],
        ['id' => 'all-projects', 'text' => 'Tous les Chantiers'],
    ];

    $trackingIcon = '<svg class="w-4 h-4 inline-block align-center" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>';

    $downloadIcon = '<svg class="w-4 h-4 inline-block align-center" xmlns="http://www.w3.org/2000/svg"
    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round"
        d="m9 13.5 3 3m0 0 3-3m-3 3v-6m1.06-4.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
    </svg>';
@endphp

@section('content')
    <div class="max-w-screen-xl flex mx-auto gap-8 pt-24">
        <!-- Contenu principal -->
        <div class="flex-1">
            <!-- Mes chantiers -->
            <div id="main-projects" class="max-w-screen-xl mx-auto mb-24">
                <h2 class="text-xl font-bold mb-4"># Mes Chantiers</h2>
                <div class="mb-8">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div class="bg-white shadow rounded">
                            <livewire:user-projects-table />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tous les chantiers -->
            <div id="all-projects" class="max-w-screen-xl mx-auto mb-24">
                <h2 class="text-xl font-bold mb-4"># Tous les Chantiers</h2>
                <div class="mb-8">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div class="bg-white shadow rounded">
                            <livewire:all-projects-table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin contenu principal -->
        <x-sidebars.nav :links="$links" title="Sur cette page" showInfo="true" infoTitle="Informations">
            <x-slot:info>
                <p>Cliquez sur "{!! $trackingIcon !!}" pour aller vers la page de pointage.</p>
                <p>Cliquez sur "{!! $downloadIcon !!}" pour télécharger une feuille de pointage vierge.</p>
            </x-slot:info>
        </x-sidebars.nav>
    </div>
@endsection
