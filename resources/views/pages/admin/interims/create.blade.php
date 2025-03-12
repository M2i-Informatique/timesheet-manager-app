@extends('pages.admin.index')

@section('title', 'Créer un intérimaire')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Créer un nouvel intérimaire</h1>
            <a href="{{ route('admin.interims.index') }}" class="text-gray-600 hover:text-gray-900">
                Retour à la liste
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form method="POST" action="{{ route('admin.interims.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="agency" class="block text-sm font-medium text-gray-700">Agence</label>
                    <input type="text" name="agency" id="agency" value="{{ old('agency') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    @error('agency')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700">Taux horaire (€/h)</label>
                    <input type="number" step="0.01" name="hourly_rate" id="hourly_rate"
                        value="{{ old('hourly_rate') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    @error('hourly_rate')
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
                        Créer l'intérimaire
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
