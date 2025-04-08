@extends('pages.admin.index')

@section('title', 'Tableau de bord - Reporting')

@section('admin-content')
<div class="container mx-auto py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Tableau de bord</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-8">
        <!-- KPI: Coût total (uniquement workers) -->
        <div class="bg-white rounded-lg shadow-md p-6 h-full flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">
                    Coût total des salariés <span class="font-bold">({{ now()->locale('fr')->translatedFormat('F Y') }})</span>
                </h3>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($totalCostCurrentMonth, 2, ',', ' ') }} €</p>
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
        <div class="bg-white rounded-lg shadow-md p-6 h-full flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">
                    Heures travaillées <span class="font-bold">({{ now()->locale('fr')->translatedFormat('F Y') }})</span>
                </h3>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($totalHoursCurrentMonth, 2, ',', ' ') }} h</p>
                <div class="mt-2 text-sm space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-blue-600 font-medium">Salariés:</span>
                        <span class="text-blue-600">
                            {{ number_format($totalWorkerHoursCurrentMonth ?? 0, 2, ',', ' ') }} h
                            <span class="inline-flex items-center justify-center rounded-full bg-blue-100 px-2 text-blue-700">
                                <p class="text-sm whitespace-nowrap">
                                    {{ $totalHoursCurrentMonth > 0 ? number_format((($totalWorkerHoursCurrentMonth ?? 0) / $totalHoursCurrentMonth) * 100, 1) : 0 }}%
                                </p>
                            </span>
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-green-600 font-medium">Intérims:</span>
                        <span class="text-green-600">
                            {{ number_format($totalInterimHoursCurrentMonth ?? 0, 2, ',', ' ') }} h
                            <span class="inline-flex items-center justify-center rounded-full bg-green-100 px-2 text-green-700">
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

        <!-- KPI: Projets actifs -->
        <!-- <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Projets actifs</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ $activeProjectsCount }}</p>
                <div class="mt-2 text-sm text-gray-500">
                    {{ $projectsWithActivityCount }} projets avec activité ce mois
                </div>
            </div> -->

        <!-- KPI: Travailleurs actifs -->
        <!-- <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Travailleurs actifs</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ $activeWorkersCount }}</p>
                <div class="mt-2 text-sm text-gray-500">
                    {{ $workersWithActivityCount }} avec pointage ce mois
                </div>
            </div> -->
    </div>

    <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8"> -->
    <!-- Top 5 projets par heures -->
    <!-- <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Top 5 projets (heures)</h3>
                <div style="height: 300px;">
                    <canvas id="topProjectsChart"></canvas>
                </div>
            </div> -->

    <!-- Top 5 travailleurs par heures -->
    <!-- <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Top 5 travailleurs (heures)</h3>
                <div style="height: 300px;">
                    <canvas id="topWorkersChart"></canvas>
                </div>
            </div> -->
    <!-- </div> -->

    <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8"> -->
    <!-- Répartition des coûts par catégorie de projet -->
    <!-- <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Coûts par catégorie (salariés uniquement)</h3>
                <div style="height: 300px;">
                    <canvas id="costsByCategoryChart"></canvas>
                </div>
            </div> -->

    <!-- Tendance heures sur 6 derniers mois -->
    <!-- <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Tendance heures (6 derniers mois)</h3>
                <div style="height: 300px;">
                    <canvas id="hoursHistoryChart"></canvas>
                </div>
            </div> -->
    <!-- </div> -->

    <!-- Liens rapides vers les rapports -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Rapports détaillés</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.reporting.index', ['report_type' => 'project_costs']) }}"
                class="block p-4 border border-gray-200 rounded-md hover:border-indigo-500 hover:bg-indigo-50 transition-colors">
                <h4 class="font-medium text-indigo-600 mb-2">Coûts par chantier</h4>
                <p class="text-sm text-gray-600">Analyse des coûts des chantiers avec répartition détaillée</p>
            </a>
            <a href="{{ route('admin.reporting.index', ['report_type' => 'project_hours']) }}"
                class="block p-4 border border-gray-200 rounded-md hover:border-indigo-500 hover:bg-indigo-50 transition-colors">
                <h4 class="font-medium text-indigo-600 mb-2">Heures par chantier</h4>
                <p class="text-sm text-gray-600">Analyse détaillée des heures par chantier et par salarié</p>
            </a>
            <a href="{{ route('admin.reporting.index', ['report_type' => 'worker_costs']) }}"
                class="block p-4 border border-gray-200 rounded-md hover:border-indigo-500 hover:bg-indigo-50 transition-colors">
                <h4 class="font-medium text-indigo-600 mb-2">Coûts par salarié</h4>
                <p class="text-sm text-gray-600">Coûts générés par salarié avec détail par pointage</p>
            </a>
            <a href="{{ route('admin.reporting.index', ['report_type' => 'worker_hours']) }}"
                class="block p-4 border border-gray-200 rounded-md hover:border-indigo-500 hover:bg-indigo-50 transition-colors">
                <h4 class="font-medium text-indigo-600 mb-2">Heures par salarié</h4>
                <p class="text-sm text-gray-600">Heures travaillées par personne avec détail des pointages</p>
            </a>
        </div>
    </div>
    <!-- Logs d'activité -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @livewire('admin.activity-logs')
    </div>
