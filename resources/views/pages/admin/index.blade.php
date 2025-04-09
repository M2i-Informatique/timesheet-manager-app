<!-- resources/views/pages/admin/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="flex flex-col gap-8">
    <!-- Zone où les pages enfants vont injecter leur contenu -->
    @yield('admin-content')
</div>
@endsection

@push('scripts')
<!-- Si tu as des scripts communs aux pages admin, tu peux les mettre ici -->
@endpush