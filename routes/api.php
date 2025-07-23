<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TimesheetController;
use App\Http\Controllers\Api\UserController;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // User routes
    Route::get('/user/projects', [UserController::class, 'projects']);

    // Project routes
    Route::apiResource('projects', ProjectController::class);

    // Timesheet routes
    Route::get('/timesheets/{project}/{year}/{month}', [TimesheetController::class, 'show']);
    Route::apiResource('timesheet-entries', TimesheetController::class)->except(['show']);

    // Export routes
    Route::get('/exports/timesheet/{project}/{year}/{month}', [TimesheetController::class, 'export']);
});