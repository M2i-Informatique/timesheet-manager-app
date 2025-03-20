@extends('pages.admin.index')

@section('title', 'Gestion des paramètres')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Gestion des paramètres système</h1>
            <a href="{{ route('admin.settings.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Ajouter un paramètre
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de
                            début</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de
                            fin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($settings as $setting)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">
                                {{ $setting->key }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $setting->value }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $setting->start_date ? $setting->start_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $setting->end_date ? $setting->end_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $now = now();
                                    $active =
                                        (!$setting->start_date || $setting->start_date <= $now) &&
                                        (!$setting->end_date || $setting->end_date >= $now);
                                @endphp
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.settings.edit', $setting) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-3">Modifier</a>

                                <form method="POST" action="{{ route('admin.settings.destroy', $setting) }}"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce paramètre?')">
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
            {{ $settings->links('vendor.pagination.custom-pagination') }}
        </div>

        <!-- Section d'aide -->
        {{-- <div class="mt-8 bg-blue-50 p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold text-blue-800 mb-3">Paramètres système</h2>
            <p class="text-sm text-blue-700 mb-4">
                Les paramètres système permettent de configurer différentes valeurs utilisées dans l'application. Voici les
                paramètres disponibles :
            </p>
            <ul class="list-disc pl-5 text-sm text-blue-700 space-y-2">
                <li><strong>rate_charged</strong> : Pourcentage de majoration appliqué au taux horaire des travailleurs (ex:
                    70 pour 70%)</li>
                <li><strong>holidays_2024</strong> : Liste des jours fériés pour l'année 2024 au format YYYY-MM-DD (séparés
                    par des virgules)</li>
                <li><strong>default_contract_hours</strong> : Nombre d'heures de contrat par défaut pour les nouveaux
                    travailleurs</li>
                <li><strong>max_daily_hours</strong> : Nombre maximum d'heures par jour autorisé pour les pointages</li>
                <!-- Ajoutez d'autres paramètres selon vos besoins -->
            </ul>
        </div> --}}
    </div>
@endsection
