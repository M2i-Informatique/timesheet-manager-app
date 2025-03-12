@extends('pages.admin.index')

@section('title', 'Détail de l\'intérimaire')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Détail de l'intérimaire: {{ $interim->agency }}</h1>
            <div>
                <a href="{{ route('admin.interims.edit', $interim) }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mr-2">
                    Modifier
                </a>
                <a href="{{ route('admin.interims.index') }}" class="text-gray-600 hover:text-gray-900">
                    Retour à la liste
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Agence</p>
                        <p class="mt-1">{{ $interim->agency }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Taux horaire</p>
                        <p class="mt-1">{{ number_format($interim->hourly_rate, 2, ',', ' ') }} €/h</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Statut</p>
                        <p class="mt-1">
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $interim->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $interim->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Chantiers assignés</h2>

                @if ($interim->projects->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($interim->projects as $project)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h3 class="font-medium">{{ $project->name }}</h3>
                                <p class="text-sm text-gray-500">Code: {{ $project->code }}</p>
                                <p class="text-sm text-gray-500">Ville: {{ $project->city }}</p>
                                <p class="text-sm text-gray-500">Statut:
                                    <span
                                        class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $project->status === 'active' ? 'Actif' : 'Inactif' }}
                                    </span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun chantier assigné à cet intérimaire.</p>
                @endif
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Pointages récents</h2>

                @if ($interim->timesheets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Chantier</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Catégorie</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Heures</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($interim->timesheets->sortByDesc('date')->take(10) as $timesheet)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $timesheet->date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $timesheet->project->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $timesheet->category === 'day' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $timesheet->category === 'day' ? 'Jour' : 'Nuit' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $timesheet->hours }} h</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($interim->timesheets->count() > 10)
                        <div class="mt-2 text-right">
                            <span class="text-sm text-gray-500">Affichage des 10 derniers pointages uniquement.</span>
                        </div>
                    @endif
                @else
                    <p class="text-gray-500">Aucun pointage enregistré pour cet intérimaire.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
