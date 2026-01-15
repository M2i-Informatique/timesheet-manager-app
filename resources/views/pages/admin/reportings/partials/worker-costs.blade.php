<!-- Report Worker Costs -->

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
                $startYear = $currentYear - 1;
                $endYear = $currentYear + 1;
            } elseif ($currentYear >= $endYear) {
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
                    'category' => 'dubocq',
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]) }}"
                class="px-3 py-1.5 text-sm {{ $category === 'dubocq' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                Employés Dubocq
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
            <a
                href="{{ route('admin.reporting.index', [
                    'report_type' => $reportType,
                    'project_id' => $projectId,
                    'worker_id' => $workerId,
                    'category' => 'interim',
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]) }}"
                class="px-3 py-1.5 text-sm {{ $category === 'interim' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-50 hover:text-gray-900 font-medium' }} transition-colors focus:relative">
                Intérim
            </a>
        </span>
    </span>
</div>

<!-- Conteneur pour le contenu principal -->
<div id="contentContainer" class="">
    <!-- KPI'S -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <!-- KPI Coût Total -->
        <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-green-500 hover:bg-green-50 transition-colors cursor-pointer">
            <div>
                <p class="text-md text-green-800 tracking-wider font-semibold">Coût Total</p>
                <div id="totalCostKpiLoading" class="hidden mt-2">
                    <div role="status">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="totalCostKpiContent">
                    <p class="text-2xl font-medium text-gray-900">
                        {{ number_format(array_sum(array_column($reportData, 'total_cost')), 2, ',', ' ') }}
                        <span class="text-sm font-normal">€</span>
                    </p>
                </div>
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
                <div id="workerCountKpiLoading" class="hidden mt-2">
                    <div role="status">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="workerCountKpiContent">
                    <p class="text-2xl font-medium text-gray-900">
                        {{ count($reportData) }}
                    </p>
                </div>
            </div>
        </article>

        <!-- KPI Coût moyen par salarié -->
        <article class="flex items-end justify-between rounded-lg bg-gray-50 border border-gray-200 shadow-sm p-6 h-full hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer">
            <div>
                <p class="text-md text-blue-800 tracking-wider font-semibold">Coût moyen</p>
                <div id="avgCostKpiLoading" class="hidden mt-2">
                    <div role="status">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="avgCostKpiContent">
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
            <tbody class="bg-white" id="tableContent">
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
                        @elseif($worker['category'] === 'interim')
                        Intérim
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
            <tbody id="tableLoading" class="hidden">
                <tr>
                    <td colspan="3" class="py-8">
                        <div class="flex justify-center items-center">
                            <div role="status">
                                <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                </svg>
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Fonction pour changer de mois avec le sélecteur et afficher les indicateurs de chargement
    function changeMonth(value) {
        // Masquer les contenus et afficher les indicateurs de chargement
        document.getElementById('totalCostKpiContent').classList.add('hidden');
        document.getElementById('totalCostKpiLoading').classList.remove('hidden');
        
        document.getElementById('workerCountKpiContent').classList.add('hidden');
        document.getElementById('workerCountKpiLoading').classList.remove('hidden');
        
        document.getElementById('avgCostKpiContent').classList.add('hidden');
        document.getElementById('avgCostKpiLoading').classList.remove('hidden');
        
        document.getElementById('tableContent').classList.add('hidden');
        document.getElementById('tableLoading').classList.remove('hidden');
        
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
    
    // Masquer les indicateurs de chargement après chargement complet de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Masquer les indicateurs de chargement
        document.getElementById('totalCostKpiLoading').classList.add('hidden');
        document.getElementById('workerCountKpiLoading').classList.add('hidden');
        document.getElementById('avgCostKpiLoading').classList.add('hidden');
        document.getElementById('tableLoading').classList.add('hidden');
        
        // Afficher les contenus
        document.getElementById('totalCostKpiContent').classList.remove('hidden');
        document.getElementById('workerCountKpiContent').classList.remove('hidden');
        document.getElementById('avgCostKpiContent').classList.remove('hidden');
        document.getElementById('tableContent').classList.remove('hidden');
    });
</script>