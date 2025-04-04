{{-- resources/views/pages/tracking/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Saisie des heures')

@section('header')
    <div class="max-w-7xl mx-auto flex justify-between items-center mb-4 pt-24">
        <p class="text-lg">
            <a class="hover:text-blue-600 hover:underline" href="{{ route('tracking.index') }}">Pointage</a> > <span class="font-bold">{{ $project->code }} - {{ $project->name }} - {{ $project->city }}</span>
        </p>
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
                    $prevMonth = 12;
                    $prevYear--;
                }
                $nextMonth = $month + 1;
                $nextYear = $year;
                if ($nextMonth > 12) {
                    $nextMonth = 1;
                    $nextYear++;
                }
            @endphp

            {{-- Navigation par mois --}}
            <div class="flex items-center gap-2">
                <x-buttons.dynamic
                    route="tracking.show"
                    routeParams="[
                    'project_id' => $project->id,
                    'month' => $prevMonth,
                    'year' => $prevYear,
                    'category' => $category,
                ]" color="gray" class="text-gray-900 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                    </svg>
                </x-buttons.dynamic>

                {{-- Sélection du mois+année --}}
                <form id="periodSelector" action="{{ route('tracking.show') }}" method="get"
                    class="flex items-center gap-2">
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="category" value="{{ $category }}">

                    <select name="month_year" id="month_year" class="border-gray-300 rounded py-1">
                        @for ($y = $year - 1; $y <= $year + 1; $y++)
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}-{{ $y }}"
                                    {{ $m == $month && $y == $year ? 'selected' : '' }}>
                                    {{ $months[$m] }} {{ $y }}
                                </option>
                            @endfor
                        @endfor
                    </select>

                    <x-buttons.dynamic type="submit" color="blue">
                        Afficher
                    </x-buttons.dynamic>
                </form>

                <x-buttons.dynamic route="tracking.show" routeParams="[
                    'project_id' => $project->id,
                    'month' => $nextMonth,
                    'year' => $nextYear,
                    'category' => $category,
                ]" color="gray" class="text-gray-900 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </x-buttons.dynamic>
            </div>

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
        </div>

        {{-- Handsontable container --}}
        <div id="handsontable" class="w-full min-h-1/2"></div>

        {{-- Bouton Enregistrer --}}
        <div class="mt-4 flex justify-end">
            <x-buttons.dynamic id="saveBtn" color="green">
                Enregistrer
            </x-buttons.dynamic>
        </div>

        {{-- Tableau récap --}}
        @if (!empty($recap))
        <div class="mt-10">
            <h2 class="text-xl font-bold mb-3">Récapitulatif des heures</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Salarié</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Heures Jour</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Heures Nuit</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Total</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandTotalDay = 0;
                                $grandTotalNight = 0;
                            @endphp
                            @foreach ($recap as $r)
                                @php
                                    $grandTotalDay += $r['day_hours'];
                                    $grandTotalNight += $r['night_hours'];
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $r['full_name'] }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $r['day_hours'] ?: 0 }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $r['night_hours'] ?: 0 }}
                                    </td>
                                    <td class="px-4 py-2 text-sm font-semibold text-gray-700">
                                        {{ $r['total'] }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        {{-- Formulaire "Détacher" --}}
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
                                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                                                onclick="return confirm('Voulez-vous vraiment détacher cet employé ?')">
                                                Détacher
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            {{-- Ligne de cumul --}}
                            <tr class="bg-gray-100">
                                <td class="px-4 py-2 font-bold text-gray-600">Cumul du mois</td>
                                <td class="px-4 py-2 font-bold text-gray-700">{{ $grandTotalDay }}</td>
                                <td class="px-4 py-2 font-bold text-gray-700">{{ $grandTotalNight }}</td>
                                <td class="px-4 py-2 font-bold text-gray-800">
                                    {{ $grandTotalDay + $grandTotalNight }}
                                </td>
                                <td class="px-4 py-2"></td>
                            </tr>
                        </tbody>
                    </table>
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
                            // location.reload(); // si souhaité
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
