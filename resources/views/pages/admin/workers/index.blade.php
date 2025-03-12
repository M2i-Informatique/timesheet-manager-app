@extends('pages.admin.index')

@section('title', 'Gestion des salariés')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Gestion des salariés</h1>
            <a href="{{ route('admin.workers.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Ajouter un salarié
            </a>
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
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $worker->last_name }} {{ $worker->first_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $worker->category === 'worker' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $worker->category === 'worker' ? 'Ouvrier' : 'ETAM' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $worker->contract_hours }} h
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ number_format($worker->monthly_salary, 2, ',', ' ') }} €
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
            {{ $workers->links() }}
        </div>
    </div>
@endsection
