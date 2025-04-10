<!-- pages/tracking/index.blade -->
@extends('layouts.app')

@section('title', 'Pointage')

@php
$links = [
['id' => 'tracking', 'text' => 'Saisie des heures'],
['id' => 'main-projects', 'text' => 'Mes Chantiers'],
['id' => 'all-projects', 'text' => 'Tous les Chantiers'],
];
/*$showInfo = false;*/
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
<div class="max-w-screen-xl mx-auto h-full">
    <div class="relative pt-8 pb-24 lg:py-24 flex flex-col lg:flex-row gap-8 lg:gap-16">
        <!-- Contenu principal -->
        <div class="flex-1 order-2 lg:order-1 px-4 lg:px-0">
            <div id="tracking" class="max-w-screen-xl mx-auto mb-16 lg:mb-24">
                <h1 class="text-xl font-bold mb-4"># Saisie des heures</h1>
                <div class="w-full py-6 bg-white shadow-md rounded-lg">
                    <form class="flex flex-col md:flex-row justify-center items-center gap-4 px-4"
                        action="{{ route('tracking.show') }}" method="GET">
                        @csrf
                        <div class="w-full md:w-auto flex flex-col md:flex-row">
                            <!-- Sélecteur de chantier -->
                            <label for="project_id" class="sr-only">Choisir un chantier</label>
                            <select name="project_id" id="project_id"
                                class="w-full md:w-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm md:rounded-l-md focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                                required>
                                <option value="" disabled selected>Choisir un chantier</option>
                                @foreach ($projects as $proj)
                                <option value="{{ $proj->id }}">{{ $proj->code }} - {{ $proj->name }}</option>
                                @endforeach
                            </select>

                            <!-- Sélecteur de mois -->
                            <label for="month" class="sr-only">Choisir un mois</label>
                            <select name="month" id="month"
                                class="w-full md:w-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 mt-2 md:mt-0"
                                required>
                                <option value="" disabled>Choisir un mois</option>
                                @php
                                $months = [
                                1 => 'Janvier',
                                2 => 'Février',
                                3 => 'Mars',
                                4 => 'Avril',
                                5 => 'Mai',
                                6 => 'Juin',
                                7 => 'Juillet',
                                8 => 'Août',
                                9 => 'Septembre',
                                10 => 'Octobre',
                                11 => 'Novembre',
                                12 => 'Décembre',
                                ];
                                $currentMonth = $month ?? date('n');
                                @endphp
                                @foreach ($months as $key => $monthName)
                                <option value="{{ $key }}" {{ $key == $currentMonth ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                                @endforeach
                            </select>

                            <!-- Sélecteur d'année -->
                            <label for="year" class="sr-only">Choisir une année</label>
                            <select name="year" id="year"
                                class="w-full md:w-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm md:rounded-r-md focus:ring-blue-500 focus:border-blue-500 block p-2.5 mt-2 md:mt-0"
                                required>
                                <option value="" disabled>Choisir une année</option>
                                @php
                                $currentYear = $year ?? date('Y');
                                @endphp
                                @for ($y = 2023; $y <= 2030; $y++)
                                    <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                                    @endfor
                            </select>
                        </div>

                        <div class="flex gap-2 mt-4 md:mt-0">
                            <!-- Bouton pour afficher le chantier -->
                            <x-button-dynamic tag="button" type="submit" color="blue" class="w-full md:w-auto">
                                Afficher le pointage
                            </x-button-dynamic>

                            <!-- Champ caché pour la catégorie -->
                            <input type="hidden" name="category" value="day">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mes chantiers -->
            <div id="main-projects" class="max-w-screen-xl mx-auto mb-16 lg:mb-24">
                <h2 class="text-xl font-bold mb-4"># Mes Chantiers</h2>
                <div class="mb-8">
                    <div class="overflow-hidden shadow-md sm:rounded-lg">
                        <div class="bg-white shadow rounded">
                            <livewire:user-projects-table />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tous les chantiers -->
            <div id="all-projects" class="max-w-screen-xl mx-auto mb-16 lg:mb-24">
                <h2 class="text-xl font-bold mb-4"># Tous les Chantiers</h2>
                <div class="mb-8">
                    <div class="overflow-hidden shadow-md sm:rounded-lg">
                        <div class="bg-white shadow rounded">
                            <livewire:all-projects-table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin contenu principal -->

        <!-- Barre d'info latérale - visible uniquement sur desktop -->
        <div class="hidden lg:block lg:w-64 order-1 lg:order-2">
            <div class="lg:sticky lg:top-24">
                <x-info-nav :links="$links" title="Sur cette page" showInfo="true" infoTitle="Informations">
                    <x-slot:info>
                        <p>Cliquez sur "{!! $trackingIcon !!}" pour aller vers la page de pointage.</p>
                        <p>Cliquez sur "{!! $downloadIcon !!}" pour télécharger une feuille de pointage vierge.</p>
                    </x-slot:info>
                </x-info-nav>
            </div>
        </div>
    </div>
</div>
@endsection