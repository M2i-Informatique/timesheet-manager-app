@extends('pages.admin.index')

@section('title', 'Détails du jour non travaillé')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Détails du jour non travaillé</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.non-working-days.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Retour à la liste
                </a>
                <a href="{{ route('admin.non-working-days.edit', $nonWorkingDay) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                    Modifier
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            style="width: 200px">
                            ID
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $nonWorkingDay->id }}
                        </td>
                    </tr>
                    <tr>
                        <th
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $nonWorkingDay->date->format('d/m/Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $nonWorkingDay->type }}
                        </td>
                    </tr>
                    <tr>
                        <th
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Créé le
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $nonWorkingDay->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    <tr>
                        <th
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mis à jour le
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $nonWorkingDay->updated_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200">
                <form action="{{ route('admin.non-working-days.destroy', $nonWorkingDay) }}" method="POST"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce jour non travaillé ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded">
                        <i class="fas fa-trash mr-2"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
