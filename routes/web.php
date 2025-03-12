<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DriverProjectController;

Route::middleware(['auth', 'role:driver|admin|super-admin', 'verified'])->group(function () {
    Route::get('/', function () {
        return view('pages.home');
    })->name('home');

    Route::get('/tracking', function () {
        return view('pages.tracking.index');
    })->name('tracking');
});

Route::middleware(['auth', 'role:admin|super-admin', 'verified'])->group(function () {
    Route::get('/admin', function () {
        return view('pages.admin.index');
    })->name('admin');
});

Route::middleware(['auth', 'role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);

    // Gestion de l'attribution des projets aux drivers
    Route::get('driver-projects', [DriverProjectController::class, 'index'])->name('driver-projects.index');
    Route::get('driver-projects/{driver}/edit', [DriverProjectController::class, 'edit'])->name('driver-projects.edit');
    Route::put('driver-projects/{driver}', [DriverProjectController::class, 'update'])->name('driver-projects.update');
});
