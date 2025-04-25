@extends('pages.admin.index')

@section('title', 'Exports')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Exports des données</h1>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Export du récapitulatif mensuel des workers -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4">
                    <h2 class="text-xl font-semibold">Export du récapitulatif mensuel des travailleurs</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">
                        Générer un fichier Excel avec le récapitulatif des heures travaillées par les salariés pour un mois
                        donné.
                    </p>

                    <form action="{{ route('admin.exports.workers-monthly') }}" method="post">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                                <select name="month" id="month"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="1" {{ now()->month == 1 ? 'selected' : '' }}>Janvier</option>
                                    <option value="2" {{ now()->month == 2 ? 'selected' : '' }}>Février</option>
                                    <option value="3" {{ now()->month == 3 ? 'selected' : '' }}>Mars</option>
                                    <option value="4" {{ now()->month == 4 ? 'selected' : '' }}>Avril</option>
                                    <option value="5" {{ now()->month == 5 ? 'selected' : '' }}>Mai</option>
                                    <option value="6" {{ now()->month == 6 ? 'selected' : '' }}>Juin</option>
                                    <option value="7" {{ now()->month == 7 ? 'selected' : '' }}>Juillet</option>
                                    <option value="8" {{ now()->month == 8 ? 'selected' : '' }}>Août</option>
                                    <option value="9" {{ now()->month == 9 ? 'selected' : '' }}>Septembre</option>
                                    <option value="10" {{ now()->month == 10 ? 'selected' : '' }}>Octobre</option>
                                    <option value="11" {{ now()->month == 11 ? 'selected' : '' }}>Novembre</option>
                                    <option value="12" {{ now()->month == 12 ? 'selected' : '' }}>Décembre</option>
                                </select>
                            </div>
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                                <select name="year" id="year"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @for ($i = now()->year - 2; $i <= now()->year + 1; $i++)
                                        <option value="{{ $i }}" {{ now()->year == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            <i class="fas fa-download mr-2"></i> Exporter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Export d'une feuille de pointage vierge -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="bg-green-600 text-white px-6 py-4">
                    <h2 class="text-xl font-semibold">Export d'une feuille de pointage vierge</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">
                        Générer une feuille de pointage vierge pour un chantier et un mois spécifique.
                    </p>

                    <form action="{{ route('admin.exports.blank-monthly') }}" method="post">
                        @csrf
                        <div class="mb-4">
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Chantier</label>
                            <select name="project_id" id="project_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">-- Sélectionner un chantier --</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">
                                        {{ $project->code }} - {{ $project->name }} ({{ $project->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="blank_month" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                                <select name="month" id="blank_month"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="1" {{ now()->month == 1 ? 'selected' : '' }}>Janvier</option>
                                    <option value="2" {{ now()->month == 2 ? 'selected' : '' }}>Février</option>
                                    <option value="3" {{ now()->month == 3 ? 'selected' : '' }}>Mars</option>
                                    <option value="4" {{ now()->month == 4 ? 'selected' : '' }}>Avril</option>
                                    <option value="5" {{ now()->month == 5 ? 'selected' : '' }}>Mai</option>
                                    <option value="6" {{ now()->month == 6 ? 'selected' : '' }}>Juin</option>
                                    <option value="7" {{ now()->month == 7 ? 'selected' : '' }}>Juillet</option>
                                    <option value="8" {{ now()->month == 8 ? 'selected' : '' }}>Août</option>
                                    <option value="9" {{ now()->month == 9 ? 'selected' : '' }}>Septembre</option>
                                    <option value="10" {{ now()->month == 10 ? 'selected' : '' }}>Octobre</option>
                                    <option value="11" {{ now()->month == 11 ? 'selected' : '' }}>Novembre</option>
                                    <option value="12" {{ now()->month == 12 ? 'selected' : '' }}>Décembre</option>
                                </select>
                            </div>
                            <div>
                                <label for="blank_year" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                                <select name="year" id="blank_year"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    @for ($i = now()->year - 2; $i <= now()->year + 1; $i++)
                                        <option value="{{ $i }}" {{ now()->year == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                            <i class="fas fa-download mr-2"></i> Exporter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
