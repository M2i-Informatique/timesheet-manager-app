<!-- Report Project Costs -->

<!-- Sélecteur de mois -->
<div class="mb-8 flex items-center justify-between">
    <div class="flex items-center">
        <span class="mr-2 text-sm font-medium text-gray-700">Mois :</span>
        <select id="monthSelector" class="rounded-md border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" onchange="changeMonth(this.value)">
            @php
            $startYear = Carbon\Carbon::now()->subYear()->year;
            $endYear = Carbon\Carbon::now()->addYear()->year;
            $currentDate = Carbon\Carbon::parse($startDate);
            $currentMonth = $currentDate->month;
            $currentYear = $currentDate->year;

            // Ajustement de la plage en fonction de l'année actuellement sélectionnée
            if ($currentYear <= $startYear) {
                $startYear=$currentYear - 1;
                $endYear=$currentYear + 1;
                } elseif ($currentYear>= $endYear) {
                $startYear = $currentYear - 1;
                $endYear = $currentYear + 1;
                }
                @endphp

                @for ($year = $startYear; $year <= $endYear; $year++)
                    @for ($month=1; $month <=12; $month++)
                    @php
                    $date=Carbon\Carbon::createFromDate($year, $month, 1);
                    $monthName=$date->locale('fr')->translatedFormat('F Y');
                    $startOfMonth = $date->startOfMonth()->format('Y-m-d');
                    $endOfMonth = $date->endOfMonth()->format('Y-m-d');
                    $selected = ($year == $currentYear && $month == $currentMonth) ? 'selected' : '';
                    @endphp
                    <option value="{{ $startOfMonth }}|{{ $endOfMonth }}" {{ $selected }}>{{ $monthName }}</option>
                    @endfor
                    @endfor
        </select>
    </div>

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

