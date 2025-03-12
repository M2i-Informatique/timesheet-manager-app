@extends('pages.admin.index')

@section('title', 'Créer un salarié')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Créer un nouveau salarié</h1>
            <a href="{{ route('admin.workers.index') }}" class="text-gray-600 hover:text-gray-900">
                Retour à la liste
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form method="POST" action="{{ route('admin.workers.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    @error('last_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    @error('first_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="category" id="category"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                        <option value="worker" {{ old('category') === 'worker' ? 'selected' : '' }}>Ouvrier</option>
                        <option value="etam" {{ old('category') === 'etam' ? 'selected' : '' }}>ETAM</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="contract_hours" class="block text-sm font-medium text-gray-700">Heures de contrat
                        (hebdomadaires)</label>
                    <input type="number" name="contract_hours" id="contract_hours" value="{{ old('contract_hours', 35) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    @error('contract_hours')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="monthly_salary" class="block text-sm font-medium text-gray-700">Salaire mensuel (€)</label>
                    <input type="number" step="0.01" name="monthly_salary" id="monthly_salary"
                        value="{{ old('monthly_salary') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    @error('monthly_salary')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Projets assignés</label>

                    @if ($projects->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                            @foreach ($projects as $project)
                                <div class="flex items-start">
                                    <input type="checkbox" name="projects[]" id="project_{{ $project->id }}"
                                        value="{{ $project->id }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ in_array($project->id, old('projects', [])) ? 'checked' : '' }}>
                                    <label for="project_{{ $project->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ $project->name }} ({{ $project->code }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mt-2">Aucun projet disponible.</p>
                    @endif

                    @error('projects')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Créer le salarié
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
