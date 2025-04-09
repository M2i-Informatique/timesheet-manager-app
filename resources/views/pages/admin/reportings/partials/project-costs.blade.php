<!-- Report Project Costs -->
<!-- Boutons Mois -->
<div class="mb-8 flex items-center justify-between">
    <span
        class="inline-flex divide-x divide-gray-300 overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
        <!-- Bouton Mois précédent -->
        <a
            href="{{ route('admin.reporting.index', [
                'report_type' => $reportType,
                'project_id' => $projectId,
                'worker_id' => $workerId,
                'category' => $category,
                'start_date' => Carbon\Carbon::parse($startDate)->subMonth()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon\Carbon::parse($startDate)->subMonth()->endOfMonth()->format('Y-m-d')
            ]) }}"
            class="px-3 py-1.5 text-sm font-medium ransition-colors text-gray-700 hover:bg-blue-50 hover:text-gray-900 focus:relative inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 19l-7-7 7-7" />
            </svg>
            Précédent
        </a>

        <!-- Affichage du mois courant -->
        <span
            class="px-3 py-1.5 text-sm font-bold bg-blue-50 text-blue-700 flex items-center">
            {{ Carbon\Carbon::parse($startDate)->locale('fr')->translatedFormat('F Y') }}
        </span>

        <!-- Bouton Mois suivant -->
        <a
            href="{{ route('admin.reporting.index', [
                'report_type' => $reportType,
                'project_id' => $projectId,
                'worker_id' => $workerId,
                'category' => $category,
                'start_date' => Carbon\Carbon::parse($startDate)->addMonth()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon\Carbon::parse($startDate)->addMonth()->endOfMonth()->format('Y-m-d')
            ]) }}"
            class="px-3 py-1.5 text-sm font-medium transition-colors text-gray-700 hover:bg-blue-50 hover:text-gray-900 focus:relative inline-flex items-center gap-1">
            Suivant
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </span>

    <!-- Filtres par catégorie (droite) -->
    <span class="inline-flex items-center">
        <span class="mr-2 text-sm font-medium text-gray-700">Catégorie :</span>
        <span class="inline-flex divide-x divide-gray-300 overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
            <a
                href="{{ route('admin.reporting.index', [
                    'report_type' => $reportType,
                    'project_id' => $projectId,
                    'worker_id' => $workerId,
                    'category' => '',
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]) }}"
                class="px-3 py-1.5 text-sm {{ !$category ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                Tous
            </a>
            <a
                href="{{ route('admin.reporting.index', [
                    'report_type' => $reportType,
                    'project_id' => $projectId,
                    'worker_id' => $workerId,
                    'category' => 'mh',
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]) }}"
                class="px-3 py-1.5 text-sm {{ $category === 'mh' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                MH
            </a>
            <a
                href="{{ route('admin.reporting.index', [
                    'report_type' => $reportType,
                    'project_id' => $projectId,
                    'worker_id' => $workerId,
                    'category' => 'go',
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]) }}"
                class="px-3 py-1.5 text-sm {{ $category === 'go' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                GO
            </a>
        </span>
    </span>
</div>

<!-- KPI'S -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <!-- KPI Coût Total -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-green-500 hover:bg-green-50 transition-colors cursor-pointer">
        <div>
            <p class="text-md text-green-800 tracking-wider font-semibold">Coût Total</p>
            <p class="text-2xl font-medium text-gray-900">
                {{ number_format(array_sum(array_column($reportData, 'total_cost')), 2, ',', ' ') }}
                <span class="text-sm font-normal">€</span>
            </p>
            <p class="text-sm text-gray-500 mt-1">(uniquement salariés Dubocq)</p>
        </div>
        @if(isset($costChangePercent) && $costChangePercent !== 0)
        <div class="inline-flex gap-2 rounded-sm {{ $costChangePercent > 0 ? 'bg-green-100 text-green-600 border border-green-600' : 'bg-red-50 text-red-600 border border-red-600' }} p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                @if($costChangePercent > 0)
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                @endif
            </svg>
            <span class="text-sm font-medium">{{ number_format(abs($costChangePercent), 2, ',', ' ') }}%</span>
        </div>
        @endif
    </article>

    <!-- KPI Heures Salariés -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer">
        <div>
            <p class="text-md text-blue-800 tracking-wider font-semibold">Heures Salariés</p>
            <p class="text-2xl font-medium text-gray-900">
                {{ number_format(
                    array_sum(
                        array_map(function ($p) {
                            return $p['total_worker_hours'] ?? array_sum(array_column($p['relationships']['workers'] ?? [], 'total_hours'));
                        }, $reportData),
                    ),
                    2,
                    ',',
                    ' '
                ) }}
                <span class="text-sm font-normal">h</span>
            </p>
        </div>
        @if(isset($workerHoursChangePercent) && $workerHoursChangePercent !== 0)
        <div class="inline-flex gap-2 rounded-sm {{ $workerHoursChangePercent > 0 ? 'bg-green-100 text-green-600 border border-green-600' : 'bg-red-50 text-red-600 border border-red-600' }} p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                @if($workerHoursChangePercent > 0)
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                @endif
            </svg>
            <span class="text-xs font-medium">{{ number_format(abs($workerHoursChangePercent), 2, ',', ' ') }}%</span>
        </div>
        @endif
    </article>

    <!-- KPI Heures Totales -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer">
        <div>
            <p class="text-md text-blue-800 tracking-wider font-semibold">Heures Totales</p>
            <p class="text-2xl font-medium text-gray-900">
                {{ number_format(array_sum(array_column($reportData, 'total_hours')), 2, ',', ' ') }}
                <span class="text-sm font-normal">h</span>
            </p>
        </div>
        @if(isset($hoursChangePercent) && $hoursChangePercent !== 0)
        <div class="inline-flex gap-2 rounded-sm {{ $hoursChangePercent > 0 ? 'bg-green-100 text-green-600 border border-green-600' : 'bg-red-50 text-red-600 border border-red-600' }} p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                @if($hoursChangePercent > 0)
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                @endif
            </svg>
            <span class="text-xs font-medium">{{ number_format(abs($hoursChangePercent), 2, ',', ' ') }}%</span>
        </div>
        @endif
    </article>
