@extends('layouts.app')

@section('title', 'Pointage')

@php
    $links = [['id' => 'tracking', 'text' => 'Saisie des heures']];
    $showInfo = false;
@endphp

@section('content')
    <div class="max-w-screen-xl flex mx-auto gap-16 pt-24 h-full">
        <!-- Contenu principal -->
        <div class="flex-1">
            <div id="tracking" class="max-w-screen-xl mx-auto mb-24">
                <h1 class="text-xl font-bold mb-4"># Saisie des heures</h1>
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 bg-white shadow-md rounded-lg">
                    <form class="flex flex-col md:flex-row justify-center items-center gap-4"
                        action="{{ route('tracking.show') }}" method="GET">
                        @csrf
                        <div class="flex flex-col md:flex-row w-full md:w-auto">
                            <!-- Sélecteur de chantier -->
                            <label for="project_id" class="sr-only">Choisir un chantier</label>
                            <select name="project_id" id="project_id"
                                class="w-full md:w-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm md:rounded-l-md focus:ring-blue-500 focus:border-blue-500 block"
                                required>
                                <option value="" disabled selected>Choisir un chantier</option>
                                @foreach ($projects as $proj)
                                    <option value="{{ $proj->id }}">{{ $proj->code }} - {{ $proj->name }}</option>
                                @endforeach
                            </select>

                            <!-- Sélecteur de mois -->
                            <label for="month" class="sr-only">Choisir un mois</label>
                            <select name="month" id="month"
                                class="w-full md:w-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block"
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
                                        {{ $monthName }}</option>
                                @endforeach
                            </select>

                            <!-- Sélecteur d'année -->
                            <label for="year" class="sr-only">Choisir une année</label>
                            <select name="year" id="year"
                                class="w-full md:w-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm md:rounded-r-md focus:ring-blue-500 focus:border-blue-500 block"
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
                            <x-buttons.dynamic tag="button" type="submit" color="blue">
                                Afficher le pointage
                            </x-buttons.dynamic>

                            <!-- Champ caché pour la catégorie -->
                            <input type="hidden" name="category" value="day">
                        </div>
                    </form>
                </div>
            </div>
            {{-- ajouter ici le filtre pour selectionner un user->driver et afficher ces chantier --}}
        </div>
        <!-- Fin contenu principal -->
        <x-sidebars.nav :links="$links" title="Sur cette page" />
    </div>
@endsection
