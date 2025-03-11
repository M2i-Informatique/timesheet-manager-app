<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'role:driver|admin|super-admin']], function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/tracking', function () {
        return view('pages.tracking.index');
    })->name('tracking');
});

Route::group(['middleware' => ['auth', 'role:admin|super-admin']], function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.index');
    })->name('dashboard');
});
