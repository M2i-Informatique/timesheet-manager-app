@extends('pages.admin.index')

@section('title', 'Modifier un paramètre')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Modifier le paramètre: {{ $setting->key }}</h1>
            <a href="{{ route('admin.settings.index') }}" class="text-gray-600 hover:text-gray-900">
                Retour à la liste
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form method="POST" action="{{ route('admin.settings.update', $setting) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="key" class="block text-sm font-medium text-gray-700">Clé</label>
                    <input type="text" name="key" id="key" value="{{ old('key', $setting->key) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    @error('key')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="value" class="block text-sm font-medium text-gray-700">Valeur</label>
                    <input type="text" name="value" id="value" value="{{ old('value', $setting->value) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    @error('value')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début <span
                            class="text-gray-500 text-xs">(optionnel)</span></label>
                    <input type="date" name="start_date" id="start_date"
                        value="{{ old('start_date', $setting->start_date ? $setting->start_date->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('start_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin <span
                            class="text-gray-500 text-xs">(optionnel)</span></label>
                    <input type="date" name="end_date" id="end_date"
                        value="{{ old('end_date', $setting->end_date ? $setting->end_date->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('end_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Mettre à jour le paramètre
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
