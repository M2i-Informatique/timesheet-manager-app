@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    <div class="max-w-screen-xl mx-auto pt-24 min-h-screen">
        <h1 class="text-2xl font-bold mb-6">Accueil</h1>

        @if ($assignedProjects->count() > 0)
            <p class="mb-4">Voici les chantiers qui vous sont assignés :</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($assignedProjects as $project)
                    <div class="p-4 border border-gray-200 rounded shadow bg-white">
                        <h2 class="text-lg font-semibold">
                            {{ $project->code }} - {{ $project->name }}
                        </h2>
                        <p class="text-sm text-gray-600">
                            {{ $project->address }}, {{ $project->city }}
                        </p>
                        <p class="text-sm text-gray-600">
                            Distance : {{ $project->distance }} km
                        </p>
                        <div class="mt-2">
                            <a href="{{ route('tracking.show', [
                                'project_id' => $project->id,
                                'month' => now()->month,
                                'year' => now()->year,
                                'category' => 'day',
                            ]) }}"
                                class="inline-block px-3 py-1 text-sm text-blue-600 underline hover:no-underline">
                                Voir la saisie
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>Aucun chantier ne vous est assigné.</p>
        @endif
    </div>
@endsection
