<!-- Report Project Costs -->
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Projet
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
                    Total heures
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Coût total
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($reportData as $project)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        {{ $project['attributes']['code'] }} - {{ $project['attributes']['name'] }}
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
                        {{ number_format($project['total_worker_hours'] ?? array_sum(array_column($project['relationships']['workers'] ?? [], 'total_hours')), 2) }}
                        h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        {{ number_format($project['total_interim_hours'] ?? array_sum(array_column($project['relationships']['interims'] ?? [], 'total_hours')), 2) }}
                        h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        {{ number_format($project['total_hours'], 2) }} h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-indigo-600 font-bold">
                        {{ number_format($project['total_cost'], 2) }} €
                        <div class="text-xs text-gray-500 font-normal">(uniquement salariés)</div>
                    </td>
                </tr>

                <!-- Worker details for this project -->
                @if (isset($project['relationships']['workers']))
                    <tr class="bg-blue-50">
                        <td colspan="6" class="px-6 py-2 text-sm font-semibold text-blue-800">
                            Salariés ({{ count($project['relationships']['workers']) }})
                        </td>
                    </tr>

                    @foreach ($project['relationships']['workers'] as $worker)
                        <tr class="bg-blue-50">
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700 pl-10">
                                {{ $worker['attributes']['first_name'] }} {{ $worker['attributes']['last_name'] }}
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
                                {{ number_format($worker['total_hours'], 2) }} h
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                <!-- Aucune heure d'intérim pour un worker -->
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($worker['total_hours'], 2) }} h
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($worker['total_cost'], 2) }} €
                            </td>
                        </tr>
                    @endforeach
                @endif

                <!-- Interim details for this project -->
                @if (isset($project['relationships']['interims']))
                    <tr class="bg-green-50">
                        <td colspan="6" class="px-6 py-2 text-sm font-semibold text-green-800">
                            Intérims ({{ count($project['relationships']['interims']) }}) - Heures comptabilisées,
                            coûts exclus
                        </td>
                    </tr>

                    @foreach ($project['relationships']['interims'] as $interim)
                        <tr class="bg-green-50">
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700 pl-10">
                                {{ $interim['attributes']['agency'] }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-700">
                                Intérim
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                <!-- Aucune heure de worker pour un intérim -->
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($interim['total_hours'], 2) }} h
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                {{ number_format($interim['total_hours'], 2) }} h
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-700">
                                <span class="text-gray-400 italic">Non comptabilisé</span>
                            </td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        Aucune donnée disponible pour la période sélectionnée.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="bg-gray-50">
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
                    ) }}
                    h
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ number_format(array_sum(array_column($reportData, 'total_hours')), 2) }} h
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ number_format(array_sum(array_column($reportData, 'total_cost')), 2) }} €
                    <div class="text-xs text-gray-500 font-normal">(uniquement salariés)</div>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
