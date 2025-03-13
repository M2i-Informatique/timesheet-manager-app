@if ($activityLogs->isEmpty())
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
        <p>Aucun log d'activité n'a été trouvé. Assurez-vous que:</p>
        <ul class="list-disc ml-5 mt-2">
            <li>Des actions ont été effectuées sur les TimeSheetables</li>
            <li>Le trait LogsActivity est correctement configuré</li>
            <li>La table activity_log contient des données</li>
        </ul>
    </div>
@endif

<div class="mt-8">
    <h2 class="text-xl font-semibold mb-4">Journal d'activité récent</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Utilisateur</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activityLogs as $activity)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $activity->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ optional($activity->causer)->first_name . ' ' . optional($activity->causer)->last_name ?? 'Système' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $activity->description }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if ($activity->subject)
                                <details>
                                    <summary class="cursor-pointer text-indigo-600 hover:text-indigo-800">Voir les
                                        détails</summary>
                                    <div class="mt-2 pl-4 border-l-2 border-indigo-200">
                                        @if ($activity->properties->has('attributes'))
                                            <p class="font-semibold">Nouvelles valeurs:</p>
                                            <ul class="list-disc pl-5">
                                                @foreach ($activity->properties['attributes'] as $key => $value)
                                                    <li>
                                                        <span class="font-medium">{{ ucfirst($key) }}</span>:
                                                        @if ($key === 'date')
                                                            {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
                                                        @elseif($key === 'hours')
                                                            {{ $value }} h
                                                        @elseif($key === 'category')
                                                            {{ $value === 'day' ? 'Jour' : 'Nuit' }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if ($activity->properties->has('old'))
                                            <p class="font-semibold mt-2">Anciennes valeurs:</p>
                                            <ul class="list-disc pl-5">
                                                @foreach ($activity->properties['old'] as $key => $value)
                                                    <li>
                                                        <span class="font-medium">{{ ucfirst($key) }}</span>:
                                                        @if ($key === 'date')
                                                            {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
                                                        @elseif($key === 'hours')
                                                            {{ $value }} h
                                                        @elseif($key === 'category')
                                                            {{ $value === 'day' ? 'Jour' : 'Nuit' }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if ($activity->subject_type === 'App\\Models\\TimeSheetable')
                                            <p class="font-semibold mt-2">Informations supplémentaires:</p>
                                            <ul class="list-disc pl-5">
                                                <li>
                                                    <span class="font-medium">ID:</span> {{ $activity->subject_id }}
                                                </li>
                                                @if ($activity->subject && $activity->subject->project)
                                                    <li>
                                                        <span class="font-medium">Projet:</span>
                                                        {{ $activity->subject->project->code }} -
                                                        {{ $activity->subject->project->name }}
                                                    </li>
                                                @endif
                                                @if ($activity->subject && $activity->subject->timesheetable)
                                                    <li>
                                                        <span class="font-medium">Employé:</span>
                                                        @if ($activity->subject->timesheetable_type === 'App\\Models\\Worker')
                                                            {{ $activity->subject->timesheetable->first_name }}
                                                            {{ $activity->subject->timesheetable->last_name }}
                                                        @else
                                                            {{ $activity->subject->timesheetable->agency }} (Intérim)
                                                        @endif
                                                    </li>
                                                @endif
                                            </ul>
                                        @endif
                                    </div>
                                </details>
                            @else
                                Détails non disponibles
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Aucune activité enregistrée
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $activityLogs->links('vendor.livewire.custom-pagination') }}
    </div>
</div>
