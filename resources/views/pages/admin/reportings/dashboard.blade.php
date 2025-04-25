@extends('pages.admin.index')

@section('title', 'Tableau de bord - Reporting')

@section('admin-content')
<div class="container mx-auto">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-8">
        <!-- KPI: Coût total (uniquement workers) -->
        <div class="bg-blue-50 border border-blue-500 rounded-lg p-6 h-full flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-medium text-blue-600 mb-1">
                    Coût total des salariés <span class="font-bold">({{ now()->locale('fr')->translatedFormat('F Y') }})</span>
                </h3>
                <p class="text-3xl font-bold text-blue-600">{{ number_format($totalCostCurrentMonth, 2, ',', ' ') }} €</p>
                <div class="mt-2 text-sm text-gray-500">
                    <div class="text-gray-500 mb-1 text-xs italic">Uniquement coûts des salariés</div>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500 flex items-end gap-2">
                @if(isset($costChangePercent) && $costChangePercent !== 0)
                <div class="inline-flex gap-2 rounded-sm {{ $costChangePercent > 0 ? 'bg-green-100 text-green-600 border border-green-600' : 'bg-red-100 text-red-600 border border-red-600' }} p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($costChangePercent > 0)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        @endif
                    </svg>
                    <span class="text-xs font-medium">{{ number_format(abs($costChangePercent), 2, ',', ' ') }}%</span>
                </div>
                @endif
                <span>vs mois précédent</span>
            </div>
        </div>

        <!-- KPI: Total heures travaillées -->
        <div class="bg-blue-50 border border-blue-500 rounded-lg p-6 h-full flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-medium text-blue-600 mb-1">
                    Heures travaillées <span class="font-bold">({{ now()->locale('fr')->translatedFormat('F Y') }})</span>
                </h3>
                <p class="text-3xl font-bold text-blue-600">{{ number_format($totalHoursCurrentMonth, 2, ',', ' ') }} h</p>
                <div class="mt-2 text-sm space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-blue-600 font-medium">Salariés:</span>
                        <span class="rounded-full bg-blue-100 px-2 text-blue-700 border border-blue-300">
                            {{ number_format($totalWorkerHoursCurrentMonth ?? 0, 2, ',', ' ') }} h /
                            <span class="inline-flex items-center justify-center rounded-full px-2">
                                <p class="text-sm whitespace-nowrap">
                                    {{ $totalHoursCurrentMonth > 0 ? number_format((($totalWorkerHoursCurrentMonth ?? 0) / $totalHoursCurrentMonth) * 100, 1) : 0 }}%
                                </p>
                            </span>
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-green-600 font-medium ">Intérims:</span>
                        <span class="rounded-full bg-green-100 px-2 text-green-700 border border-green-300">
                            {{ number_format($totalInterimHoursCurrentMonth ?? 0, 2, ',', ' ') }} h /
                            <span class="inline-flex items-center justify-center rounded-full px-2">
                                <p class="text-sm whitespace-nowrap">
                                    {{ $totalHoursCurrentMonth > 0 ? number_format((($totalInterimHoursCurrentMonth ?? 0) / $totalHoursCurrentMonth) * 100, 1) : 0 }}%
                                </p>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500 flex items-end gap-2">
                @if(isset($hoursChangePercent) && $hoursChangePercent !== 0)
                <div class="inline-flex gap-2 rounded-sm {{ $hoursChangePercent > 0 ? 'bg-green-100 text-green-600 border border-green-600' : 'bg-red-100 text-red-600 border border-red-600' }} p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($hoursChangePercent > 0)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        @endif
                    </svg>
                    <span class="text-xs font-medium">{{ number_format(abs($hoursChangePercent), 2, ',', ' ') }}%</span>
                </div>
                @endif
                <span>vs mois précédent</span>
            </div>
        </div>
    </div>

    <!-- Liens rapides vers les rapports -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Rapports détaillés</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.reporting.index', ['report_type' => 'project_costs']) }}"
                class="report-link block p-4 border border-gray-200 rounded-md hover:border-blue-500 hover:bg-blue-50 transition-colors">
                <h4 class="font-medium text-blue-600 mb-2">Coûts par chantier</h4>
                <p class="text-sm text-gray-600">Analyse des coûts des chantiers avec répartition détaillée</p>
            </a>
            <a href="{{ route('admin.reporting.index', ['report_type' => 'project_hours']) }}"
                class="report-link block p-4 border border-gray-200 rounded-md hover:border-blue-500 hover:bg-blue-50 transition-colors">
                <h4 class="font-medium text-blue-600 mb-2">Heures par chantier</h4>
                <p class="text-sm text-gray-600">Analyse détaillée des heures par chantier et par salarié</p>
            </a>
            <a href="{{ route('admin.reporting.index', ['report_type' => 'worker_costs']) }}"
                class="report-link block p-4 border border-gray-200 rounded-md hover:border-blue-500 hover:bg-blue-50 transition-colors">
                <h4 class="font-medium text-blue-600 mb-2">Coûts par salarié</h4>
                <p class="text-sm text-gray-600">Coûts générés par salarié avec détail par pointage</p>
            </a>
            <a href="{{ route('admin.reporting.index', ['report_type' => 'worker_hours']) }}"
                class="report-link block p-4 border border-gray-200 rounded-md hover:border-blue-500 hover:bg-blue-50 transition-colors">
                <h4 class="font-medium text-blue-600 mb-2">Heures par salarié</h4>
                <p class="text-sm text-gray-600">Heures travaillées par personne avec détail des pointages</p>
            </a>
        </div>
    </div>
    
    <!-- Logs d'activité -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @livewire('admin.activity-logs')
    </div>
</div>

<!-- Overlay de chargement qui sera visible après le clic sur un lien -->
<div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-70 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-8 max-w-sm text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-blue-500 mb-4"></div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Chargement en cours</h3>
        <p class="text-gray-600">Veuillez patienter pendant la récupération des données...</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sélectionne tous les liens de rapports
        const reportLinks = document.querySelectorAll('.report-link');
        
        // Ajoute un écouteur d'événement sur chaque lien
        reportLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Empêche le comportement par défaut du lien
                e.preventDefault();
                
                // Récupère l'URL du lien
                const url = this.getAttribute('href');
                
                // Affiche l'overlay de chargement
                document.getElementById('loadingOverlay').classList.remove('hidden');
                document.getElementById('loadingOverlay').classList.add('flex');
                
                // Stocke l'état du chargement dans sessionStorage
                sessionStorage.setItem('isLoadingReport', 'true');
                
                // Redirige immédiatement vers la page
                window.location.href = url;
            });
        });
    });
</script>
@endpush