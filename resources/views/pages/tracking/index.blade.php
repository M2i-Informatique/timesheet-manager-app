@extends('layouts.app')

@section('title', 'Tracking')

@section('content')
    <div class="max-w-screen-xl mx-auto pt-24 min-h-screen">
        <h1 class="text-2xl font-bold mb-4">Sélection du projet</h1>
        <form method="get" action="{{ route('tracking.show') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="project_id" class="block font-medium">Projet</label>
                    <select name="project_id" id="project_id" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Choisir --</option>
                        @foreach ($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->code }} - {{ $proj->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="month_year" class="block font-medium">Période</label>
                    <select name="month_year" id="month_year" class="w-full border-gray-300 rounded-md shadow-sm">
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
                        @endphp

                        @for ($y = 2023; $y <= 2030; $y++)
                            @for ($m = 1; $m <= 12; $m++)
                                @php
                                    $selected = $y == $year && $m == $month ? 'selected' : '';

                                    // Si on est en 2025, on affiche tous les mois sans restriction
                                    // Pour les autres années, on applique la restriction habituelle
                                    if ($y == date('Y') && $y != 2025 && $m > date('n')) {
                                        continue;
                                    }
                                @endphp
                                <option value="{{ $m }}-{{ $y }}" {{ $selected }}>
                                    {{ $months[$m] }} {{ $y }}
                                </option>
                            @endfor
                        @endfor
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Afficher
                </button>
            </div>

            {{-- Champ caché pour la catégorie, toujours défini sur "day" --}}
            <input type="hidden" name="category" value="day">
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Fonction pour diviser la valeur de month_year en month et year
                const monthYearSelect = document.getElementById('month_year');
                const form = monthYearSelect.closest('form');

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Récupérer la valeur sélectionnée et la séparer
                    const selectedValue = monthYearSelect.value;
                    const [month, year] = selectedValue.split('-');

                    // Créer des champs cachés pour month et year
                    const monthInput = document.createElement('input');
                    monthInput.type = 'hidden';
                    monthInput.name = 'month';
                    monthInput.value = month;

                    const yearInput = document.createElement('input');
                    yearInput.type = 'hidden';
                    yearInput.name = 'year';
                    yearInput.value = year;

                    // Ajouter les champs au formulaire
                    form.appendChild(monthInput);
                    form.appendChild(yearInput);

                    // Soumettre le formulaire
                    form.submit();
                });
            });
        </script>
    @endpush
@endsection