</div>

<!-- Report Project Costs -->
<div class="overflow-x-auto mb-8 rounded-lg">
    <table class="min-w-full p-4">
        <thead class="bg-blue-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-blue-600">
                    Chantier
                </th>
                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-blue-600">
                    Catégorie
                </th>
                <th scope="col" class="px-6 py-3 text-right text-md font-bold text-blue-600">
                    Heures Salarié
                </th>
                <th scope="col" class="px-6 py-3 text-right text-md font-bold text-blue-600">
                    Heures Intérim
                </th>
                <th scope="col" class="px-6 py-3 text-right text-md font-bold text-blue-600">
                    Total s'heures
                </th>
                <th scope="col" class="px-6 py-3 text-right text-md font-bold text-blue-600">
                    Coût Total
                </th>
            </tr>
        </thead>
        <tbody class="bg-white">
            @forelse($reportData as $project)
            @if($project['total_cost'] > 0)
            <!-- Ligne principale du projet -->
            <tr class="hover:bg-blue-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center">
                        {{ $project['attributes']['code'] }} - {{ $project['attributes']['name'] }}
                        <button
                            class="text-black hover:text-blue-800 cursor-pointer focus:outline-none transition-all duration-200 ml-2 flex-shrink-0"
                            onclick="toggleDetails('{{ $project['id'] ?? $loop->index }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 toggle-icon-{{ $project['id'] ?? $loop->index }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if ($project['attributes']['category'] === 'mh')
                    MH
                    @elseif($project['attributes']['category'] === 'go')
                    GO
                    @else
                    Autre
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    {{ number_format($project['total_worker_hours'] ?? array_sum(array_column($project['relationships']['workers'] ?? [], 'total_hours')), 2, ',', ' ') }} h
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    {{ ($project['total_interim_hours'] ?? array_sum(array_column($project['relationships']['interims'] ?? [], 'total_hours'))) > 0 ? number_format($project['total_interim_hours'] ?? array_sum(array_column($project['relationships']['interims'] ?? [], 'total_hours')), 2, ',', ' ') . ' h' : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    {{ number_format($project['total_hours'], 2, ',', ' ') }} h
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-blue-600 font-bold">
                    {{ number_format($project['total_cost'], 2, ',', ' ') }} €
                    <!-- <div class="text-xs text-gray-500 font-normal">(uniquement salariés)</div> -->
                </td>
            </tr>

            <!-- En-tête de la section des salariés -->
            @if (isset($project['relationships']['workers']) && count($project['relationships']['workers']) > 0)
            <tr class="bg-blue-50 hidden detail-{{ $project['id'] ?? $loop->index }}">
                <td colspan="6" class="px-6 py-2 text-sm font-semibold text-blue-800">
                    Salariés ({{ count($project['relationships']['workers']) }})
                </td>
            </tr>

            <!-- Détails des salariés -->
            @foreach ($project['relationships']['workers'] as $worker)
            <tr class="bg-blue-50 hidden detail-{{ $project['id'] ?? $loop->index }}">
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700 pl-10">
                    {{ $worker['attributes']['last_name'] }} {{ $worker['attributes']['first_name'] }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700">
                    @if ($worker['attributes']['category'] === 'worker')
                    Ouvrier
                    @elseif($worker['attributes']['category'] === 'etam')
                    ETAM
                    @else
                    Autre
                    @endif
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                    {{ number_format($worker['total_hours'], 2, ',', ' ') }} h
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                    <!-- Vide pour les heures d'intérim -->
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                    {{ number_format($worker['total_hours'], 2, ',', ' ') }} h
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                    {{ number_format($worker['total_cost'], 2, ',', ' ') }} €
                </td>
            </tr>
            @endforeach
            @endif

            <!-- En-tête de la section des intérims -->
            @if (isset($project['relationships']['interims']) && count($project['relationships']['interims']) > 0)
            <tr class="bg-green-50 hidden detail-{{ $project['id'] ?? $loop->index }}">
                <td colspan="6" class="px-6 py-2 text-sm font-semibold text-green-800">
                    Intérims ({{ count($project['relationships']['interims']) }}) - Heures comptabilisées, coûts exclus
                </td>
            </tr>

            <!-- Détails des intérims -->
            @foreach ($project['relationships']['interims'] as $interim)
            <tr class="bg-green-50 hidden detail-{{ $project['id'] ?? $loop->index }}">
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700 pl-10">
                    {{ $interim['attributes']['agency'] }}
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700">
                    Intérim
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                    <!-- Vide pour les heures de salariés -->
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                    {{ number_format($interim['total_hours'], 2, ',', ' ') }} h
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                    {{ number_format($interim['total_hours'], 2, ',', ' ') }} h
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                    <span class="text-gray-400 italic">Non comptabilisé</span>
                </td>
            </tr>
            @endforeach
            @endif
            @endif
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    Aucune donnée disponible pour la période sélectionnée.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Histogramme des coûts mensuels -->
<div class="mt-12 mb-8">
    <div class="bg-white p-4 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Coûts mensuels pour l'année {{ now()->year }}</h3>
            <div class="flex items-center">
                <label for="projectSelector" class="sr-only">Chantier :</label>
                <select id="projectSelector" class="rounded-md border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Tous les chantiers</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->code }} - {{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <canvas id="monthlyCostsChart" height="110"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleDetails(projectId) {
        const detailElements = document.querySelectorAll('.detail-' + projectId);
        const icon = document.querySelector('.toggle-icon-' + projectId);
        
        // Vérifier si les éléments sont visibles ou cachés
        const isHidden = detailElements[0].classList.contains('hidden');
        
        // Basculer la visibilité de tous les éléments de détail
        detailElements.forEach(element => {
            if (isHidden) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        });
        
        // Changer l'icône
        if (isHidden) {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />';
        } else {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />';
        }
    }

    // Code pour l'histogramme des coûts mensuels
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier si l'élément existe pour éviter des erreurs
        const chartElement = document.getElementById('monthlyCostsChart');
        if (!chartElement) return;

        // Stocker les données initiales de tous les projets
        const originalLabels = JSON.parse('{{ json_encode($monthLabels) }}'.replace(/&quot;/g, '"'));
        const originalData = JSON.parse('{{ json_encode($monthlyTotalCosts) }}'.replace(/&quot;/g, '"'));

        // Déclarer les variables à l'intérieur de la fonction pour éviter qu'elles apparaissent dans la source
        let monthlyCostsChart;
        let defaultChartData = {
            labels: originalLabels,
            datasets: [{
                label: 'Coûts (€)',
                data: originalData,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        // Configuration du graphique
        const ctx = chartElement.getContext('2d');
        monthlyCostsChart = new Chart(ctx, {
            type: 'bar',
            data: defaultChartData,
            options: createChartOptions('Tous les chantiers')
        });

        // Ajouter l'écouteur d'événement pour le changement de projet
        const projectSelector = document.getElementById('projectSelector');
        if (projectSelector) {
            projectSelector.addEventListener('change', function() {
                updateChart(this.value);
            });
        }

        // Fonction pour créer les options du graphique
        function createChartOptions(title) {
            return {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }).format(value) + ' €';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Masquer la légende
                    },
                    title: {
                        display: true,
                        text: title,
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('fr-FR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }).format(context.raw) + ' €';
                            }
                        }
                    }
                }
            };
        }

        // Fonction pour mettre à jour le graphique
        function updateChart(projectId) {
            if (!projectId) {
                // Réinitialiser au graphique par défaut (tous les projets)
                // Utiliser les données originales pour garantir que nous avons toujours les bonnes valeurs
                monthlyCostsChart.data.labels = originalLabels;
                monthlyCostsChart.data.datasets[0].data = originalData;
                monthlyCostsChart.data.datasets[0].label = 'Coûts (€)';
                monthlyCostsChart.options = createChartOptions('Tous les chantiers');
                monthlyCostsChart.update();
                return;
            }

            // Afficher un indicateur de chargement
            monthlyCostsChart.data.datasets[0].data = Array(12).fill(0);
            monthlyCostsChart.data.datasets[0].label = 'Chargement...';
            monthlyCostsChart.update();

            // Récupérer les données du projet sélectionné
            fetch(`{{ route('admin.reporting.project-monthly-costs') }}?project_id=${projectId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    monthlyCostsChart.data.labels = data.labels;
                    monthlyCostsChart.data.datasets[0].data = data.costs;
                    monthlyCostsChart.data.datasets[0].label = `Coûts (€)`;

                    // Créer un titre sur une seule ligne avec toutes les informations
                    const singleLineTitle = `${data.project_code} - ${data.project_name} - ${data.project_address}`;

                    monthlyCostsChart.options = createChartOptions(singleLineTitle);
                    monthlyCostsChart.update();
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des données:', error);
                    monthlyCostsChart.data.datasets[0].label = 'Erreur de chargement';
                    monthlyCostsChart.update();
                    // Optionnel: afficher un message d'erreur à l'utilisateur
                    alert('Impossible de charger les données du chantier. Veuillez réessayer.');
                });
        }
    });
</script>