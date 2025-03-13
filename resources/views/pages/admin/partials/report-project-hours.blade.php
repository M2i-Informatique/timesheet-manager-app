<!-- Report Project Hours -->
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
                    Total d'heures
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($reportData as $project)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        {{ $project['code'] }} - {{ $project['name'] }}
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
                        {{ number_format($project['total_hours'], 2) }} h
                    </td>
                </tr>

                <!-- Worker details rows for this project -->
                @foreach ($project['workers'] as $worker)
                    <tr class="bg-gray-50">
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 pl-10">
                            {{ $worker['first_name'] }} {{ $worker['last_name'] }}
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                            &nbsp;
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-right text-sm text-gray-500">
                            {{ number_format($worker['total_hours'], 2) }} h
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
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
            </tr>
        </tfoot>
    </table>
</div>
