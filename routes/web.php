<?php

use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        // Landing / login page
        Route::get('/', function () {
            return view('superadmin.welcome');
        });

        // Guest auth routes (login only — no register for superadmin)
        Route::middleware('guest')->group(function () {
            Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
            Route::post('/login', [AuthenticatedSessionController::class, 'store']);

            Route::get('/forgot-password',  [PasswordResetLinkController::class, 'create'])->name('password.request');
            Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

            Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
            Route::post('/reset-password',         [NewPasswordController::class, 'store'])->name('password.store');
        });

        // Authenticated routes
        Route::middleware('auth')->group(function () {
            Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

            Route::get('/confirm-password',  [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
            Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);
            Route::put('/password',          [PasswordController::class, 'update'])->name('password.update');
        });

        // SuperAdmin protected routes
        Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
            Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

            Route::get('/tenants',                    [SuperAdminController::class, 'index'])->name('tenants.index');
            Route::get('/tenants/create',             [SuperAdminController::class, 'create'])->name('tenants.create');
            Route::post('/tenants',                   [SuperAdminController::class, 'store'])->name('tenants.store');
            Route::get('/tenants/{tenant}',           [SuperAdminController::class, 'show'])->name('tenants.show');
            Route::delete('/tenants/{tenant}',        [SuperAdminController::class, 'destroy'])->name('tenants.destroy');
            Route::post('/tenants/{tenant}/approve',  [SuperAdminController::class, 'approve'])->name('tenants.approve');
            Route::post('/tenants/{tenant}/reject',   [SuperAdminController::class, 'reject'])->name('tenants.reject');
            Route::post('/tenants/{tenant}/upgrade',  [SuperAdminController::class, 'upgrade'])->name('tenants.upgrade');
        });
    });
}