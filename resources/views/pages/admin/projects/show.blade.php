@extends('pages.admin.index')

@section('title', 'Détail du chantier')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Détail du chantier: {{ $project->name }}</h1>
            <div>
                <a href="{{ route('admin.projects.edit', $project) }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mr-2">
                    Modifier
                </a>
                <a href="{{ route('admin.projects.index') }}" class="text-gray-600 hover:text-gray-900">
                    Retour à la liste
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Code</p>
                        <p class="mt-1">{{ $project->code }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nom</p>
                        <p class="mt-1">{{ $project->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Catégorie</p>
                        <p class="mt-1">
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $project->category === 'mh'
                                    ? 'bg-blue-100 text-blue-800'
                                    : ($project->category === 'go'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $project->formatted_category }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Adresse</p>
                        <p class="mt-1">{{ $project->formatted_address }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Ville</p>
                        <p class="mt-1">{{ $project->city }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Distance</p>
                        <p class="mt-1">{{ $project->formatted_distance }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Zone</p>
                        <p class="mt-1">{{ $project->formatted_zone }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Statut</p>
                        <p class="mt-1">
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $project->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Travailleurs assignés</h2>

                @if ($project->workers->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($project->workers as $worker)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h3 class="font-medium">{{ $worker->last_name }} {{ $worker->first_name }}</h3>
                                <p class="text-sm text-gray-500">Catégorie:
                                    <span
                                        class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $worker->category === 'worker' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $worker->category === 'worker' ? 'Ouvrier' : 'ETAM' }}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-500">Taux horaire:
                                    {{ $worker->hourly_rate ? number_format($worker->hourly_rate, 2, ',', ' ') . ' €' : '-' }}
                                </p>
                                <p class="text-sm text-gray-500">Statut:
                                    <span
                                        class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $worker->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $worker->status === 'active' ? 'Actif' : 'Inactif' }}
                                    </span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun travailleur assigné à ce chantier.</p>
                @endif
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Intérimaires assignés</h2>

                @if ($project->interims->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($project->interims as $interim)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h3 class="font-medium">{{ $interim->agency }}</h3>
                                <p class="text-sm text-gray-500">Taux horaire:
                                    {{ number_format($interim->hourly_rate, 2, ',', ' ') }} €/h</p>
                                <p class="text-sm text-gray-500">Statut:
                                    <span
                                        class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $interim->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $interim->status === 'active' ? 'Actif' : 'Inactif' }}
                                    </span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun intérimaire assigné à ce chantier.</p>
                @endif
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Drivers assignés</h2>

                @if ($project->drivers->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($project->drivers as $driver)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h3 class="font-medium">{{ $driver->last_name }} {{ $driver->first_name }}</h3>
                                <p class="text-sm text-gray-500">Email: {{ $driver->email }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun driver assigné à ce chantier.</p>
                @endif
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Pointages récents</h2>

                @if ($project->timesheets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Salarié/Intérimaire</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Catégorie</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Heures</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($project->timesheets as $timesheet)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $timesheet->date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($timesheet->timesheetable_type == 'App\\Models\\Worker')
                                                @php $worker = \App\Models\Worker::find($timesheet->timesheetable_id); @endphp
                                                {{ $worker ? $worker->last_name . ' ' . $worker->first_name : 'Travailleur inconnu' }}
                                            @elseif($timesheet->timesheetable_type == 'App\\Models\\Interim')
                                                @php $interim = \App\Models\Interim::find($timesheet->timesheetable_id); @endphp
                                                {{ $interim ? $interim->agency : 'Intérimaire inconnu' }}
                                            @else
                                                Inconnu
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($timesheet->timesheetable_type == 'App\\Models\\Worker')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Salarié
                                                </span>
                                            @elseif($timesheet->timesheetable_type == 'App\\Models\\Interim')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Intérimaire
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Inconnu
                                                </span>
                                            @endif
                                        </td>
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
                    @if ($project->timesheets->count() > 10)
                        <div class="mt-2 text-right">
                            <span class="text-sm text-gray-500">Affichage des 10 derniers pointages uniquement.</span>
                        </div>
                    @endif
                @else
                    <p class="text-gray-500">Aucun pointage enregistré pour ce chantier.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
