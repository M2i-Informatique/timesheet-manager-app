<!-- Report Project Hours -->
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
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer">
        <div>
            <p class="text-sm text-blue-800 tracking-wider font-semibold">Heures Salariés</p>
            <p class="text-2xl font-medium text-gray-900">
                {{ number_format(array_sum(array_column($reportData, 'worker_hours')), 2, ',', ' ') }}
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

    <!-- KPI Heures Intérim -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer">
        <div>
            <p class="text-sm text-blue-800 tracking-wider font-semibold">Heures Intérim</p>
            <p class="text-2xl font-medium text-gray-900">
                {{ number_format(array_sum(array_column($reportData, 'interim_hours')), 2, ',', ' ') }}
                <span class="text-sm font-normal">h</span>
            </p>
        </div>
        @if(isset($interimHoursChangePercent) && $interimHoursChangePercent !== 0)
        <div class="inline-flex gap-2 rounded-sm {{ $interimHoursChangePercent > 0 ? 'bg-green-100 text-green-600 border border-green-600' : 'bg-red-100 text-red-600 border border-red-600' }} p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                @if($interimHoursChangePercent > 0)
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                @endif
            </svg>
            <span class="text-xs font-medium">{{ number_format(abs($interimHoursChangePercent), 2, ',', ' ') }}%</span>
        </div>
        @endif
    </article>

    <!-- KPI Heures Totales -->
    <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer">
        <div>
            <p class="text-sm text-blue-800 tracking-wider font-semibold">Heures Totales</p>
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
</div>

<!-- Report Project Hours -->
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Chantier
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Catégorie
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Heures Salariés
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Heures Intérim
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total d'heures
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($reportData as $project)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center">
                            {{ $project['code'] }} - {{ $project['name'] }}
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
                        @if ($project['category'] === 'mh')
                            MH
                        @elseif($project['category'] === 'go')
                            GO
                        @else
                            Autre
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        {{ number_format($project['worker_hours'], 2, ',', ' ') }} h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        {{ number_format($project['interim_hours'], 2, ',', ' ') }} h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-blue-600 text-sm font-medium">
                        {{ number_format($project['total_hours'], 2, ',', ' ') }} h
                    </td>
                </tr>

                <!-- Worker details rows for this project -->
                @if (count($project['workers']) > 0)
                    <tr class="bg-blue-50 hidden detail-{{ $project['id'] ?? $loop->index }}">
                        <td colspan="5" class="px-6 py-2 text-sm font-semibold text-blue-800">
                            Travailleurs ({{ count($project['workers']) }})
                        </td>
                    </tr>

                    @foreach ($project['workers'] as $worker)
                        <tr class="bg-blue-50 hidden detail-{{ $project['id'] ?? $loop->index }}">
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700 pl-10">
                                {{ $worker['first_name'] }} {{ $worker['last_name'] }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700">
                                @if ($worker['category'] === 'worker')
                                    Ouvrier
                                @elseif($worker['category'] === 'etam')
                                    ETAM
                                @else
                                    Autre
                                @endif
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($worker['total_hours'], 2, ',', ' ') }} h
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                <!-- Aucune heure d'intérim pour un worker -->
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($worker['total_hours'], 2, ',', ' ') }} h
                            </td>
                        </tr>
                    @endforeach
                @endif

                <!-- Interim details rows for this project -->
                @if (count($project['interims']) > 0)
                    <tr class="bg-green-50 hidden detail-{{ $project['id'] ?? $loop->index }}">
                        <td colspan="5" class="px-6 py-2 text-sm font-semibold text-green-800">
                            Intérims ({{ count($project['interims']) }})
                        </td>
                    </tr>

                    @foreach ($project['interims'] as $interim)
                        <tr class="bg-green-50 hidden detail-{{ $project['id'] ?? $loop->index }}">
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700 pl-10">
                                {{ $interim['agency'] }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700">
                                Intérim
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                <!-- Aucune heure de worker pour un intérim -->
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($interim['total_hours'], 2, ',', ' ') }} h
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($interim['total_hours'], 2, ',', ' ') }} h
                            </td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        Aucune donnée disponible pour la période sélectionnée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

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
</script>