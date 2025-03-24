@extends('pages.admin.index')

@section('title', 'Gestion des jours non travaillés')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Gestion des jours non travaillés</h1>
            <a href="{{ route('admin.non-working-days.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Ajouter un jour
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

        <!-- Form for generating French holidays -->
        {{-- <div class="bg-gray-100 border border-gray-200 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-semibold mb-2">Génération automatique des jours fériés français</h2>
            <form action="{{ route('admin.non-working-days.generate-french-holidays') }}" method="post"
                class="flex flex-wrap items-end gap-4">
                @csrf
                <div class="flex-grow max-w-xs">
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                    <select name="year" id="year"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @for ($y = now()->year - 1; $y <= now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                        <i class="fas fa-calendar-check mr-2"></i> Générer les jours fériés
                    </button>
                </div>
            </form>
        </div> --}}

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($nonWorkingDays as $day)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $day->date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $day->type }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.non-working-days.edit', $day) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-3">Modifier</a>

                                <form method="POST" action="{{ route('admin.non-working-days.destroy', $day) }}"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce jour?')">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                Aucun jour non travaillé enregistré
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $nonWorkingDays->links('vendor.pagination.custom-pagination') }}
        </div>
    </div>
@endsection
