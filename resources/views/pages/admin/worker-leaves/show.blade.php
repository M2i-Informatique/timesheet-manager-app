@extends('layouts.admin')

@section('title', 'Détail du congé')

@section('content')
<div class="p-6">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.worker-leaves.index') }}" class="hover:text-gray-700">Congés salariés</a>
            <span>/</span>
            <span>Détail congé</span>
        </div>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900">
                Congé de {{ $workerLeave->worker->first_name }} {{ $workerLeave->worker->last_name }}
            </h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.worker-leaves.edit', $workerLeave) }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
                <form method="POST" action="{{ route('admin.worker-leaves.destroy', $workerLeave) }}" 
                      class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce congé ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Informations du congé</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Salarié -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Salarié</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($workerLeave->worker->first_name, 0, 1) }}{{ substr($workerLeave->worker->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $workerLeave->worker->first_name }} {{ $workerLeave->worker->last_name }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $workerLeave->worker->category }}</p>
                                    </div>
                                </div>
                            </dd>
                        </div>

                        <!-- Type de congé -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type de congé</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white"
                                      style="background-color: {{ $workerLeave->type_color }}">
                                    {{ $workerLeave->type_code }}
                                </span>
                                <p class="text-sm text-gray-900 mt-1">{{ $workerLeave->type_label }}</p>
                            </dd>
                        </div>

                        <!-- Date de début -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de début</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $workerLeave->start_date->format('l j F Y') }}
                                <span class="text-gray-500">({{ $workerLeave->start_date->format('d/m/Y') }})</span>
                            </dd>
                        </div>

                        <!-- Date de fin -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de fin</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $workerLeave->end_date->format('l j F Y') }}
                                <span class="text-gray-500">({{ $workerLeave->end_date->format('d/m/Y') }})</span>
                            </dd>
                        </div>

                        <!-- Durée -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Durée</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="text-lg font-semibold">{{ $workerLeave->days_count }}</span>
                                jour{{ $workerLeave->days_count > 1 ? 's' : '' }} ouvré{{ $workerLeave->days_count > 1 ? 's' : '' }}
                            </dd>
                        </div>

                        <!-- Période -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Période</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                Du {{ $workerLeave->start_date->format('d/m/Y') }} au {{ $workerLeave->end_date->format('d/m/Y') }}
                                @if($workerLeave->start_date->eq($workerLeave->end_date))
                                    <span class="text-gray-500">(1 jour)</span>
                                @endif
                            </dd>
                        </div>
                    </div>

                    <!-- Motif -->
                    @if($workerLeave->reason)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Motif / Commentaire</dt>
                            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md p-3">
                                {{ $workerLeave->reason }}
                            </dd>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informations de suivi -->
        <div>
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Suivi</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Créé par -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Créé par</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $workerLeave->createdBy->first_name }} {{ $workerLeave->createdBy->last_name }}
                            </dd>
                        </div>

                        <!-- Date de création -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $workerLeave->created_at->format('d/m/Y à H:i') }}
                            </dd>
                        </div>

                        <!-- Dernière modification -->
                        @if($workerLeave->created_at != $workerLeave->updated_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $workerLeave->updated_at->format('d/m/Y à H:i') }}
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Actions rapides</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('admin.workers.show', $workerLeave->worker) }}" 
                           class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Voir le profil salarié
                        </a>
                        <a href="{{ route('admin.worker-leaves.index', ['worker_id' => $workerLeave->worker_id]) }}" 
                           class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z"/>
                            </svg>
                            Autres congés de ce salarié
                        </a>
                        <a href="{{ route('admin.worker-leaves.create', ['worker_id' => $workerLeave->worker_id]) }}" 
                           class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nouveau congé pour ce salarié
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection