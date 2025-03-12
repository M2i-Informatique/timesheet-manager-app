@extends('pages.admin.index')

@section('title', 'Attribuer des projets')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Attribuer des projets à: {{ $driver->name }}</h1>
            <a href="{{ route('admin.driver-projects.index') }}" class="text-gray-600 hover:text-gray-900">
                Retour à la liste
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form method="POST" action="{{ route('admin.driver-projects.update', $driver) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Projets disponibles</label>

                    @if ($projects->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($projects as $project)
                                <div class="flex items-start">
                                    <input type="checkbox" name="projects[]" id="project_{{ $project->id }}"
                                        value="{{ $project->id }}"
                                        class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ in_array($project->id, $assignedProjects) ? 'checked' : '' }}>
                                    <label for="project_{{ $project->id }}" class="ml-2 block text-sm text-gray-900">
                                        <span class="font-medium">{{ $project->name }}</span>
                                        <span class="text-gray-500 block">(Code: {{ $project->code }})</span>
                                        <span class="text-gray-500 block">{{ $project->city }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Aucun projet disponible.</p>
                    @endif

                    @error('projects')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Enregistrer les attributions
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
