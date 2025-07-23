@extends('layouts.admin')

@section('title', 'Modifier le congé')

@section('content')
<div class="p-6">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.worker-leaves.index') }}" class="hover:text-gray-700">Congés salariés</a>
            <span>/</span>
            <span>Modifier congé</span>
        </div>
        <h1 class="text-xl font-semibold text-gray-900">
            @if($workerLeave->worker)
                Modifier le congé de {{ $workerLeave->worker->first_name }} {{ $workerLeave->worker->last_name }}
            @else
                Modifier le congé (salarié supprimé)
            @endif
        </h1>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.worker-leaves.update', $workerLeave) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Salarié -->
                    <div>
                        <label for="worker_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Salarié <span class="text-red-500">*</span>
                        </label>
                        <select name="worker_id" id="worker_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('worker_id') border-red-300 @enderror">
                            <option value="">Sélectionner un salarié</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}" 
                                        {{ (old('worker_id', $workerLeave->worker_id) == $worker->id) ? 'selected' : '' }}>
                                    {{ $worker->first_name }} {{ $worker->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('worker_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de congé <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('type') border-red-300 @enderror">
                            <option value="">Sélectionner un type</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" 
                                        {{ (old('type', $workerLeave->type) == $key) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date de début -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de début <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" 
                               value="{{ old('start_date', $workerLeave->start_date->format('Y-m-d')) }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('start_date') border-red-300 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date de fin -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de fin <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" id="end_date" 
                               value="{{ old('end_date', $workerLeave->end_date->format('Y-m-d')) }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('end_date') border-red-300 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Motif/Commentaire -->
                    <div class="md:col-span-2">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Motif / Commentaire
                        </label>
                        <textarea name="reason" id="reason" rows="3" placeholder="Motif ou commentaire optionnel..."
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('reason') border-red-300 @enderror">{{ old('reason', $workerLeave->reason) }}</textarea>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Informations actuelles -->
                <div class="mt-6 bg-gray-50 rounded-md p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Informations actuelles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Jours calculés :</span>
                            <span class="font-medium">{{ $workerLeave->days_count }} jour{{ $workerLeave->days_count > 1 ? 's' : '' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Créé par :</span>
                            <span class="font-medium">
                                @if($workerLeave->createdBy)
                                    {{ $workerLeave->createdBy->first_name }} {{ $workerLeave->createdBy->last_name }}
                                @else
                                    Non renseigné
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500">Créé le :</span>
                            <span class="font-medium">{{ $workerLeave->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Messages d'erreur globaux -->
                @if($errors->has('dates'))
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Erreur de validation</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    {{ $errors->first('dates') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Boutons -->
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.worker-leaves.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validation côté client pour s'assurer que la date de fin est >= date de début
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    function validateDates() {
        if (startDate.value && endDate.value) {
            if (new Date(endDate.value) < new Date(startDate.value)) {
                endDate.setCustomValidity('La date de fin doit être supérieure ou égale à la date de début');
            } else {
                endDate.setCustomValidity('');
            }
        }
    }

    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);
});
</script>
@endsection