@extends('pages.admin.index')

@section('title', 'Modifier un chantier')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Modifier le chantier: {{ $project->name }}</h1>
            <a href="{{ route('admin.projects.index') }}" class="text-gray-600 hover:text-gray-900">
                Retour à la liste
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form method="POST" action="{{ route('admin.projects.update', $project) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Code*</label>
                        <input type="number" name="code" id="code" value="{{ old('code', $project->code) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>
                        @error('code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom*</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Adresse</label>
                        <input type="text" name="address" id="address" value="{{ old('address', $project->address) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Ville*</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $project->city) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="distance" class="block text-sm font-medium text-gray-700">Distance (km)</label>
                        <input type="number" step="0.1" name="distance" id="distance"
                            value="{{ old('distance', $project->distance) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>
                        @error('distance')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>
                            <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>
                                Actif</option>
                            <option value="inactive"
                                {{ old('status', $project->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Travailleurs assignés</h3>

                    @if ($workers->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2 max-h-60 overflow-y-auto p-2">
                            @foreach ($workers as $worker)
                                <div class="flex items-start">
                                    <input type="checkbox" name="workers[]" id="worker_{{ $worker->id }}"
                                        value="{{ $worker->id }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ in_array($worker->id, old('workers', $assignedWorkers)) ? 'checked' : '' }}>
                                    <label for="worker_{{ $worker->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ $worker->last_name }} {{ $worker->first_name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mt-2">Aucun travailleur disponible.</p>
                    @endif

                    @error('workers')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Intérimaires assignés</h3>

                    @if ($interims->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2 max-h-60 overflow-y-auto p-2">
                            @foreach ($interims as $interim)
                                <div class="flex items-start">
                                    <input type="checkbox" name="interims[]" id="interim_{{ $interim->id }}"
                                        value="{{ $interim->id }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ in_array($interim->id, old('interims', $assignedInterims)) ? 'checked' : '' }}>
                                    <label for="interim_{{ $interim->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ $interim->agency }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mt-2">Aucun intérimaire disponible.</p>
                    @endif

                    @error('interims')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Drivers assignés</h3>

                    @if ($drivers->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2 max-h-60 overflow-y-auto p-2">
                            @foreach ($drivers as $driver)
                                <div class="flex items-start">
                                    <input type="checkbox" name="drivers[]" id="driver_{{ $driver->id }}"
                                        value="{{ $driver->id }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ in_array($driver->id, old('drivers', $assignedDrivers)) ? 'checked' : '' }}>
                                    <label for="driver_{{ $driver->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ $driver->last_name }} {{ $driver->first_name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mt-2">Aucun driver disponible.</p>
                    @endif

                    @error('drivers')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Mettre à jour le chantier
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