<!-- Conteneur pour le contenu principal -->
<div id="contentContainer" class="">
    <!-- KPI'S -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <!-- KPI Coût Total -->
        <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-green-500 hover:bg-green-50 transition-colors cursor-pointer">
            <div>
                <p class="text-md text-green-800 tracking-wider font-semibold">Coût Total</p>
                <div id="costKpiLoading" class="hidden mt-2">
                    <div role="status">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="costKpiContent">
                    <p class="text-2xl font-medium text-gray-900">
                        {{ number_format(array_sum(array_column($reportData, 'total_cost')), 2, ',', ' ') }}
                        <span class="text-sm font-normal">€</span>
                    </p>
                    <p class="text-sm text-gray-500 mt-1">(uniquement salariés Dubocq)</p>
                </div>
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
                <div id="workerHoursKpiLoading" class="hidden mt-2">
                    <div role="status">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="workerHoursKpiContent">
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
                <span class="text-sm font-medium">{{ number_format(abs($workerHoursChangePercent), 2, ',', ' ') }}%</span>
            </div>
            @endif
        </article>

        <!-- KPI Heures Totales -->
        <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer">
            <div>
                <p class="text-md text-blue-800 tracking-wider font-semibold">Heures Totales</p>
                <div id="totalHoursKpiLoading" class="hidden mt-2">
                    <div role="status">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="totalHoursKpiContent">
                    <p class="text-2xl font-medium text-gray-900">
                        {{ number_format(array_sum(array_column($reportData, 'total_hours')), 2, ',', ' ') }}
                        <span class="text-sm font-normal">h</span>
                    </p>
                </div>
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
                <span class="text-sm font-medium">{{ number_format(abs($hoursChangePercent), 2, ',', ' ') }}%</span>
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
            <tbody class="bg-white" id="tableContent">
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
            <tbody id="tableLoading" class="hidden">
                <tr>
                    <td colspan="6" class="py-8">
                        <div class="flex justify-center items-center">
                            <div role="status">
                                <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                                </svg>
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </td>
                </tr>
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
            <div id="chartLoading" class="hidden flex justify-center items-center py-12">
                <div role="status">
                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div id="chartContainer">
                <canvas id="monthlyCostsChart" height="110"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fonction pour changer de mois avec le sélecteur et afficher le loader
    function changeMonth(value) {
        // Masquer les contenus et afficher les indicateurs de chargement
        document.getElementById('costKpiContent').classList.add('hidden');
        document.getElementById('costKpiLoading').classList.remove('hidden');

        document.getElementById('workerHoursKpiContent').classList.add('hidden');
        document.getElementById('workerHoursKpiLoading').classList.remove('hidden');

        document.getElementById('totalHoursKpiContent').classList.add('hidden');
        document.getElementById('totalHoursKpiLoading').classList.remove('hidden');

        document.getElementById('tableContent').classList.add('hidden');
        document.getElementById('tableLoading').classList.remove('hidden');

        document.getElementById('chartContainer').classList.add('hidden');
        document.getElementById('chartLoading').classList.remove('hidden');

        // Récupérer les dates de début et de fin
        const [startDate, endDate] = value.split('|');

        // Rediriger vers la nouvelle URL avec les paramètres mis à jour
        window.location.href = "{{ route('admin.reporting.index') }}" +
            "?report_type={{ $reportType }}" +
            "&project_id={{ $projectId }}" +
            "&worker_id={{ $workerId }}" +
            "&category={{ $category }}" +
            "&start_date=" + startDate +
            "&end_date=" + endDate;
    }

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

    // Masquer les indicateurs de chargement après chargement complet de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Masquer les indicateurs de chargement
        document.getElementById('costKpiLoading').classList.add('hidden');
        document.getElementById('workerHoursKpiLoading').classList.add('hidden');
        document.getElementById('totalHoursKpiLoading').classList.add('hidden');
        document.getElementById('tableLoading').classList.add('hidden');
        document.getElementById('chartLoading').classList.add('hidden');

        // Afficher les contenus
        document.getElementById('costKpiContent').classList.remove('hidden');
        document.getElementById('workerHoursKpiContent').classList.remove('hidden');
        document.getElementById('totalHoursKpiContent').classList.remove('hidden');
        document.getElementById('tableContent').classList.remove('hidden');
        document.getElementById('chartContainer').classList.remove('hidden');

        // Code pour l'histogramme des coûts mensuels
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

            // Afficher l'indicateur de chargement du graphique
            document.getElementById('chartContainer').classList.add('hidden');
            document.getElementById('chartLoading').classList.remove('hidden');

            // Récupérer les données du projet sélectionné
            fetch(`{{ route('admin.reporting.project-monthly-costs') }}?project_id=${projectId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Masquer l'indicateur de chargement
                    document.getElementById('chartLoading').classList.add('hidden');
                    document.getElementById('chartContainer').classList.remove('hidden');

                    monthlyCostsChart.data.labels = data.labels;
                    monthlyCostsChart.data.datasets[0].data = data.costs;
                    monthlyCostsChart.data.datasets[0].label = `Coûts (€)`;

                    // Créer un titre sur une seule ligne avec toutes les informations
                    const singleLineTitle = `${data.project_code} - ${data.project_name} - ${data.project_address}`;

                    monthlyCostsChart.options = createChartOptions(singleLineTitle);
                    monthlyCostsChart.update();
                })
                .catch(error => {
                    // Masquer l'indicateur de chargement en cas d'erreur
                    document.getElementById('chartLoading').classList.add('hidden');
                    document.getElementById('chartContainer').classList.remove('hidden');

                    console.error('Erreur lors du chargement des données:', error);
                    monthlyCostsChart.data.datasets[0].label = 'Erreur de chargement';
                    monthlyCostsChart.update();
                    // Optionnel: afficher un message d'erreur à l'utilisateur
                    alert('Impossible de charger les données du chantier. Veuillez réessayer.');
                });
        }
    });
</script>