<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClassSchedulePageController;
use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\DashboardCashController;
use App\Http\Controllers\DashboardReportController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', UserDashboardController::class)->name('dashboard');
    Route::get('/dashboard/kas', DashboardCashController::class)->name('dashboard.cash');
    Route::get('/dashboard/laporan', [DashboardReportController::class, 'index'])->name('dashboard.reports');
    Route::get('/dashboard/laporan/unduh', [DashboardReportController::class, 'download'])->name('dashboard.reports.download');

    Route::get('/profile/complete', [CompleteProfileController::class, 'edit'])
        ->name('profile.complete');
    Route::post('/profile/complete', [CompleteProfileController::class, 'update'])
        ->name('profile.complete.store');
});

Route::get('/jadwal-kuliah', ClassSchedulePageController::class)->name('schedule');
