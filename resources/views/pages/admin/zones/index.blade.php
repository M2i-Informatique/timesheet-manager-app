@extends('pages.admin.index')

@section('title', 'Gestion des zones')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Gestion des zones</h1>
            <a href="{{ route('admin.zones.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Ajouter une zone
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distance
                            min (km)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distance
                            max (km)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chantiers
                        </th>
                        <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th> -->
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($zones as $zone)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">
                                {{ $zone->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ number_format($zone->min_km, 1, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ is_null($zone->max_km) ? '∞' : number_format($zone->max_km, 1, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ number_format($zone->rate, 2, ',', ' ') }} €
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $zone->projects()->count() }}
                            </td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.zones.edit', $zone) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-3">Modifier</a>

                                <form method="POST" action="{{ route('admin.zones.destroy', $zone) }}"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette zone?')">
                                        Supprimer
                                    </button>
                                </form>
                            </td> -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $zones->links('vendor.pagination.custom-pagination') }}
        </div>
    </div>
@endsection
