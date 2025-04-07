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
    <!-- KPI Heures Salariés -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full">
        <div>
            <p class="text-sm text-gray-800 tracking-wider font-semibold">Heures Salariés</p>
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
        <div class="inline-flex gap-2 rounded-sm {{ $workerHoursChangePercent > 0 ? 'bg-green-100 text-green-600 border border-green-600' : 'bg-red-100 text-red-600 border border-red-600' }} p-1">
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
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full">
        <div>
            <p class="text-sm text-gray-800 tracking-wider font-semibold">Heures Totales</p>
            <p class="text-2xl font-medium text-gray-900">
                {{ number_format(array_sum(array_column($reportData, 'total_hours')), 2, ',', ' ') }}
                <span class="text-sm font-normal">h</span>
            </p>
        </div>
        @if(isset($hoursChangePercent) && $hoursChangePercent !== 0)
        <div class="inline-flex gap-2 rounded-sm {{ $hoursChangePercent > 0 ? 'bg-green-100 text-green-600 border border-green-600' : 'bg-red-100 text-red-600 border border-red-600' }} p-1">
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

    <!-- KPI Coût Total -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full">
        <div>
            <p class="text-sm text-gray-800 tracking-wider font-semibold">Coût Total</p>
            <p class="text-2xl font-medium text-gray-900">
                {{ number_format(array_sum(array_column($reportData, 'total_cost')), 2, ',', ' ') }}
                <span class="text-sm font-normal">€</span>
            </p>
            <p class="text-xs text-gray-500 mt-1">(uniquement salariés Dubocq)</p>
        </div>
        @if(isset($costChangePercent) && $costChangePercent !== 0)
        <div class="inline-flex gap-2 rounded-sm {{ $costChangePercent > 0 ? 'bg-green-100 text-green-600 border border-green-600' : 'bg-red-100 text-red-600 border border-red-600' }} p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                @if($costChangePercent > 0)
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                @endif
            </svg>
            <span class="text-xs font-medium">{{ number_format(abs($costChangePercent), 2, ',', ' ') }}%</span>
        </div>
        @endif
    </article>
</div>

