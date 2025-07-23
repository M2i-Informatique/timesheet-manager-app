<!-- resources/views/pages/tracking/show.blade.php -->

@extends('layouts.app')

@section('title', 'Saisie des heures')

@section('header')
<div class="max-w-7xl mx-auto flex justify-between items-center mb-6 px-4">
    <div class="relative pt-8 lg:pt-24">
        <h1 class="text-2xl flex items-center flex-wrap">
            <a class="hover:text-blue-600 hover:underline font-bold transition-colors duration-200" href="{{ route('tracking.index') }}">Pointage</a>
            <span class="mx-2 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </span>
            <span class="font-bold text-customColor">{{ $project->code }} - {{ $project->name }} - {{ $project->city }}</span>
        </h1>
    </div>
</div>
@endsection

@section('content')
<div class="pb-32 px-4 lg:px-0">
    {{-- Message de succès --}}
    @if (session('success'))
    <div class="mb-6 p-4 bg-green-50 text-green-800 border-l-4 border-green-500 rounded-lg shadow-md flex items-start">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Section d'informations avec légende -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Colonne d'informations importantes -->
        <div class="bg-white rounded-xl shadow-lg p-5 border border-gray-100 transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center gap-2 mb-4 font-bold text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="w-5 h-5 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <span>Informations importantes</span>
            </div>
            <div class="p-4 rounded-lg bg-blue-50">
                <ul class="space-y-4">
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-6 h-6 mt-0.5 rounded-full bg-blue-500 flex items-center justify-center text-white">
                            <span class="text-xs font-bold">1</span>
                        </div>
                        <span class="font-medium text-blue-800 leading-snug">
                            Veuillez mettre « 0 » pour marquer un salarié absent.
                        </span>
                    </li>

                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-6 h-6 mt-0.5 rounded-full bg-red-500 flex items-center justify-center text-white">
                            <span class="text-xs font-bold">2</span>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <span class="font-medium text-red-800 leading-snug">
                                Avant de détacher un salarié, assurez-vous d'avoir supprimé toutes ses heures.
                            </span>
                            <span class="font-medium text-red-800 leading-snug">
                                Faites un clic droit sur la ligne de l'employé pour le détacher du chantier.
                            </span>
                            <span class="font-medium text-red-800 leading-snug">
                                Attention, cette action est irréversible.
                            </span>
                        </div>
                    </li>

                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-6 h-6 mt-0.5 rounded-full bg-yellow-500 flex items-center justify-center text-white">
                            <span class="text-xs font-bold">3</span>
                        </div>
                        <span class="font-medium text-yellow-800 leading-snug">
                            Sauvegarder toutes vos modifications avant de quitter cette page.
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Légende des jours non travaillés -->
        <div class="bg-white rounded-xl shadow-lg p-5 border border-gray-100 transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center gap-2 mb-4 font-bold text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="w-5 h-5 text-orange-600">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227
                             1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1
                             .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394
                             48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                <span>Légende des jours non travaillés</span>
            </div>
            <div class="p-4 rounded-lg bg-orange-50">
                <ul class="space-y-4">
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-3 py-1 rounded-lg bg-orange-100 flex items-center justify-center font-bold text-orange-800 text-xs uppercase">
                            FER
                        </div>
                        <span class="font-medium text-gray-800">Jour férié</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-3 py-1 rounded-lg bg-orange-100 flex items-center justify-center font-bold text-orange-800 text-xs uppercase">
                            RTT
                        </div>
                        <span class="font-medium text-gray-800">RTT Imposé</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-3 py-1 rounded-lg bg-orange-100 flex items-center justify-center font-bold text-orange-800 text-xs uppercase">
                            FRM
                        </div>
                        <span class="font-medium text-gray-800">Fermeture entreprise</span>
                    </li>
                </ul>
                <div class="mt-4 p-3 bg-white rounded-lg border border-orange-100 text-sm text-gray-600">
                    <div class="flex items-center">
                        <span>
                            Les jours non travaillés sont affichés en orange dans le tableau. La saisie d'heures pendant ces jours sera également affichée en orange.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Légende des congés salariés -->
        <div class="bg-white rounded-xl shadow-lg p-5 border border-gray-100 transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center gap-2 mb-4 font-bold text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="w-5 h-5 text-green-600">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5M3 16.5l7.5-7.5 7.5 7.5" />
                </svg>
                <span>Légende des congés salariés</span>
            </div>
            <div class="p-4 rounded-lg bg-green-50">
                <ul class="space-y-2">
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-2 py-1 rounded text-white font-bold text-xs" style="background-color: #006400;">CP</div>
                        <span class="font-medium text-gray-800 text-sm">Congés payés</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-2 py-1 rounded text-white font-bold text-xs" style="background-color: #0066CC;">RTT</div>
                        <span class="font-medium text-gray-800 text-sm">RTT</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-2 py-1 rounded text-white font-bold text-xs" style="background-color: #FF8C00;">CSP</div>
                        <span class="font-medium text-gray-800 text-sm">Congé sans solde</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-2 py-1 rounded text-white font-bold text-xs" style="background-color: #DC143C;">AM</div>
                        <span class="font-medium text-gray-800 text-sm">Arrêt maladie/accident</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-2 py-1 rounded text-white font-bold text-xs" style="background-color: #DC143C;">AI</div>
                        <span class="font-medium text-gray-800 text-sm">Attestation isolation</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-2 py-1 rounded text-white font-bold text-xs" style="background-color: #FFB6C1;">PM</div>
                        <span class="font-medium text-gray-800 text-sm">Congé paternité/maternité</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-2 py-1 rounded text-white font-bold text-xs" style="background-color: #90EE90;">AP</div>
                        <span class="font-medium text-gray-800 text-sm">Activité partielle</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 px-2 py-1 rounded text-white font-bold text-xs" style="background-color: #90EE90;">INT</div>
                        <span class="font-medium text-gray-800 text-sm">Intempéries</span>
                    </li>
                </ul>
                <div class="mt-4 p-3 bg-white rounded-lg border border-green-100 text-sm text-gray-600">
                    <div class="flex items-center">
                        <span>
                            Les congés sont affichés avec leur code et couleur spécifique. Les cellules en congé sont en lecture seule.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sélecteur mobile avec Tailwind uniquement --}}
    {{-- Sélecteur de période responsive optimisé --}}
    <div class="bg-white p-5 rounded-xl shadow-sm mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Titre et navigation par mois -->
            <div class="flex flex-col items-start">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Période de pointage</h2>

                <!-- Navigation mois -->
                <div class="flex w-full rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                    <!-- Bouton Précédent -->
                    <a href="{{ route('tracking.show', [
                    'project_id' => $project->id,
                    'month' => $prevMonth,
                    'year' => $prevYear,
                    'category' => $category,
                ]) }}"
                        class="flex-1 md:flex-none flex items-center justify-center py-3 md:py-2.5 px-2 md:px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:text-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-4 md:w-4 md:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="hidden md:inline">Précédent</span>
                    </a>

                    <!-- Mois courant -->
                    <div class="flex-1 md:flex-none flex items-center justify-center py-3 md:py-2.5 px-4 md:px-5 text-sm font-medium text-blue-700 bg-blue-50 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ \Carbon\Carbon::create($year, $month, 1)->locale('fr')->translatedFormat('F Y') }}</span>
                    </div>

                    <!-- Bouton Suivant -->
                    <a href="{{ route('tracking.show', [
                    'project_id' => $project->id,
                    'month' => $nextMonth,
                    'year' => $nextYear,
                    'category' => $category,
                ]) }}"
                        class="flex-1 md:flex-none flex items-center justify-center py-3 md:py-2.5 px-2 md:px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:text-blue-700">
                        <span class="hidden md:inline">Suivant</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-4 md:w-4 md:ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Sélecteur Jour/Nuit -->
            <div class="grid grid-cols-2 gap-3 md:flex md:gap-0">
                <!-- Bouton Jour -->
                <a href="{{ route('tracking.show', [
                'project_id' => $project->id,
                'month' => $month,
                'year' => $year,
                'category' => 'day',
            ]) }}"
                    class="flex items-center justify-center gap-2 py-3 md:py-2.5 px-4 md:px-5 rounded-lg md:rounded-l-lg md:rounded-r-none text-sm font-medium {{ $category === 'day' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="md:hidden">Jour</span>
                    <span class="hidden md:inline">Heures de jour</span>
                </a>

                <!-- Bouton Nuit -->
                <a href="{{ route('tracking.show', [
                'project_id' => $project->id,
                'month' => $month,
                'year' => $year,
                'category' => 'night',
            ]) }}"
                    class="flex items-center justify-center gap-2 py-3 md:py-2.5 px-4 md:px-5 rounded-lg md:rounded-l-none md:rounded-r-lg text-sm font-medium {{ $category === 'night' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="md:hidden">Nuit</span>
                    <span class="hidden md:inline">Heures de nuit</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Handsontable container avec titre --}}
    <div class="bg-white p-5 rounded-xl shadow-lg mb-8 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Tableau de saisie des heures</span>
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category === 'day' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ $category === 'day' ? 'Jour' : 'Nuit' }}
                </span>
            </h2>
        </div>
        <div id="handsontable-wrapper" class="border border-gray-200 overflow-hidden">
            <div id="handsontable" class="w-full min-h-1/2 bg-white"></div>
        </div>
    </div>

    {{-- Bouton Enregistrer + formulaires "Ajouter un salarié" --}}
    <div class="mb-6">
        <!-- Bouton "Enregistrer" en haut -->
        <div class="flex justify-center mb-6">
            <button id="saveBtn" class="flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Enregistrer les modifications</span>
            </button>
        </div>

        <!-- Formulaires d'ajout -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Formulaire Salariés -->
            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Ajouter un salarié Dubocq</h3>
                <form action="{{ route('tracking.assignEmployee') }}" method="POST" class="flex flex-col gap-3">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <input type="hidden" name="employee_type" value="worker">

                    <select name="employee_id" class="w-full border-gray-300 rounded-lg py-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">-- Sélectionner un salarié --</option>
                        @foreach ($availableWorkers as $w)
                        <option value="{{ $w->id }}">
                            {{ $w->last_name }} {{ $w->first_name }}
                        </option>
                        @endforeach
                    </select>

                    <button type="submit" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Ajouter salarié</span>
                    </button>
                </form>
            </div>

            <!-- Formulaire Intérim -->
            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Ajouter un intérimaire</h3>
                <form action="{{ route('tracking.assignEmployee') }}" method="POST" class="flex flex-col gap-3">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <input type="hidden" name="employee_type" value="interim">

                    <select name="employee_id" class="w-full border-gray-300 rounded-lg py-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">-- Sélectionner une agence --</option>
                        @foreach ($availableInterims as $i)
                        <option value="{{ $i->id }}">
                            {{ $i->agency }} (Intérim)
                        </option>
                        @endforeach
                    </select>

                    <button type="submit" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Ajouter intérimaire</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tableau récapitulatif --}}
    @if (!empty($recap))
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:justify-between gap-1 p-5 text-left rtl:text-right bg-blue-50">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-800">Récapitulatif des heures</h2>
            </div>
        </div>

        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-blue-50">
                    <tr>
                        <th class="px-6 py-3 whitespace-nowrap">Salarié</th>
                        <th class="px-6 py-3 whitespace-nowrap">Heures Jour</th>
                        <th class="px-6 py-3 whitespace-nowrap">Heures Nuit</th>
                        <th class="px-6 py-3 whitespace-nowrap">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $hasData = false; @endphp
                    @foreach ($recap as $r)
                    @if ($r['total'] > 0)
                    @php $hasData = true; @endphp
                    <tr class="bg-white hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">
                            {{ $r['full_name'] }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $r['day_hours'] ?: 0 }} h
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $r['night_hours'] ?: 0 }} h
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-700 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $r['total'] }} h
                            </span>
                        </td>
                    </tr>
                    @endif
                    @endforeach

                    @if (!$hasData)
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 whitespace-nowrap">
                            Aucune donnée disponible pour la période sélectionnée.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Bloc KPI (récap global) --}}
    @if(!empty($recap))
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <!-- KPI: Salariés -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-blue-100 transition-all duration-300 hover:shadow-xl hover:border-blue-300">
            <div class="p-4 border-b border-blue-200">
                <h3 class="text-sm font-semibold text-blue-700">Salariés Dubocq</h3>
            </div>
            <div class="p-6">
                <div class="flex items-end justify-between">
                    <p class="text-3xl font-bold text-blue-600">
                        {{ number_format($totalWorkerHoursCurrentMonth, 2, ',', ' ') }} h
                    </p>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $totalHoursCurrentMonth > 0
                                ? number_format(($totalWorkerHoursCurrentMonth / $totalHoursCurrentMonth) * 100, 1)
                                : 0 }}%
                        </span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-blue-100">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Heures par les employés</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI: Intérims -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-green-100 transition-all duration-300 hover:shadow-xl hover:border-green-300">
            <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                <h3 class="text-sm font-semibold text-green-700">Intérims</h3>
            </div>
            <div class="p-6">
                <div class="flex items-end justify-between">
                    <p class="text-3xl font-bold text-green-600">
                        {{ number_format($totalInterimHoursCurrentMonth, 2, ',', ' ') }} h
                    </p>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $totalHoursCurrentMonth > 0
                                ? number_format(($totalInterimHoursCurrentMonth / $totalHoursCurrentMonth) * 100, 1)
                                : 0 }}%
                        </span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-green-100">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Heures par les intérimaires</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI: Total heures travaillées -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-indigo-100 transition-all duration-300 hover:shadow-xl hover:border-indigo-300">
            <div class="p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
                <h3 class="text-sm font-semibold text-indigo-700">
                    Total des heures
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-end justify-between">
                    <p class="text-3xl font-bold text-indigo-600">
                        {{ number_format($totalHoursCurrentMonth, 2, ',', ' ') }} h
                    </p>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            100%
                        </span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-indigo-100">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ \Carbon\Carbon::create($year, $month, 1)->locale('fr')->translatedFormat('F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI: Coût chantier (Workers uniquement) -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-green-100 transition-all duration-300 hover:shadow-xl hover:border-green-300">
            <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                <h3 class="text-sm font-semibold text-green-700">Coût du chantier (salariés)</h3>
            </div>
            <div class="p-6">
                <div class="flex items-end justify-between">
                    <p class="text-3xl font-bold text-green-600">
                        {{ number_format($costWorkerTotal, 2, ',', ' ') }} €
                    </p>
                    <!-- <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $totalWorkerHoursCurrentMonth > 0 ? number_format($costWorkerTotal / $totalWorkerHoursCurrentMonth, 2, ',', ' ') : '0,00' }} €/h
                        </span>
                    </div> -->
                </div>
                <div class="mt-4 pt-4 border-t border-green-100">
                    <div class="flex items-center gap-2 text-sm text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-green-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 7.756a4.5 4.5 0 1 0 0 8.488M7.5 10.5h5.25m-5.25 3h5.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Coût total du chantier pour les salariés Dubocq</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @endif
