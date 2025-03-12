@extends('pages.admin.index')

@section('title', 'Détail du salarié')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Détail du salarié: {{ $worker->last_name }} {{ $worker->first_name }}</h1>
            <div>
                <a href="{{ route('admin.workers.edit', $worker) }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mr-2">
                    Modifier
                </a>
                <a href="{{ route('admin.workers.index') }}" class="text-gray-600 hover:text-gray-900">
                    Retour à la liste
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nom complet</p>
                        <p class="mt-1">{{ $worker->last_name }} {{ $worker->first_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Catégorie</p>
                        <p class="mt-1">
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $worker->category === 'worker' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $worker->category === 'worker' ? 'Ouvrier' : 'ETAM' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Heures contractuelles</p>
                        <p class="mt-1">{{ $worker->contract_hours }} h / semaine</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Salaire mensuel</p>
                        <p class="mt-1">{{ number_format($worker->monthly_salary, 2, ',', ' ') }} €</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Taux horaire calculé</p>
                        <p class="mt-1">
                            {{ $worker->hourly_rate ? number_format($worker->hourly_rate, 2, ',', ' ') . ' €' : 'Non calculable' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Statut</p>
                        <p class="mt-1">
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $worker->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $worker->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Projets assignés</h2>

                @if ($worker->projects->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($worker->projects as $project)
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
                    <p class="text-gray-500">Aucun projet assigné à ce salarié.</p>
                @endif
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Pointages récents</h2>

                @if ($worker->timesheets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Projet</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Catégorie</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Heures</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($worker->timesheets->sortByDesc('date')->take(10) as $timesheet)
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
                    @if ($worker->timesheets->count() > 10)
                        <div class="mt-2 text-right">
                            <span class="text-sm text-gray-500">Affichage des 10 derniers pointages uniquement.</span>
                        </div>
                    @endif
                @else
                    <p class="text-gray-500">Aucun pointage enregistré pour ce salarié.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