<!-- Tableau des coûts par chantier -->
<div class="overflow-x-auto rounded-lg mb-8">
    <table class="min-w-full divide-y divide-gray-200 bg-gray-50 p-4">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-sm text-gray-800 tracking-wider">
                    Chantiers
                </th>
                <th scope="col" class="px-6 py-3 text-righ text-sm text-gray-800 tracking-wider">
                    Catégorie
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-sm text-gray-800 tracking-wider">
                    Heures Salariés
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-sm text-gray-800 tracking-wider">
                    Heures Intérim
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-sm text-gray-800 tracking-wider">
                    Total heures
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-sm text-gray-800 tracking-wider">
                    Coût total
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($reportData as $project)
            @if($project['total_cost'] > 0)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center">
                    <span class="mr-2">{{ $project['attributes']['code'] }} - {{ $project['attributes']['name'] }}</span>
                    <button
                        class="text-black hover:text-blue-800 cursor-pointer focus:outline-none transition-all duration-200 ml-2 flex-shrink-0"
                        onclick="toggleDetails('project-{{ $project['id'] ?? $loop->index }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 toggle-icon-{{ $project['id'] ?? $loop->index }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-left text-sm text-gray-500">
                    @if ($project['attributes']['category'] === 'mh')
                    MH
                    @elseif($project['attributes']['category'] === 'go')
                    GO
                    @else
                    Autre
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    {{ number_format($project['total_worker_hours'] ?? array_sum(array_column($project['relationships']['workers'] ?? [], 'total_hours')), 2, ',', ' ') }}
                    h
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    {{ number_format($project['total_interim_hours'] ?? array_sum(array_column($project['relationships']['interims'] ?? [], 'total_hours')), 2, ',', ' ') }}
                    h
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    {{ number_format($project['total_hours'], 2, ',', ' ') }} h
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-indigo-600 font-bold">
                    {{ number_format($project['total_cost'], 2, ',', ' ') }} €
                    <div class="text-xs text-gray-500 font-normal">(uniquement salariés)</div>
                </td>
            </tr>

            <!-- Wrapper pour le contenu pliable -->
            <tr class="detail-row">
                <td colspan="6" class="p-0">
                    <div id="project-{{ $project['id'] ?? $loop->index }}" class="hidden">
                        <!-- Worker details for this project -->
                        @if (isset($project['relationships']['workers']))
                        <div class="bg-blue-50">
                            <div class="px-6 py-2 text-sm font-semibold text-blue-800">
                                Salariés ({{ count($project['relationships']['workers']) }})
                            </div>
                        </div>

                        @foreach ($project['relationships']['workers'] as $worker)
                        <div class="bg-blue-50 grid grid-cols-8">
                            <div class="px-6 py-2 whitespace-nowrap text-sm text-gray-700 pl-10">
                                {{ $worker['attributes']['first_name'] }} {{ $worker['attributes']['last_name'] }}
                            </div>
                            <div></div>
                            <div></div>
                            <div class="px-6 py-2 whitespace-nowrap text-sm text-gray-700">
                                @if ($worker['attributes']['category'] === 'worker')
                                Ouvrier
                                @elseif($worker['attributes']['category'] === 'etam')
                                ETAM
                                @else
                                Autre
                                @endif
                            </div>
                            <div class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($worker['total_hours'], 2, ',', ' ') }} h
                            </div>
                            <div class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                <!-- Aucune heure d'intérim pour un worker -->
                            </div>
                            <div></div>
                            <div class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($worker['total_cost'], 2, ',', ' ') }} €
                            </div>
                        </div>
                        @endforeach
                        @endif

                        <!-- Interim details for this project -->
                        @if (isset($project['relationships']['interims']))
                        <div class="bg-green-50">
                            <div class="px-6 py-2 text-sm font-semibold text-green-800">
                                Intérims ({{ count($project['relationships']['interims']) }}) - Heures comptabilisées,
                                coûts exclus
                            </div>
                        </div>

                        @foreach ($project['relationships']['interims'] as $interim)
                        <div class="bg-green-50 grid grid-cols-8">
                            <div class="px-6 py-2 whitespace-nowrap text-sm text-gray-700 pl-10">
                                {{ $interim['attributes']['agency'] }}
                            </div>
                            <div></div>
                            <div></div>
                            <div class="px-6 py-2 whitespace-nowrap text-sm text-gray-700">
                                Intérim
                            </div>
                            <div class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                <!-- Aucune heure de worker pour un interim -->
                            </div>
                            <div class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($interim['total_hours'], 2, ',', ' ') }} h
                            </div>
                            <div></div>
                            <div class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                <span class="text-gray-400 italic">Non comptabilisé</span>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    Aucune donnée disponible pour la période sélectionnée.
                </td>
            </tr>
            @endforelse
        </tbody>
        <!-- <tfoot class="bg-gray-50">
            <tr>
                <th scope="col" colspan="2"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ number_format(
                array_sum(
                    array_map(function ($p) {
                        return $p['total_worker_hours'] ??
                            array_sum(array_column($p['relationships']['workers'] ?? [], 'total_hours'));
                    }, $reportData),
                ),
                2,
                ',',
                ' '
            ) }}
                    h
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ number_format(
                array_sum(
                    array_map(function ($p) {
                        return $p['total_interim_hours'] ??
                            array_sum(array_column($p['relationships']['interims'] ?? [], 'total_hours'));
                    }, $reportData),
                ),
                2,
                ',',
                ' '
            ) }}
                    h
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ number_format(array_sum(array_column($reportData, 'total_hours')), 2, ',', ' ') }} h
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ number_format(array_sum(array_column($reportData, 'total_cost')), 2, ',', ' ') }} €
                    <div class="text-xs text-gray-500 font-normal">(uniquement salariés)</div>
                </th>
            </tr>
        </tfoot> -->
    </table>
</div>


<!-- Histogramme des coûts mensuels -->
<div class="mt-12 mb-8">
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
        <canvas id="monthlyCostsChart" height="110"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleDetails(elementId) {
        const element = document.getElementById(elementId);
        const projectId = elementId.split('-')[1];
        const icon = document.querySelector('.toggle-icon-' + projectId);

        if (element.classList.contains('hidden')) {
            element.classList.remove('hidden');
            element.classList.add('block');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />';
        } else {
            element.classList.remove('block');
            element.classList.add('hidden');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />';
        }
    }

    // Code pour l'histogramme des coûts mensuels
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier si l'élément existe pour éviter des erreurs
        const chartElement = document.getElementById('monthlyCostsChart');
        if (!chartElement) return;

        // Données pour l'histogramme
        const monthlyCostsData = {
            labels: JSON.parse('{{ json_encode($monthLabels) }}'.replace(/&quot;/g, '"')),
            datasets: [{
                label: 'Coûts (€) - {{ now()->year }}',
                data: JSON.parse('{{ json_encode($monthlyTotalCosts) }}'.replace(/&quot;/g, '"')),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        // Configuration du graphique
        const ctx = chartElement.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: monthlyCostsData,
            options: {
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
            }
        });
    });
</script>