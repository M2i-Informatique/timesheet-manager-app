<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DriverProjectController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\InterimController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\TimeSheetController;
use App\Http\Controllers\Admin\ReportingController;

Route::middleware(['auth', 'role:driver|admin|super-admin', 'verified'])->group(function () {
    Route::get('/', function () {
        return view('pages.home');
    })->name('home');

    Route::get('/tracking', function () {
        return view('pages.tracking');
    })->name('tracking');

    // Route API pour récupérer les informations d'un projet
    Route::get('/api/projects/{id}', function ($id) {
        $project = App\Models\Project::with('zone')->findOrFail($id);
        return response()->json([
            'id' => $project->id,
            'code' => $project->code,
            'name' => $project->name,
            'address' => $project->address,
            'city' => $project->city,
            'zone_name' => $project->zone ? $project->zone->name : 'Inconnue'
        ]);
    });
});

Route::middleware(['auth', 'role:admin|super-admin', 'verified'])->group(function () {
    Route::get('/admin', function () {
        return view('pages.admin.index');
    })->name('admin');
});

Route::middleware(['auth', 'role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('workers', WorkerController::class);
    Route::resource('interims', InterimController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('zones', ZoneController::class);
    Route::resource('settings', SettingController::class);

    // Gestion de l'attribution des projets aux drivers
    Route::get('driver-projects', [DriverProjectController::class, 'index'])->name('driver-projects.index');
    Route::get('driver-projects/{driver}/edit', [DriverProjectController::class, 'edit'])->name('driver-projects.edit');
    Route::put('driver-projects/{driver}', [DriverProjectController::class, 'update'])->name('driver-projects.update');
});
