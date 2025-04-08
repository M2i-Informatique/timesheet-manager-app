{{-- resources/views/pages/tracking/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Saisie des heures')

@section('header')
<div class="max-w-7xl mx-auto flex justify-between items-center mb-6 pt-24">
    <h1 class="text-2xl">
        <a class="hover:text-blue-600 hover:underline font-bold" href="{{ route('tracking.index') }}">Pointage</a><span class="font-bold"> > {{ $project->code }} - {{ $project->name }} - {{ $project->city }}</span>
    </h1>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Message de succès --}}
    @if (session('success'))
    <div class="mb-4 p-2 bg-green-200 text-green-800 border border-green-300 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
        @php
        $months = [
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Août',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre',
        ];
        // Mois précédent / suivant
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth=12;
            $prevYear--;
            }
            $nextMonth=$month + 1;
            $nextYear=$year;
            if ($nextMonth> 12) {
            $nextMonth = 1;
            $nextYear++;
            }
            @endphp

            <span class="inline-flex divide-x divide-gray-300 overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
                <!-- Bouton Mois précédent -->
                <a href="{{ route('tracking.show', [
            'project_id' => $project->id,
            'month' => $prevMonth,
            'year' => $prevYear,
            'category' => $category,
            ]) }}"
                    class="px-3 py-1.5 text-sm font-medium transition-colors text-gray-700 hover:bg-blue-50 hover:text-gray-900 focus:relative inline-flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Précédent
                </a>

                <!-- Affichage du mois courant -->
                <span class="px-3 py-1.5 text-sm font-bold bg-blue-50 text-blue-700 flex items-center">
                    {{ \Carbon\Carbon::create($year, $month, 1)->locale('fr')->translatedFormat('F Y') }}
                </span>

                <!-- Bouton Mois suivant -->
                <a href="{{ route('tracking.show', [
            'project_id' => $project->id,
            'month' => $nextMonth,
            'year' => $nextYear,
            'category' => $category,
            ]) }}"
                    class="px-3 py-1.5 text-sm font-medium transition-colors text-gray-700 hover:bg-blue-50 hover:text-gray-900 focus:relative inline-flex items-center gap-1">
                    Suivant
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </span>

            {{-- Boutons Jour / Nuit --}}
            <div class="flex gap-4">
                <x-buttons.dynamic
                    tag="a"
                    route="tracking.show"
                    :routeParams="[
                        'project_id' => $project->id,
                        'month' => $month,
                        'year' => $year,
                        'category' => 'day',
                    ]"
                    color="{{ $category === 'day' ? 'green' : 'gray' }}"
                    class="{{ $category === 'day' ? 'text-green-500 bg-green-50 border-green-500' : 'text-gray-700' }}">
                    Jour
                </x-buttons.dynamic>

                <x-buttons.dynamic
                    tag="a"
                    route="tracking.show"
                    :routeParams="[
                        'project_id' => $project->id,
                        'month' => $month,
                        'year' => $year,
                        'category' => 'night',
                    ]"
                    color="{{ $category === 'night' ? 'purple' : 'gray' }}"
                    class="{{ $category === 'night' ? 'text-purple-500 bg-purple-50 border-purple-500' : 'text-gray-700' }}">
                    Nuit
                </x-buttons.dynamic>
            </div>
    </div>

    {{-- Handsontable container --}}
    <div id="handsontable" class="w-full min-h-1/2 mb-4"></div>

    {{-- Bouton Enregistrer --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
        {{-- Formulaires "Ajouter un salarié" --}}
        <div class="flex gap-6">
            {{-- + Worker --}}
            <form action="{{ route('tracking.assignEmployee') }}" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="category" value="{{ $category }}">
                <input type="hidden" name="employee_type" value="worker">

                <select name="employee_id" class="border-gray-300 rounded py-1">
                    <option value="">-- Salariés --</option>
                    @foreach ($availableWorkers as $w)
                    <option value="{{ $w->id }}">
                        {{ $w->last_name }} {{ $w->first_name }}
                    </option>
                    @endforeach
                </select>
                <x-buttons.dynamic type="submit">
                    Ajouter
                </x-buttons.dynamic>
            </form>

            {{-- + Interim --}}
            <form action="{{ route('tracking.assignEmployee') }}" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="category" value="{{ $category }}">
                <input type="hidden" name="employee_type" value="interim">

                <select name="employee_id" class="border-gray-300 rounded py-1">
                    <option value="">-- Intérim --</option>
                    @foreach ($availableInterims as $i)
                    <option value="{{ $i->id }}">
                        {{ $i->agency }} (Intérim)
                    </option>
                    @endforeach
                </select>
                <x-buttons.dynamic type="submit">
                    Ajouter
                </x-buttons.dynamic>
            </form>
        </div>
        <x-buttons.dynamic id="saveBtn">
            Enregistrer
        </x-buttons.dynamic>
    </div>

    {{-- Tableau récapitulatif --}}
    @if (!empty($recap))
    <div class="bg-white shadow rounded mb-6">
        <div class="mt-10">
            <!-- Zone de description et de recherche pour le récapitulatif -->
            <div class="flex justify-between gap-1 p-5 text-lg text-left rtl:text-right text-blue-600 bg-white">
                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <p class="text-sm font-normal">Récapitulatif des heures</p>
                </div>
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Salarié</th>
                            <th class="px-6 py-3">Heures Jour</th>
                            <th class="px-6 py-3">Heures Nuit</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recap as $r)
                        @if ($r['total'] > 0)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $r['full_name'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $r['day_hours'] ?: 0 }} h</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $r['night_hours'] ?: 0 }} h</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ $r['total'] }} h</td>
                            <td class="px-6 py-4 text-sm">
                                <form action="{{ route('tracking.detachEmployee') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                                    <input type="hidden" name="employee_type" value="{{ $r['model_type'] }}">
                                    <input type="hidden" name="employee_id" value="{{ $r['id'] }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <input type="hidden" name="category" value="{{ $category }}">
                                    <button type="submit"
                                        class="cursor-pointer text-red-500"
                                        onclick="return confirm('Voulez-vous vraiment détacher cet employé ?')">
                                        <x-icons.trash class="w-5 h-5" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Bloc KPI en dehors du tableau (en haut de la section récapitulatif) --}}
    @if(!empty($recap))
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <!-- KPI: Total heures travaillées -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-1">
                Total heures travaillées <span class="font-bold">( {{ \Carbon\Carbon::create($year, $month, 1)->locale('fr')->translatedFormat('F Y') }})</span>
            </h3>
            <p class="text-3xl font-bold text-indigo-700">
                {{ number_format($totalHoursCurrentMonth, 2, ',', ' ') }} h
            </p>
        </div>
        <!-- KPI: Salariés -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-medium text-blue-600 mb-1">Salariés Dubocq</h3>
            <p class="text-3xl font-bold text-blue-600">
                {{ number_format($totalWorkerHoursCurrentMonth, 2, ',', ' ') }} h
            </p>
            <p class="mt-2 text-sm text-gray-500">
                {{ $totalHoursCurrentMonth > 0 ? number_format(($totalWorkerHoursCurrentMonth / $totalHoursCurrentMonth) * 100, 1) : 0 }}%
            </p>
        </div>
        <!-- KPI: Intérims -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-medium text-green-600 mb-1">Intérims</h3>
            <p class="text-3xl font-bold text-green-600">
                {{ number_format($totalInterimHoursCurrentMonth, 2, ',', ' ') }} h
            </p>
            <p class="mt-2 text-sm text-gray-500">
                {{ $totalHoursCurrentMonth > 0 ? number_format(($totalInterimHoursCurrentMonth / $totalHoursCurrentMonth) * 100, 1) : 0 }}%
            </p>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let projectId = @json($project->id);
        let month = @json($month);
        let year = @json($year);
        let category = @json($category); // "day" ou "night"
        let daysInMonth = @json($daysInMonth);
        let entriesData = @json($entriesData);

        // Gérer la sélection "month_year" -> scinder en month & year
        const monthYearSelect = document.getElementById('month_year');
        const periodForm = document.getElementById('periodSelector');

        if (monthYearSelect && periodForm) {
            monthYearSelect.addEventListener('change', function() {
                const [m, y] = this.value.split('-');
                // Créer/MAJ champs cachés
                let monthInput = periodForm.querySelector('input[name="month"]');
                if (!monthInput) {
                    monthInput = document.createElement('input');
                    monthInput.type = 'hidden';
                    monthInput.name = 'month';
                    periodForm.appendChild(monthInput);
                }
                monthInput.value = m;

                let yearInput = periodForm.querySelector('input[name="year"]');
                if (!yearInput) {
                    yearInput = document.createElement('input');
                    yearInput.type = 'hidden';
                    yearInput.name = 'year';
                    periodForm.appendChild(yearInput);
                }
                yearInput.value = y;
            });
        }

        // Préparer data => [ [id, model_type, full_name, d1, d2,...], ... ]
        let hotData = entriesData.map(e => {
            let row = [e.id, e.model_type, e.full_name];
            for (let d = 1; d <= daysInMonth; d++) {
                row.push(e.days[d] ?? null);
            }
            return row;
        });

        // colHeaders
        let colHeaders = ["ID", "Type", "Employé"];
        for (let d = 1; d <= daysInMonth; d++) {
            // Optionnel : marquer weekend
            const date = new Date(year, month - 1, d);
            const dayOfWeek = date.getDay();
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
            colHeaders.push(isWeekend ? `${d}` : d.toString());
        }

        // columns
        let columns = [{
                data: 0,
                readOnly: true
            },
            {
                data: 1,
                readOnly: true
            },
            {
                data: 2,
                readOnly: true
            }
        ];
        for (let i = 0; i < daysInMonth; i++) {
            columns.push({
                data: i + 3,
                type: 'numeric',
                allowInvalid: false,
                validator: function(value, callback) {
                    if (value === null || value === '') {
                        // On autorise vide => "pas d'enregistrement"
                        callback(true);
                    } else {
                        let v = parseFloat(value);
                        callback(!isNaN(v) && v >= 0 && v <= 12);
                    }
                }
            });
        }

        let container = document.getElementById('handsontable');
        let hot = new Handsontable(container, {
            data: hotData,
            rowHeaders: true,
            colHeaders: colHeaders,
            columns: columns,
            hiddenColumns: {
                columns: [0, 1],
                indicators: false
            },
            fixedColumnsStart: 3,
            height: 'auto',
            licenseKey: 'non-commercial-and-evaluation',

            cells: function(row, col) {
                const cellProperties = {};

                // Appliquer un renderer => "abs" si value=0
                if (col >= 3) {
                    cellProperties.renderer = function(instance, td, row, col, prop, value) {
                        Handsontable.renderers.NumericRenderer.apply(this, arguments);

                        // Griser weekend
                        const dayNum = col - 2;
                        const date = new Date(year, month - 1, dayNum);
                        const dayOfWeek = date.getDay();
                        if (dayOfWeek === 0 || dayOfWeek === 6) {
                            td.style.backgroundColor = '#f0f0f0';
                        }

                        // Ajouter la coloration selon la valeur et la catégorie
                        if (value !== null && value !== '') {
                            if (parseFloat(value) === 0) {
                                td.textContent = 'abs';
                                td.style.backgroundColor = '#FFCCCC'; // bg-red-200
                            } else {
                                // Si catégorie est "night", utiliser violet, sinon vert
                                if (category === 'night') {
                                    td.style.backgroundColor = '#E9D5FF'; // bg-purple-200
                                } else {
                                    td.style.backgroundColor = '#CCFFCC'; // bg-green-200
                                }
                            }
                        }
                    };
                }

                return cellProperties;
            }
        });

        // Bouton "Enregistrer"
        let saveBtn = document.getElementById('saveBtn');
        saveBtn.addEventListener('click', function() {
            let updated = hot.getData();
            // => [ [id, model_type, full_name, d1, d2,...], ... ]

            let payload = updated.map(row => {
                let empId = row[0];
                let modelType = row[1];
                let daysArr = row.slice(3);
                let daysObj = {};

                for (let i = 0; i < daysArr.length; i++) {
                    let val = daysArr[i];
                    // si c'est '' ou null => on stocke null => on supprimera en base
                    if (val === null || val === '') {
                        daysObj[i + 1] = null;
                    } else {
                        daysObj[i + 1] = parseFloat(val); // 0..12
                    }
                }
                return {
                    id: empId,
                    model_type: modelType,
                    days: daysObj
                };
            });

            fetch("{{ route('tracking.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        project_id: projectId,
                        month: month,
                        year: year,
                        category: category,
                        data: payload
                    })
                })
                .then(resp => resp.json())
                .then(json => {
                    if (json.success) {
                        alert(json.message);
                        location.reload(); // si souhaité
                    } else {
                        alert("Erreur : " + json.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Erreur requête');
                });
        });
    });
</script>
@endpush