<!-- pages/tracking/index.blade -->
@extends('layouts.app')

@section('title', 'Pointage')

@php
$links = [
    ['id' => 'tracking', 'text' => 'Saisie des heures'],
    ['id' => 'main-projects', 'text' => 'Mes Chantiers'],
    ['id' => 'all-projects', 'text' => 'Tous les Chantiers'],
];

$trackingIcon = '<svg class="w-4 h-4 inline-block align-center" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>';

$downloadIcon = '<svg class="w-4 h-4 inline-block align-center" xmlns="http://www.w3.org/2000/svg"
    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round"
        d="m9 13.5 3 3m0 0 3-3m-3 3v-6m1.06-4.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
</svg>';
@endphp

@section('content')
<div class="max-w-screen-xl mx-auto h-full">
    <div class="relative pt-8 pb-24 lg:py-24 flex flex-col lg:flex-row gap-8 lg:gap-8">
        <!-- Contenu principal -->
        <div class="flex-1 order-2 lg:order-1 px-4 lg:px-0">
            <div id="tracking" class="max-w-screen-xl mx-auto mb-16 lg:mb-24">
                <div class="flex items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Saisie des heures</h1>
                    <div class="ml-3 relative top-[2px]">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 animate-pulse">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Section active
                        </span>
                    </div>
                </div>
                
                <div class="w-full py-8 px-6 bg-white shadow-lg rounded-xl border border-gray-100 transition-all duration-300 hover:shadow-xl">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">Sélectionnez votre chantier et la période</h2>
                        <p class="text-gray-600">Choisissez le chantier pour lequel vous souhaitez effectuer un pointage ainsi que la période concernée.</p>
                    </div>
                    
                    <form class="flex flex-col md:flex-row justify-between items-end gap-4"
                        action="{{ route('tracking.show') }}" method="GET">
                        @csrf
                        <div class="w-full md:w-auto flex flex-col md:flex-row gap-2">
                            <!-- Sélecteur de chantier avec étiquette visible -->
                            <div class="w-full md:w-auto">
                                <label for="project_id" class="block mb-2 text-sm font-medium text-gray-700">Chantier</label>
                                <select name="project_id" id="project_id"
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 transition-all duration-300"
                                    required>
                                    <option value="" disabled selected>Choisir un chantier</option>
                                    @foreach ($projects as $proj)
                                    <option value="{{ $proj->id }}">{{ $proj->code }} - {{ $proj->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Groupe Mois/Année avec étiquettes visibles -->
                            <div class="w-full md:w-auto flex flex-col sm:flex-row gap-2">
                                <div>
                                    <label for="month" class="block mb-2 text-sm font-medium text-gray-700">Mois</label>
                                    <select name="month" id="month"
                                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 transition-all duration-300"
                                        required>
                                        <option value="" disabled>Sélectionner</option>
                                        @php
                                        $months = [
                                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
                                        ];
                                        $currentMonth = $month ?? date('n');
                                        @endphp
                                        @foreach ($months as $key => $monthName)
                                        <option value="{{ $key }}" {{ $key == $currentMonth ? 'selected' : '' }}>
                                            {{ $monthName }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="year" class="block mb-2 text-sm font-medium text-gray-700">Année</label>
                                    <select name="year" id="year"
                                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 transition-all duration-300"
                                        required>
                                        <option value="" disabled>Sélectionner</option>
                                        @php
                                        $currentYear = $year ?? date('Y');
                                        @endphp
                                        @for ($y = 2023; $y <= 2030; $y++)
                                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 w-full md:w-auto">
                            <!-- Bouton pour afficher le chantier -->
                            <x-button-dynamic tag="button" type="submit" color="blue" class="w-full md:w-auto px-6 py-2.5 flex items-center justify-center group">
                                <span>Afficher le pointage</span>
                            </x-button-dynamic>

                            <!-- Champ caché pour la catégorie -->
                            <input type="hidden" name="category" value="day">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mes chantiers -->
            <div id="main-projects" class="max-w-screen-xl mx-auto mb-16 lg:mb-24">
                <div class="flex items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Mes Chantiers</h2>
                    <div class="ml-3 relative">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="relative flex h-2 w-2 mr-1">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            Actifs
                        </span>
                    </div>
                </div>
                
                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
                    <div class="livewire-table-container">
                        <livewire:user-projects-table />
                    </div>
                </div>
            </div>

            <!-- Tous les chantiers -->
            <div id="all-projects" class="max-w-screen-xl mx-auto mb-16 lg:mb-24">
                <div class="flex items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Tous les Chantiers</h2>
                    <div class="ml-3 relative">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Global
                        </span>
                    </div>
                </div>
                
                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
                    <div class="livewire-table-container">
                        <livewire:all-projects-table />
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin contenu principal -->

        <!-- Barre d'info latérale - visible uniquement sur desktop -->
        <div class="hidden lg:block lg:w-72 order-1 lg:order-2">
            <div class="lg:sticky lg:top-24">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100">
                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                        Sur cette page
                    </h3>
                    <ul class="space-y-3 mb-4">
                        @foreach ($links as $link)
                        <li>
                            <a href="#{{ $link['id'] }}" class="flex items-center text-gray-700 hover:text-blue-600 transition-colors duration-200 group">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></span>
                                <span>{{ $link['text'] }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-md p-6 border border-blue-100 w-64">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        Informations
                    </h3>
                    <div class="space-y-4 text-sm text-gray-600">
                        <div class="flex items-start space-x-2 p-3 bg-white rounded-lg shadow-sm">
                            <div class="flex-shrink-0 text-blue-500">
                                {!! $trackingIcon !!}
                            </div>
                            <p>Cliquez sur cette icône pour aller vers la page de pointage.</p>
                        </div>
                        <div class="flex items-start space-x-2 p-3 bg-white rounded-lg shadow-sm">
                            <div class="flex-shrink-0 text-blue-500">
                                {!! $downloadIcon !!}
                            </div>
                            <p>Cliquez sur cette icône pour télécharger une feuille de pointage vierge.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animation pour les badges et labels */
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }
    
    /* Amélioration des tableaux Livewire */
    .livewire-table-container {
        @apply overflow-x-auto;
    }
    
    /* Amélioration de l'esthétique des formulaires */
    select:focus, input:focus {
        @apply ring-2 ring-blue-300 ring-opacity-50;
        transition: all 0.2s ease-in-out;
    }
    
    /* Animation pour le scroll smooth */
    html {
        scroll-behavior: smooth;
    }
    
    /* Animation pour les sections au scroll */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #tracking, #main-projects, #all-projects {
        animation: fadeInUp 0.6s ease-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation au scroll pour les sections
        const sections = document.querySelectorAll('#tracking, #main-projects, #all-projects');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        sections.forEach(section => {
            section.style.opacity = 0;
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(section);
        });
        
        // Ajout de classe active au lien de navigation lors du scroll
        const navLinks = document.querySelectorAll('a[href^="#"]');
        
        window.addEventListener('scroll', function() {
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                
                if (window.pageYOffset >= sectionTop - 60) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('text-blue-600');
                link.querySelector('span:first-child')?.classList.add('opacity-0');
                
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('text-blue-600');
                    link.querySelector('span:first-child')?.classList.remove('opacity-0');
                }
            });
        });
    });
</script>
@endsection