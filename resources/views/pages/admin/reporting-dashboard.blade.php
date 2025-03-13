@extends('pages.admin.index')

@section('title', 'Tableau de bord - Reporting')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Tableau de bord</h1>
            <a href="{{ route('admin.reporting.index', ['report_type' => 'project_hours']) }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Voir les rapports détaillés
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- KPI: Total heures travaillées -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Heures travaillées (mois courant)</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($totalHoursCurrentMonth, 2) }} h</p>
                <div class="mt-2 text-sm text-gray-500">
                    @if ($hoursChangePercent > 0)
                        <span class="text-green-600">+{{ number_format($hoursChangePercent, 1) }}%</span>
                    @else
                        <span class="text-red-600">{{ number_format($hoursChangePercent, 1) }}%</span>
                    @endif
                    vs mois précédent
                </div>
            </div>

            <!-- KPI: Coût total -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Coût total (mois courant)</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($totalCostCurrentMonth, 2) }} €</p>
                <div class="mt-2 text-sm text-gray-500">
                    @if ($costChangePercent > 0)
                        <span class="text-red-600">+{{ number_format($costChangePercent, 1) }}%</span>
                    @else
                        <span class="text-green-600">{{ number_format($costChangePercent, 1) }}%</span>
                    @endif
                    vs mois précédent
                </div>
            </div>

            <!-- KPI: Projets actifs -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Projets actifs</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ $activeProjectsCount }}</p>
                <div class="mt-2 text-sm text-gray-500">
                    {{ $projectsWithActivityCount }} projets avec activité ce mois
                </div>
            </div>

            <!-- KPI: Travailleurs actifs -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Travailleurs actifs</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ $activeWorkersCount }}</p>
                <div class="mt-2 text-sm text-gray-500">
                    {{ $workersWithActivityCount }} avec pointage ce mois
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Top 5 projets par heures -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Top 5 projets (heures)</h3>
                <div style="height: 300px;">
                    <canvas id="topProjectsChart"></canvas>
                </div>
            </div>

            <!-- Top 5 travailleurs par heures -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Top 5 travailleurs (heures)</h3>
                <div style="height: 300px;">
                    <canvas id="topWorkersChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Répartition des coûts par catégorie de projet -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Coûts par catégorie</h3>
                <div style="height: 300px;">
                    <canvas id="costsByCategoryChart"></canvas>
                </div>
            </div>

            <!-- Tendance heures sur 6 derniers mois -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Tendance heures (6 derniers mois)</h3>
                <div style="height: 300px;">
                    <canvas id="hoursHistoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Liens rapides vers les rapports -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Rapports détaillés</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.reporting.index', ['report_type' => 'project_hours']) }}"
                    class="block p-4 border border-gray-200 rounded-md hover:border-indigo-500 hover:bg-indigo-50 transition-colors">
                    <h4 class="font-medium text-indigo-600 mb-2">Rapport heures par projet</h4>
                    <p class="text-sm text-gray-600">Analyse détaillée des heures par projet et par travailleur</p>
                </a>

                <a href="{{ route('admin.reporting.index', ['report_type' => 'worker_hours']) }}"
                    class="block p-4 border border-gray-200 rounded-md hover:border-indigo-500 hover:bg-indigo-50 transition-colors">
                    <h4 class="font-medium text-indigo-600 mb-2">Rapport heures par travailleur</h4>
                    <p class="text-sm text-gray-600">Heures travaillées par personne avec détail des pointages</p>
                </a>

                <a href="{{ route('admin.reporting.index', ['report_type' => 'project_costs']) }}"
                    class="block p-4 border border-gray-200 rounded-md hover:border-indigo-500 hover:bg-indigo-50 transition-colors">
                    <h4 class="font-medium text-indigo-600 mb-2">Rapport coûts par projet</h4>
                    <p class="text-sm text-gray-600">Analyse des coûts des projets avec répartition détaillée</p>
                </a>

                <a href="{{ route('admin.reporting.index', ['report_type' => 'worker_costs']) }}"
                    class="block p-4 border border-gray-200 rounded-md hover:border-indigo-500 hover:bg-indigo-50 transition-colors">
                    <h4 class="font-medium text-indigo-600 mb-2">Rapport coûts par travailleur</h4>
                    <p class="text-sm text-gray-600">Coûts générés par travailleur avec détail par pointage</p>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Top 5 projets
            const topProjectsCtx = document.getElementById('topProjectsChart').getContext('2d');
            new Chart(topProjectsCtx, {
                type: 'bar',
                data: {
                    labels: @json($topProjects->pluck('name')),
                    datasets: [{
                        label: 'Heures travaillées',
                        data: @json($topProjects->pluck('total_hours')),
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

            // Tendance heures 6 derniers mois
            const hoursHistoryCtx = document.getElementById('hoursHistoryChart').getContext('2d');
            new Chart(hoursHistoryCtx, {
                type: 'line',
                data: {
                    labels: @json($monthlyHoursLabels),
                    datasets: [{
                        label: 'Heures travaillées',
                        data: @json($monthlyHoursData),
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.1,
                        fill: true
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
        });
    </script>
@endpush
