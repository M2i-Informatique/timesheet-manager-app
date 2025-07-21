@extends('layouts.admin')

@section('title', 'Gestion des congés salariés')

@section('content')
<div class="p-6">
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Gestion des congés salariés</h1>
            <p class="text-sm text-gray-600">Gérez les congés, RTT et absences des salariés</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.worker-leaves.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouveau congé
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filtres</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('admin.worker-leaves.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Filtre Salarié -->
                <div>
                    <label for="worker_id" class="block text-sm font-medium text-gray-700 mb-1">Salarié</label>
                    <select name="worker_id" id="worker_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les salariés</option>
                        @foreach($workers as $worker)
                            <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
                                {{ $worker->first_name }} {{ $worker->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type de congé</label>
                    <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre Date début -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">À partir du</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Filtre Date fin -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Jusqu'au</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Boutons -->
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                        Filtrer
                    </button>
                    <a href="{{ route('admin.worker-leaves.index') }}" 
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des congés -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salarié</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaves as $leave)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $leave->worker->first_name }} {{ $leave->worker->last_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                      style="background-color: {{ $leave->type_color }}">
                                    {{ $leave->type_code }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">{{ $leave->type_label }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Du {{ $leave->start_date->format('d/m/Y') }} au {{ $leave->end_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $leave->days_count }} jour{{ $leave->days_count > 1 ? 's' : '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $leave->createdBy->first_name }} {{ $leave->createdBy->last_name }}
                                <div class="text-xs text-gray-500">{{ $leave->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.worker-leaves.show', $leave) }}" 
                                   class="text-blue-600 hover:text-blue-900">Voir</a>
                                <a href="{{ route('admin.worker-leaves.edit', $leave) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                <form method="POST" action="{{ route('admin.worker-leaves.destroy', $leave) }}" 
                                      class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce congé ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Aucun congé trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leaves->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $leaves->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection