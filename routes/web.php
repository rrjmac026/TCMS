<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SuperAdminLoginController;
use App\Http\Controllers\Auth\SuperAdminRegisterController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController;
use App\Http\Controllers\ProfileController;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)
        ->middleware('web')
        ->group(function () {

        // ── Landing page ───────────────────────────────────────────────────
        Route::get('/', function () {
            return view('superadmin.welcome');
        });

        // ── Guest routes ───────────────────────────────────────────────────
        Route::middleware('guest')->group(function () {
            Route::get('/login',     [SuperAdminLoginController::class, 'showLoginForm'])->name('superadmin.login');
            Route::post('/login',    [SuperAdminLoginController::class, 'login']);
            Route::get('/register',  [SuperAdminRegisterController::class, 'showRegistrationForm'])->name('superadmin.register');
            Route::post('/register', [SuperAdminRegisterController::class, 'register']);
        });

        // ── Authenticated routes ───────────────────────────────────────────
        Route::middleware('auth')->group(function () {
            Route::post('/logout', [SuperAdminLoginController::class, 'logout'])->name('superadmin.logout');

            Route::get('/profile',          [ProfileController::class, 'edit'])->name('superadmin.profile.edit');
            Route::patch('/profile',        [ProfileController::class, 'update'])->name('superadmin.profile.update');
            Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('superadmin.profile.password.update');
            Route::delete('/profile',       [ProfileController::class, 'destroy'])->name('superadmin.profile.destroy');
        });

        // ── SuperAdmin protected routes ────────────────────────────────────
        Route::middleware(['auth', 'superadmin'])
            ->prefix('superadmin')
            ->name('superadmin.')
            ->group(function () {
                Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

                // Static routes BEFORE wildcard {tenant}
                Route::get('/tenants',        [SuperAdminController::class, 'index'])->name('tenants.index');
                Route::get('/tenants/create', [SuperAdminController::class, 'create'])->name('tenants.create');
                Route::post('/tenants',       [SuperAdminController::class, 'store'])->name('tenants.store');

                // Wildcard {tenant} routes AFTER static routes
                Route::get('/tenants/{tenant}',           [SuperAdminController::class, 'show'])->name('tenants.show');
                Route::delete('/tenants/{tenant}',        [SuperAdminController::class, 'destroy'])->name('tenants.destroy');
                Route::post('/tenants/{tenant}/approve',  [SuperAdminController::class, 'approve'])->name('tenants.approve');
                Route::post('/tenants/{tenant}/reject',   [SuperAdminController::class, 'reject'])->name('tenants.reject');
                Route::patch('/tenants/{tenant}/upgrade', [SuperAdminController::class, 'upgrade'])->name('tenants.upgrade');
                Route::get('/analytics', [SuperAdminAnalyticsController::class, 'index'])
                    ->name('analytics');
            });
    });
}