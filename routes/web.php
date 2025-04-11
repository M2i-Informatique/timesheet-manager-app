<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DriverProjectController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\InterimController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ReportingController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\NonWorkingDayController;
use App\Http\Controllers\Admin\ExportController;

Route::middleware(['verified', 'auth', 'role:driver|leader|admin|super-admin'])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');

    Route::get('/tracking/show', [TrackingController::class, 'show'])->name('tracking.show');

    Route::post('/tracking/store', [TrackingController::class, 'store'])->name('tracking.store');

    Route::post('/tracking/assign-employee', [TrackingController::class, 'assignEmployee'])
        ->name('tracking.assignEmployee');

    Route::delete('/tracking/detach-employee', [TrackingController::class, 'detachEmployee'])
        ->name('tracking.detachEmployee');

    Route::get('/exports/blank-monthly', [ExportController::class, 'exportBlankMonthly'])->name('exports.blank-monthly');
});

Route::middleware(['verified', 'auth', 'role:leader|admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
    // Reporting
    Route::get('/', [ReportingController::class, 'index'])->name('reporting.index');
    Route::get('dashboard', [ReportingController::class, 'dashboard'])->name('reporting.dashboard');
    Route::get('reporting/project-monthly-costs', [ReportingController::class, 'getProjectMonthlyCosts'])->name('reporting.project-monthly-costs');
});

Route::middleware(['verified', 'auth', 'role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('workers', WorkerController::class);
    Route::resource('interims', InterimController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('zones', ZoneController::class)->only(['index']);

    // Gestion de l'attribution des projets aux drivers
    Route::get('driver-projects', [DriverProjectController::class, 'index'])->name('driver-projects.index');
    Route::get('driver-projects/{driver}/edit', [DriverProjectController::class, 'edit'])->name('driver-projects.edit');
    Route::put('driver-projects/{driver}', [DriverProjectController::class, 'update'])->name('driver-projects.update');

    // Jours non travaillés
    Route::resource('non-working-days', NonWorkingDayController::class);
    Route::post('non-working-days/generate-french-holidays', [NonWorkingDayController::class, 'generateFrenchHolidays'])
        ->name('non-working-days.generate-french-holidays');

    // Exports
    Route::get('exports', [ExportController::class, 'index'])->name('exports.index');
    Route::post('exports/workers-monthly', [ExportController::class, 'exportWorkersMonthly'])->name('exports.workers-monthly');
    Route::post('exports/blank-monthly', [ExportController::class, 'exportBlankMonthly'])->name('exports.blank-monthly');
});

Route::middleware(['verified', 'auth', 'role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('settings', SettingController::class);
});