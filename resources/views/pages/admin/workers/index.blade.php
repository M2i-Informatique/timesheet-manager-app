@extends('pages.admin.index')

@section('title', 'Gestion des salariés')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Gestion des salariés</h1>
            
            <div class="flex space-x-2">
                <a href="{{ route('admin.workers.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Ajouter un salarié
                </a>
            </div>
        </div>

        <!-- Filtres et Export -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form action="{{ route('admin.workers.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                    <select name="month" id="month" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->locale('fr')->monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                    <select name="year" id="year" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(now()->year - 1, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="project_category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie Chantier</label>
                    <select name="project_category" id="project_category" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous</option>
                        <option value="mh" {{ request('project_category') == 'mh' ? 'selected' : '' }}>MH</option>
                        <option value="go" {{ request('project_category') == 'go' ? 'selected' : '' }}>GO</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors">
                        Filtrer
                    </button>
                    
                    @if(request('month') || request('year') || request('project_category'))
                        <a href="{{ route('admin.workers.index') }}" class="text-gray-600 hover:text-gray-900 ml-2 underline text-sm">Réinitialiser</a>
                    @endif
                </div>

                <div class="ml-auto">
                    <a href="{{ route('admin.workers.export-yearly', request()->all()) }}" class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Annuel {{ request('year', now()->year) }}
                    </a>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Heures <span class="text-gray-400">({{ \Carbon\Carbon::create(null, request('month', now()->month))->locale('fr')->monthName }})</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures de
                            contrat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salaire
                            mensuel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux
                            horaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($workers as $worker)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                {{ $worker->last_name }} {{ $worker->first_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $worker->category === 'worker' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $worker->category === 'worker' ? 'Ouvrier' : 'ETAM' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
                                {{ number_format($workersHours[$worker->id] ?? 0, 2, ',', ' ') }} h
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                {{ $worker->contract_hours }} h
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                {{ number_format($worker->monthly_salary, 2, ',', ' ') }} €
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                {{ $worker->hourly_rate ? number_format($worker->hourly_rate, 2, ',', ' ') . ' €' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $worker->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $worker->status === 'active' ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.workers.show', $worker) }}"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Voir</a>
                                <a href="{{ route('admin.workers.edit', $worker) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-3">Modifier</a>

                                <form method="POST" action="{{ route('admin.workers.destroy', $worker) }}"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce travailleur?')">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <!-- Pagination append query parameters -->
            {{ $workers->appends(request()->all())->links('vendor.pagination.custom-pagination') }}
        </div>
    </div>
@endsection