</div>
@endsection

@push('scripts')
<style>
    /* Styles personnalisés pour Handsontable */
    #handsontable-wrapper {
        position: relative;
    }

    #handsontable {
        max-height: 600px;
        overflow: auto;
    }

    .handsontable .htDimmed {
        color: #333 !important;
        font-weight: 600;
    }

    .handsontable .current {
        background-color: rgba(58, 134, 255, 0.1) !important;
    }

    .handsontable td.weekend {
        background-color: #f0f0f0;
    }

    .handsontable td.absent {
        background-color: #FFCCCC !important;
        font-weight: bold;
    }

    .handsontable td.day-hours {
        background-color: #CCFFCC !important;
    }

    .handsontable td.night-hours {
        background-color: #E9D5FF !important;
    }

    .handsontable td.non-working {
        background-color: #FFE0B2 !important;
        font-weight: bold;
    }

    .handsontable td.non-working-empty {
        background-color: #FFE4B5 !important;
        font-style: italic;
        font-weight: bold;
    }

    /* Animations */
    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    /* Styles pour le menu contextuel */
    .handsontable .htContextMenu {
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .handsontable .htContextMenu .ht_master .wtHolder {
        background-color: white;
    }

    .handsontable .htContextMenu table tbody tr td.htItemWrapper {
        padding: 6px 12px;
    }

    .handsontable .htContextMenu table tbody tr td.htItemWrapper:hover {
        background-color: #F1F5FF;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let projectId = @json($project->id);
        let month = @json($month);
        let year = @json($year);
        let category = @json($category); // "day" ou "night"
        let daysInMonth = @json($daysInMonth);
        let entriesData = @json($entriesData);
        let nonWorkingDays = @json($nonWorkingDays ?? []);
        let nonWorkingDayTypes = @json($nonWorkingDayTypes ?? []);
        let workerLeaveDays = @json($workerLeaveDays ?? []);
        let workerLeaveTypes = @json($workerLeaveTypes ?? []);

        function getTypeAbbreviation(type) {
            switch (type) {
                case 'Férié':
                    return 'FER';
                case 'RTT Imposé':
                case 'rtt': // Support de l'ancien format
                    return 'RTT';
                case 'Fermeture':
                    return 'FRM';
                default:
                    return type.substring(0, 3).toUpperCase();
            }
        }

        // Construire la data [ [id, model_type, full_name, d1, d2,...], ... ]
        let hotData = entriesData.map(e => {
            let row = [e.id, e.model_type, e.full_name];
            for (let d = 1; d <= daysInMonth; d++) {
                row.push(e.days[d] ?? null);
            }
            return row;
        });

        // colHeaders
        let colHeaders = ["ID", "Type", "Employé"];
        for (let d = 1; d <= daysInMonth; d++) {
            const date = new Date(year, month - 1, d);
            const dayOfWeek = date.getDay();
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
            colHeaders.push(isWeekend ? `<span style="color:#999;">${d}</span>` : d.toString());
        }

        // columns
        let columns = [{
                data: 0,
                readOnly: true
            },
            {
                data: 1,
                readOnly: true
            },
            {
                data: 2,
                readOnly: true
            },
        ];
        for (let i = 0; i < daysInMonth; i++) {
            columns.push({
                data: i + 3,
                type: 'numeric',
                allowInvalid: false,
                validator: function(value, callback) {
                    if (value === null || value === '') {
                        callback(true); // on autorise vide
                    } else {
                        let v = parseFloat(value);
                        callback(!isNaN(v) && v >= 0 && v <= 12);
                    }
                }
            });
        }

        function detachEmployee(employeeId, modelType) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';

            // CSRF
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // DELETE method
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            // Champs nécessaires
            const projectIdInput = document.createElement('input');
            projectIdInput.type = 'hidden';
            projectIdInput.name = 'project_id';
            projectIdInput.value = projectId;
            form.appendChild(projectIdInput);

            const employeeTypeInput = document.createElement('input');
            employeeTypeInput.type = 'hidden';
            employeeTypeInput.name = 'employee_type';
            employeeTypeInput.value = modelType;
            form.appendChild(employeeTypeInput);

            const employeeIdInput = document.createElement('input');
            employeeIdInput.type = 'hidden';
            employeeIdInput.name = 'employee_id';
            employeeIdInput.value = employeeId;
            form.appendChild(employeeIdInput);

            const monthInput = document.createElement('input');
            monthInput.type = 'hidden';
            monthInput.name = 'month';
            monthInput.value = month;
            form.appendChild(monthInput);

            const yearInput = document.createElement('input');
            yearInput.type = 'hidden';
            yearInput.name = 'year';
            yearInput.value = year;
            form.appendChild(yearInput);

            const categoryInput = document.createElement('input');
            categoryInput.type = 'hidden';
            categoryInput.name = 'category';
            categoryInput.value = category;
            form.appendChild(categoryInput);

            document.body.appendChild(form);
            form.action = "{{ route('tracking.detachEmployee') }}";
            form.submit();
        }

        // Détecter si on est sur mobile
        const isMobile = window.matchMedia('(max-width: 640px)').matches;

        let container = document.getElementById('handsontable');
        let hot = new Handsontable(container, {
            data: hotData,
            rowHeaders: true,
            colHeaders: colHeaders,
            columns: columns,
            hiddenColumns: {
                columns: [0, 1], // ID + Type masqués
                indicators: false
            },
            // On fixe les 3 colonnes seulement en desktop
            fixedColumnsStart: isMobile ? 0 : 3,
            height: 'auto',
            licenseKey: 'non-commercial-and-evaluation',
            stretchH: 'all',
            manualColumnResize: true,
            rowHeights: 25,

            contextMenu: {
                items: {
                    detachEmployee: {
                        name: 'Détacher cet employé',
                        callback: function(key, selection) {
                            const row = selection[0].start.row;
                            const employeeId = hot.getDataAtCell(row, 0);
                            const modelType = hot.getDataAtCell(row, 1);
                            const employeeName = hot.getDataAtCell(row, 2);

                            if (confirm(`Voulez-vous vraiment détacher ${employeeName} ?`)) {
                                detachEmployee(employeeId, modelType);
                            }
                        }
                    }
                }
            },

            cells: function(row, col) {
                const cellProperties = {};
                if (col >= 3) {
                    cellProperties.renderer = function(instance, td, row, col, prop, value) {
                        Handsontable.renderers.NumericRenderer.apply(this, arguments);
                        const dayNum = col - 2;
                        const date = new Date(year, month - 1, dayNum);
                        const dayOfWeek = date.getDay();
                        
                        // Récupérer l'ID et le type de l'employé
                        const employeeId = instance.getDataAtRowProp(row, 0);
                        const employeeType = instance.getDataAtRowProp(row, 1);

                        // Weekend => gris
                        if (dayOfWeek === 0 || dayOfWeek === 6) {
                            td.classList.add('weekend');
                        }

                        // Jour non travaillé ?
                        const isNonWorkingDay = (dayNum in nonWorkingDayTypes);
                        const nonWorkingType = isNonWorkingDay ? nonWorkingDayTypes[dayNum] : null;
                        
                        // Congé worker ?
                        const isWorkerOnLeave = employeeType === 'worker' && 
                                               workerLeaveTypes[employeeId] && 
                                               workerLeaveTypes[employeeId][dayNum];
                        const leaveInfo = isWorkerOnLeave ? workerLeaveTypes[employeeId][dayNum] : null;

                        if (isWorkerOnLeave) {
                            // Worker en congé - afficher le code et rendre la cellule read-only
                            td.textContent = leaveInfo.code;
                            td.style.backgroundColor = leaveInfo.color;
                            td.style.color = '#FFFFFF';
                            td.style.fontWeight = 'bold';
                            td.style.textAlign = 'center';
                            cellProperties.readOnly = true;
                        } else if (value !== null && value !== '') {
                            if (parseFloat(value) === 0) {
                                td.textContent = 'abs';
                                td.classList.add('absent');
                            } else {
                                // Si jour non travaillé => orange
                                if (isNonWorkingDay) {
                                    td.classList.add('non-working');
                                } else {
                                    // Sinon colorer selon la catégorie
                                    if (category === 'night') {
                                        td.classList.add('night-hours');
                                    } else {
                                        td.classList.add('day-hours');
                                    }
                                }
                            }
                        } else if (isNonWorkingDay) {
                            // Cellule vide jour non-travaillé
                            td.textContent = getTypeAbbreviation(nonWorkingType);
                            td.classList.add('non-working-empty');
                        }
                    };
                }
                return cellProperties;
            }
        });

        // Bouton "Enregistrer"
        let saveBtn = document.getElementById('saveBtn');
        saveBtn.addEventListener('click', function() {
            // Ajouter un effet visuel pour montrer l'action
            saveBtn.classList.add('animate-pulse');
            saveBtn.querySelector('span').textContent = 'Enregistrement en cours...';

            let updated = hot.getData(); // => [ [id,model_type,full_name,d1,d2,...], ... ]
            let payload = updated.map(row => {
                let empId = row[0];
                let modelType = row[1];
                let daysArr = row.slice(3);
                let daysObj = {};
                for (let i = 0; i < daysArr.length; i++) {
                    let val = daysArr[i];
                    if (val === null || val === '') {
                        daysObj[i + 1] = null;
                    } else {
                        daysObj[i + 1] = parseFloat(val);
                    }
                }
                return {
                    id: empId,
                    model_type: modelType,
                    days: daysObj
                };
            });

            fetch("{{ route('tracking.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        project_id: projectId,
                        month: month,
                        year: year,
                        category: category,
                        data: payload
                    })
                })
                .then(resp => resp.json())
                .then(json => {
                    saveBtn.classList.remove('animate-pulse');
                    saveBtn.querySelector('span').textContent = 'Enregistrer les modifications';

                    if (json.success) {
                        // Notification de succès avec animation
                        const notification = document.createElement('div');
                        notification.className = 'fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md fade-in';
                        notification.innerHTML = `
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p>${json.message}</p>
                            </div>
                        `;
                        document.body.appendChild(notification);

                        // Supprimer la notification après 3 secondes
                        setTimeout(() => {
                            notification.style.opacity = '0';
                            notification.style.transform = 'translateY(-10px)';
                            notification.style.transition = 'opacity 0.5s, transform 0.5s';

                            setTimeout(() => {
                                notification.remove();
                            }, 500);
                        }, 3000);

                        // Rafraîchir après un court délai
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        // Notification d'erreur
                        const notification = document.createElement('div');
                        notification.className = 'fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md fade-in';
                        notification.innerHTML = `
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <p>Erreur : ${json.message}</p>
                            </div>
                        `;
                        document.body.appendChild(notification);

                        // Supprimer la notification après 4 secondes
                        setTimeout(() => {
                            notification.style.opacity = '0';
                            notification.style.transform = 'translateY(-10px)';
                            notification.style.transition = 'opacity 0.5s, transform 0.5s';

                            setTimeout(() => {
                                notification.remove();
                            }, 500);
                        }, 4000);
                    }
                })
                .catch(err => {
                    console.error(err);
                    saveBtn.classList.remove('animate-pulse');
                    saveBtn.querySelector('span').textContent = 'Enregistrer les modifications';

                    // Notification d'erreur réseau
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md fade-in';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p>Erreur de connexion. Veuillez réessayer.</p>
                        </div>
                    `;
                    document.body.appendChild(notification);

                    // Supprimer la notification après 4 secondes
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateY(-10px)';
                        notification.style.transition = 'opacity 0.5s, transform 0.5s';

                        setTimeout(() => {
                            notification.remove();
                        }, 500);
                    }, 4000);
                });
        });
    });
</script>
@endpush
