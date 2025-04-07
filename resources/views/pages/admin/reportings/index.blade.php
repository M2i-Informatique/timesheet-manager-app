@extends('pages.admin.index')

@section('title', 'Reporting')

@section('admin-content')
    <div class="container mx-auto py-8">
        <div class="flex justify-between mb-6">
            @php
                $titles = [
                    'project_hours' => 'Rapport des heures par chantier',
                    'worker_hours' => 'Rapport des heures par salarié',
                    'project_costs' => 'Rapport des coûts par chantier',
                    'worker_costs' => 'Rapport des coûts par salarié',
                ];
                $reportType = request()->get('report_type', 'default');
                $title = $titles[$reportType] ?? 'Rapports et analyses';
            @endphp
            <h1 class="text-2xl font-bold"><a href="">Tableau de bord > </a>{{ $title }}</h1>
            <x-buttons.dynamic tag="a" route="admin.reporting.index" routeParams="['view' => 'dashboard']"
                color="blue">
                Retour au tableau de bord
            </x-buttons.dynamic>
        </div>

        <!-- Formulaire de filtres -->
        <!-- <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form action="{{ route('admin.reporting.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"> -->
                    <!-- Type de rapport -->
                    <!-- <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Type de
                            rapport</label>
                        <select id="report_type" name="report_type"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="project_hours" {{ $reportType === 'project_hours' ? 'selected' : '' }}>Heures par
                                projet</option>
                            <option value="worker_hours" {{ $reportType === 'worker_hours' ? 'selected' : '' }}>Heures par
                                salarié</option>
                            <option value="project_costs" {{ $reportType === 'project_costs' ? 'selected' : '' }}>Coûts par
                                projet</option>
                            <option value="worker_costs" {{ $reportType === 'worker_costs' ? 'selected' : '' }}>Coûts par
                                salarié</option>
                        </select>
                    </div> -->

                    <!-- Période -->
                    <!-- <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div> -->

                    <!-- Projet (conditionnellement affiché) -->
                    <!-- <div id="project_filter"
                        class="{{ in_array($reportType, ['worker_hours', 'worker_costs']) ? 'hidden' : '' }}">
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Projet</label>
                        <select id="project_id" name="project_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Tous les projets</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                    {{ $project->code }} - {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div> -->

                    <!-- Travailleur (conditionnellement affiché) -->
                    <!-- <div id="worker_filter"
                        class="{{ in_array($reportType, ['project_hours', 'project_costs']) ? 'hidden' : '' }}">
                        <label for="worker_id" class="block text-sm font-medium text-gray-700 mb-1">Travailleur</label>
                        <select id="worker_id" name="worker_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Tous les salariés</option>
                            @foreach ($workers as $worker)
                                <option value="{{ $worker->id }}" {{ $workerId == $worker->id ? 'selected' : '' }}>
                                    {{ $worker->first_name }} {{ $worker->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div> -->

                    <!-- Catégorie (jour/nuit) -->
                    <!-- <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                        <select id="category" name="category"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Toutes</option>
                            <option value="day" {{ $category === 'day' ? 'selected' : '' }}>Jour</option>
                            <option value="night" {{ $category === 'night' ? 'selected' : '' }}>Nuit</option>
                        </select>
                    </div> -->
                <!-- </div> -->

                <!-- <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Générer le rapport
                    </button>
                </div>
            </form>
        </div> -->

        <!-- Résultats -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Tableaux de données -->
                <div class="w-full overflow-auto">
                    <!-- <h2 class="text-xl font-semibold mb-4">Détails</h2> -->

                    @if ($reportType === 'project_hours')
                        @include('pages.admin.reportings.partials.project-hours')
                    @elseif($reportType === 'worker_hours')
                        @include('pages.admin.reportings.partials.worker-hours')
                    @elseif($reportType === 'project_costs')
                        @include('pages.admin.reportings.partials.project-costs')
                    @elseif($reportType === 'worker_costs')
                        @include('pages.admin.reportings.partials.worker-costs')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle filters based on report type
            const reportTypeSelect = document.getElementById('report_type');
            const projectFilter = document.getElementById('project_filter');
            const workerFilter = document.getElementById('worker_filter');

            reportTypeSelect.addEventListener('change', function() {
                const reportType = this.value;

                if (reportType === 'project_hours' || reportType === 'project_costs') {
                    projectFilter.classList.remove('hidden');
                    workerFilter.classList.add('hidden');
                } else {
                    projectFilter.classList.add('hidden');
                    workerFilter.classList.remove('hidden');
                }
            });

            // Initialize chart
            const chartData = @json($chartData);
            const ctx = document.getElementById('reportChart').getContext('2d');

            if (chartData && chartData.labels && chartData.labels.length > 0) {
                new Chart(ctx, {
                    type: '{{ in_array($reportType, ['project_hours', 'worker_hours']) ? 'bar' : 'doughnut' }}',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }

                                        const value = context.parsed;
                                        const dataValue =
                                            '{{ in_array($reportType, ['project_costs', 'worker_costs']) }}' ===
                                            '1' ? context.parsed : context.parsed.y;

                                        if ('{{ in_array($reportType, ['project_costs', 'worker_costs']) }}' ===
                                            '1') {
                                            label += new Intl.NumberFormat('fr-FR', {
                                                style: 'currency',
                                                currency: 'EUR'
                                            }).format(dataValue);
                                        } else {
                                            label += dataValue + ' heures';
                                        }

                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                display: '{{ in_array($reportType, ['project_hours', 'worker_hours']) }}' ===
                                    '1'
                            },
                            y: {
                                display: '{{ in_array($reportType, ['project_hours', 'worker_hours']) }}' ===
                                    '1',
                                ticks: {
                                    callback: function(value) {
                                        if ('{{ in_array($reportType, ['project_costs', 'worker_costs']) }}' ===
                                            '1') {
                                            return new Intl.NumberFormat('fr-FR', {
                                                style: 'currency',
                                                currency: 'EUR'
                                            }).format(value);
                                        }
                                        return value + ' h';
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                document.getElementById('reportChart').parentElement.innerHTML =
                    '<div class="flex h-full items-center justify-center text-gray-500">Aucune donnée disponible pour la visualisation</div>';
            }
        });
    </script> -->
@endpush
