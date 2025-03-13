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
                        {{ number_format($project['total_hours'], 2) }} h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        {{ number_format($project['total_cost'], 2) }} €
                    </td>
                </tr>

                <!-- Worker details for this project -->
                @foreach ($project['relationships']['workers'] as $worker)
                    <tr class="bg-gray-50">
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 pl-10">
                            {{ $worker['attributes']['first_name'] }} {{ $worker['attributes']['last_name'] }}
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                            @if ($worker['attributes']['category'] === 'worker')
                                Ouvrier
                            @elseif($worker['attributes']['category'] === 'etam')
                                ETAM
                            @else
                                Autre
                            @endif
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-500">
                            {{ number_format($worker['total_hours'], 2) }} h
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-500">
                            {{ number_format($worker['total_cost'], 2) }} €
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
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
                    {{ number_format(array_sum(array_column($reportData, 'total_hours')), 2) }} h
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ number_format(array_sum(array_column($reportData, 'total_cost')), 2) }} €
                </th>
            </tr>
        </tfoot>
    </table>
</div>
