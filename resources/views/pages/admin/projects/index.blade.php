@extends('pages.admin.index')

@section('title', 'Gestion des chantiers')

@section('admin-content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestion des chantiers</h1>
        <a href="{{ route('admin.projects.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Ajouter un chantier
        </a>
    </div>

    <!-- Barre de filtres -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <!-- Filtre par catégorie MH/GO -->
            <div class="flex items-center">
                <span class="mr-2 text-sm font-medium text-gray-700">Catégorie :</span>
                <span class="inline-flex divide-x divide-gray-300 overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
                    <a href="{{ route('admin.projects.index', array_merge(request()->except('category'), ['category' => ''])) }}"
                        class="px-3 py-1.5 text-sm {{ !$category ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                        Tous
                    </a>
                    <a href="{{ route('admin.projects.index', array_merge(request()->except('category'), ['category' => 'mh'])) }}"
                        class="px-3 py-1.5 text-sm {{ $category === 'mh' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                        MH
                    </a>
                    <a href="{{ route('admin.projects.index', array_merge(request()->except('category'), ['category' => 'go'])) }}"
                        class="px-3 py-1.5 text-sm {{ $category === 'go' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                        GO
                    </a>
                </span>
            </div>

            <!-- Filtre par heures sur le mois en cours -->
            <div class="flex items-center">
                <span class="mr-2 text-sm font-medium text-gray-700">Affichage :</span>
                <a href="{{ route('admin.projects.index', array_merge(request()->except('with_hours'), ['with_hours' => $withHoursOnly ? '' : '1'])) }}"
                    class="inline-flex items-center px-3 py-1.5 text-sm rounded border transition-colors
                        {{ $withHoursOnly 
                            ? 'bg-green-50 text-green-700 border-green-300 font-bold' 
                            : 'bg-white text-gray-700 border-gray-200 hover:bg-green-50 hover:text-green-700 font-medium' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Avec heures ce mois ({{ now()->locale('fr')->translatedFormat('F Y') }})
                </a>
            </div>
        </div>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distance
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($projects as $project)
                <tr class="hover:bg-blue-50 cursor-pointer transition-colors" 
                    onclick="window.location='{{ route('tracking.show', ['project_id' => $project->id, 'month' => now()->month, 'year' => now()->year]) }}'">
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-blue-600">
                        {{ $project->code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $project->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $project->category === 'mh'
                                    ? 'bg-blue-100 text-blue-800'
                                    : ($project->category === 'go'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $project->formatted_category }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $project->city }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $project->formatted_zone }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $project->formatted_distance }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $project->status === 'active' ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                        <a href="{{ route('admin.projects.show', $project) }}"
                            class="text-indigo-600 hover:text-indigo-900 mr-3">Voir</a>
                        <a href="{{ route('admin.projects.edit', $project) }}"
                            class="text-blue-600 hover:text-blue-900 mr-3">Modifier</a>

                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                            class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce chantier?')">
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
        {{ $projects->links('vendor.pagination.custom-pagination') }}
    </div>
</div>
@endsection