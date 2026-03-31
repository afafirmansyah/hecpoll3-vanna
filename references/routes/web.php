<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\CardsController;
use App\Http\Controllers\VehiclesController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ReconciliationsController;
use App\Http\Controllers\DailyReportsController;
use App\Http\Controllers\UserController;

// Authentication routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth.session')->group(function () {
    Route::get('/dashboards', [DashboardController::class, 'index'])->middleware('permission:view_dashboard')->name('dashboard');
    Route::post('/dashboards', [DashboardController::class, 'index'])->middleware('permission:view_dashboard')->name('dashboard.filter');
    Route::get('/dashboard/terminal-settings', [DashboardController::class, 'getTerminalSettings'])->middleware('permission:edit_dashboard')->name('dashboard.terminal-settings');
    Route::post('/dashboard/terminal-settings', [DashboardController::class, 'updateTerminalSettings'])->middleware('permission:edit_dashboard')->name('dashboard.update-terminal-settings');
    
    // Cards
    Route::get('/cards', [CardsController::class, 'index'])->middleware('permission:view_cards')->name('cards');
    Route::get('/cards/export', [CardsController::class, 'export'])->middleware('permission:export_cards')->name('cards.export');
    
    // Vehicles
    Route::get('/vehicles', [VehiclesController::class, 'index'])->middleware('permission:view_vehicles')->name('vehicles');
    Route::get('/vehicles/export', [VehiclesController::class, 'export'])->middleware('permission:export_vehicles')->name('vehicles.export');
    
    // Transactions
    Route::get('/transactions', [TransactionsController::class, 'index'])->middleware('permission:view_transactions')->name('transactions');
    Route::get('/transactions/export', [TransactionsController::class, 'export'])->middleware('permission:export_transactions')->name('transactions.export');
    Route::post('/transactions/update-mileage', [TransactionsController::class, 'updateMileage'])->middleware('permission:update_mileage')->name('transactions.update-mileage');
    
    // Events
    Route::get('/events', [EventsController::class, 'index'])->middleware('permission:view_events')->name('events');
    Route::get('/events/export', [EventsController::class, 'export'])->middleware('permission:export_events')->name('events.export');
    
    // Reconciliations
    Route::get('/reconciliations', [ReconciliationsController::class, 'index'])->middleware('permission:view_reconciliations')->name('reconciliations');
    Route::get('/reconciliations/export', [ReconciliationsController::class, 'export'])->middleware('permission:export_reconciliations')->name('reconciliations.export');
    
    // Daily Reports
    Route::get('/daily-reports', [DailyReportsController::class, 'index'])->middleware('permission:view_daily_reports')->name('daily-reports');
    Route::get('/daily-reports/export', [DailyReportsController::class, 'export'])->middleware('permission:export_daily_reports')->name('daily-reports.export');
    
    // Daily Ratio
    Route::get('/daily-ratio', [\App\Http\Controllers\DailyRatioController::class, 'index'])->middleware('permission:view_daily_ratio')->name('daily-ratio');
    Route::get('/daily-ratio/export', [\App\Http\Controllers\DailyRatioController::class, 'export'])->middleware('permission:export_daily_ratio')->name('daily-ratio.export');
    Route::post('/daily-ratio/store', [\App\Http\Controllers\DailyRatioController::class, 'store'])->middleware('permission:manage_daily_ratio')->name('daily-ratio.store');
    Route::post('/daily-ratio/delete', [\App\Http\Controllers\DailyRatioController::class, 'delete'])->middleware('permission:manage_daily_ratio')->name('daily-ratio.delete');
    
    // User Management
    Route::middleware('permission:manage_users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
        Route::get('/users/{user}/permissions', [UserController::class, 'getPermissions'])->name('users.permissions.get');
        Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
        Route::get('/users/{user}/access', [UserController::class, 'getAccess'])->name('users.access.get');
        Route::put('/users/{user}/access', [UserController::class, 'updateAccess'])->name('users.access.update');
        
        // Role Management
        Route::get('/roles', [UserController::class, 'getRoles'])->name('roles.index');
        Route::post('/roles', [UserController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{role}', [UserController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [UserController::class, 'destroyRole'])->name('roles.destroy');
    });
    

    

    

});
