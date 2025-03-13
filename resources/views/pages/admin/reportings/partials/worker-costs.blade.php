<!-- Report Worker Costs -->
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Travailleur
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Catégorie
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Coût total
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($reportData as $worker)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        {{ $worker['first_name'] }} {{ $worker['last_name'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if ($worker['category'] === 'worker')
                            Ouvrier
                        @elseif($worker['category'] === 'etam')
                            ETAM
                        @else
                            Autre
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        {{ number_format($worker['total_cost'], 2) }} €
                    </td>
                </tr>

                <!-- Timesheet details for this worker -->
                @if (isset($worker['timesheets']) && count($worker['timesheets']) > 0)
                    <tr class="bg-gray-50">
                        <td colspan="3" class="px-6 py-2">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th scope="col"
                                            class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Catégorie
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-1 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Heures
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-1 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Coût horaire
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-1 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Coût
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($worker['timesheets'] as $timesheet)
                                        <tr>
                                            <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($timesheet['date'])->format('d/m/Y') }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-500">
                                                {{ $timesheet['category'] === 'day' ? 'Jour' : 'Nuit' }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-right text-xs text-gray-500">
                                                {{ number_format($timesheet['hours'], 2) }} h
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-right text-xs text-gray-500">
                                                {{ number_format($timesheet['hourly_cost'], 2) }} €
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-right text-xs text-gray-500">
                                                {{ number_format($timesheet['cost'], 2) }} €
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
        <tfoot class="bg-gray-50">
            <tr>
                <th scope="col" colspan="2"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ number_format(array_sum(array_column($reportData, 'total_cost')), 2) }} €
                </th>
            </tr>
        </tfoot>
    </table>
</div>