</div>
@endsection

@push('scripts')
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Top 5 projets avec détail workers/interims
            const topProjectsCtx = document.getElementById('topProjectsChart').getContext('2d');
            new Chart(topProjectsCtx, {
                type: 'bar',
                data: {
                    labels: @json($topProjects->pluck('name')),
                    datasets: [{
                            label: 'Salariés',
                            data: @json($topProjects->pluck('worker_hours')),
                            backgroundColor: '#3B82F6', // Bleu
                            borderWidth: 0,
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Intérims',
                            data: @json($topProjects->pluck('interim_hours')),
                            backgroundColor: '#10B981', // Vert
                            borderWidth: 0,
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                afterTitle: function(context) {
                                    const idx = context[0].dataIndex;
                                    const total = @json($topProjects->pluck('total_hours'))[idx];
                                    return `Total: ${total} h`;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' h';
                                }
                            }
                        }
                    }
                }
            });

            // Top 5 travailleurs
            const topWorkersCtx = document.getElementById('topWorkersChart').getContext('2d');
            new Chart(topWorkersCtx, {
                type: 'bar',
                data: {
                    labels: @json(
                        $topWorkers->map(function ($worker) {
                            return $worker->first_name . ' ' . $worker->last_name;
                        })),
                    datasets: [{
                        label: 'Heures travaillées',
                        data: @json($topWorkers->pluck('total_hours')),
                        backgroundColor: [
                            '#4F46E5', '#7C3AED', '#8B5CF6', '#A78BFA', '#C4B5FD'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' h';
                                }
                            }
                        }
                    }
                }
            });

            // Coûts par catégorie
            const costsByCategoryCtx = document.getElementById('costsByCategoryChart').getContext('2d');
            new Chart(costsByCategoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['MH', 'GO', 'Autre'],
                    datasets: [{
                        data: [
                            @json($costsByCategory['mh'] ?? 0),
                            @json($costsByCategory['go'] ?? 0),
                            @json($costsByCategory['other'] ?? 0)
                        ],
                        backgroundColor: [
                            '#4F46E5', '#7C3AED', '#8B5CF6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }

                                    const value = context.parsed;
                                    label += new Intl.NumberFormat('fr-FR', {
                                        style: 'currency',
                                        currency: 'EUR'
                                    }).format(value);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Préparer les données pour le graphique d'historique des heures
            @php
                $workersData = $monthlyWorkersData ?? [];
                $interimsData = $monthlyInterimsData ?? [];

                // Si les tableaux sont vides, utiliser des valeurs par défaut
                if (empty($workersData) && !empty($monthlyHoursData)) {
                    $workersData = $monthlyHoursData;
                }

                if (empty($interimsData)) {
                    $interimsData = array_fill(0, 6, 0);
                }
            @endphp

            // Tendance heures 6 derniers mois
            const hoursHistoryCtx = document.getElementById('hoursHistoryChart').getContext('2d');
            new Chart(hoursHistoryCtx, {
                type: 'line',
                data: {
                    labels: @json($monthlyHoursLabels),
                    datasets: [{
                            label: 'Heures totales',
                            data: @json($monthlyHoursData),
                            borderColor: '#4F46E5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            tension: 0.1,
                            fill: true
                        },
                        {
                            label: 'Salariés',
                            data: @json($workersData),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.1,
                            fill: false
                        },
                        {
                            label: 'Intérims',
                            data: @json($interimsData),
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.1,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' h';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script> -->
@endpush