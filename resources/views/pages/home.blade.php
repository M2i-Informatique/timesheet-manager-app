@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    <div class="max-w-screen-xl flex mx-auto gap-16 pt-24 min-h-screen">
        <!-- Contenu principal -->
        <div class="flex-1">
            <!-- Mes chantiers -->
            <div class="max-w-screen-xl mx-auto mb-24">
                <h2 class="text-xl font-bold mb-4"># Mes Chantiers</h2>
                <div id="main-projects" class="mb-8">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div class="bg-white shadow rounded">
                            <livewire:projects-user-table />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tous les chantiers -->
            <div class="max-w-screen-xl mx-auto mb-24">
                <h2 class="text-xl font-bold mb-4"># Tous les Chantiers</h2>
                <div id="all-projects" class="mb-8">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div class="bg-white shadow rounded">
                            <livewire:all-projects-table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin contenu principal -->
        <x-sidebars.nav :links="[
            ['id' => 'main-projects', 'text' => 'Mes Chantiers'],
            ['id' => 'all-projects', 'text' => 'Tous les Chantiers'],
        ]" />
    </div>
@endsection
