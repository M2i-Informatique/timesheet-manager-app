<!-- Report Worker Costs -->
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
                    'category' => 'worker',
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]) }}"
                class="px-3 py-1.5 text-sm {{ $category === 'worker' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                Ouvrier
            </a>
            <a
                href="{{ route('admin.reporting.index', [
                    'report_type' => $reportType,
                    'project_id' => $projectId,
                    'worker_id' => $workerId,
                    'category' => 'etam',
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]) }}"
                class="px-3 py-1.5 text-sm {{ $category === 'etam' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                ETAM
            </a>
        </span>
    </span>
</div>

<!-- KPI'S -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <!-- KPI Coût Total -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-green-500 hover:bg-green-50 transition-colors cursor-pointer">
        <div>
            <p class="text-md text-green-800 tracking-wider font-semibold">Coût Total</p>
            <p class="text-2xl font-medium text-gray-900">
                {{ number_format(array_sum(array_column($reportData, 'total_cost')), 2, ',', ' ') }}
                <span class="text-sm font-normal">€</span>
            </p>
        </div>
        @if(isset($costChangePercent) && $costChangePercent !== 0)
        <div class="inline-flex gap-2 rounded-sm {{ $costChangePercent > 0 ? 'bg-green-50 text-green-600 border border-green-600' : 'bg-red-50 text-red-600 border border-red-600' }} p-1">
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

    <!-- KPI Nombre de salariés -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer">
        <div>
            <p class="text-md text-blue-800 tracking-wider font-semibold">Nombre de salariés</p>
            <p class="text-2xl font-medium text-gray-900">
                {{ count($reportData) }}
            </p>
        </div>
    </article>

    <!-- KPI Coût moyen par salarié -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer">
        <div>
            <p class="text-md text-blue-800 tracking-wider font-semibold">Coût moyen</p>
            <p class="text-2xl font-medium text-gray-900">
                @if(count($reportData) > 0)
                {{ number_format(array_sum(array_column($reportData, 'total_cost')) / count($reportData), 2, ',', ' ') }}
                @else
                0,00
                @endif
                <span class="text-sm font-normal">€</span>
            </p>
            <p class="text-sm text-gray-500 mt-1">Par salarié</p>
        </div>
    </article>
</div>

<!-- Report Worker Costs -->
<div class="overflow-x-auto rounded-lg">
    <table class="min-w-full">
        <thead class="bg-blue-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-blue-600">
                    Salarié
                </th>
                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-blue-600">
                    Catégorie
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-md font-bold text-blue-600">
                    Coût total
                </th>
            </tr>
        </thead>
        <tbody class="bg-white">
            @forelse($reportData as $worker)
            <tr class="hover:bg-blue-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center">
                        {{ $worker['last_name'] }} {{ $worker['first_name'] }}
                        <button
                            class="text-black hover:text-blue-800 cursor-pointer focus:outline-none transition-all duration-200 ml-2 flex-shrink-0"
                            onclick="toggleWorkerDetails('{{ $worker['id'] ?? $loop->index }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 toggle-worker-icon-{{ $worker['id'] ?? $loop->index }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                    @if ($worker['category'] === 'worker')
                    Ouvrier
                    @elseif($worker['category'] === 'etam')
                    ETAM
                    @else
                    Autre
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-blue-600">
                    {{ number_format($worker['total_cost'], 2, ',', ' ') }} €
                </td>
            </tr>

            <!-- Timesheet details for this worker -->
            @if (isset($worker['timesheets']) && count($worker['timesheets']) > 0)
            <tr class="bg-blue-50 hidden worker-detail-{{ $worker['id'] ?? $loop->index }}">
                <td colspan="3" class="px-6 py-2 text-sm font-semibold text-blue-800">
                    Détails des feuilles de temps ({{ count($worker['timesheets']) }})
                </td>
            </tr>
            <tr class="bg-blue-50 hidden worker-detail-{{ $worker['id'] ?? $loop->index }}">
                <td colspan="3" class="px-6 py-2">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-2 py-1 text-left text-md font-medium text-gray-800">
                                    Date
                                </th>
                                <th scope="col"
                                    class="px-2 py-1 text-left text-md font-medium text-gray-800">
                                    Catégorie
                                </th>
                                <th scope="col"
                                    class="px-2 py-1 text-right text-md font-medium text-gray-800">
                                    Heures
                                </th>
                                <th scope="col"
                                    class="px-2 py-1 text-right text-md font-medium text-gray-800">
                                    Coût horaire
                                </th>
                                <th scope="col"
                                    class="px-2 py-1 text-right text-md font-medium text-gray-800">
                                    Coût
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($worker['timesheets'] as $timesheet)
                            <tr>
                                <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-800">
                                    {{ \Carbon\Carbon::parse($timesheet['date'])->format('d/m/Y') }}
                                </td>
                                <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-800">
                                    {{ $timesheet['category'] === 'day' ? 'Jour' : 'Nuit' }}
                                </td>
                                <td class="px-2 py-1 whitespace-nowrap text-right text-sm text-gray-800">
                                    {{ number_format($timesheet['hours'], 2, ',', ' ') }} h
                                </td>
                                <td class="px-2 py-1 whitespace-nowrap text-right text-sm text-gray-800">
                                    {{ number_format($timesheet['hourly_cost'], 2, ',', ' ') }} €
                                </td>
                                <td class="px-2 py-1 whitespace-nowrap text-right text-sm text-gray-800">
                                    {{ number_format($timesheet['cost'], 2, ',', ' ') }} €
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    Aucune donnée disponible pour la période sélectionnée.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    function toggleWorkerDetails(workerId) {
        const detailElements = document.querySelectorAll('.worker-detail-' + workerId);
        const icon = document.querySelector('.toggle-worker-icon-' + workerId);

        // Vérifier si les éléments sont visibles ou cachés
        const isHidden = detailElements.length > 0 && detailElements[0].classList.contains('hidden');

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
</script>