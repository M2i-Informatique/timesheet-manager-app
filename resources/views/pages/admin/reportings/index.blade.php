<!-- resources/views/pages/admin/reportings/index.blade.php -->
@extends('pages.admin.index')

@section('title', 'Reporting')

@php
$titles = [
    'project_hours' => 'Rapport des heures par chantier',
    'worker_hours' => 'Rapport des heures par salarié',
    'project_costs' => 'Rapport des coûts par chantier',
    'worker_costs' => 'Rapport des coûts par salarié',
];
$reportType = request()->get('report_type', 'default');
$title = $titles[$reportType] ?? 'Rapports et analyses';
$title = 'Tableau de bord > ' . $title;
@endphp

@section('page-title', $title)

@section('admin-content')
<div class="container mx-auto">
    <!-- Ton contenu de reporting (tableaux, graphiques, etc.) -->
    <div class="flex flex-col md:flex-row gap-6">
        <div class="w-full overflow-auto">
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
@endsection

@push('scripts')
<!-- Scripts spécifiques à cette page (Chart.js etc.) -->
@endpush