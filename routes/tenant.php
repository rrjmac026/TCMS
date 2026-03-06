<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Controllers — Auth
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\TenantLoginController;

// Controllers — Admin
use App\Http\Controllers\Tenant\Admin\AdminController;
use App\Http\Controllers\Tenant\Admin\AdminTrainersManagementController;
use App\Http\Controllers\Tenant\Admin\AdminTraineesManagementController;
use App\Http\Controllers\Tenant\Admin\AdminCourseController;
use App\Http\Controllers\Tenant\Admin\AdminEnrollmentController;
use App\Http\Controllers\Tenant\Admin\AdminTrainingScheduleController;
use App\Http\Controllers\Tenant\Admin\AdminAttendanceController;
use App\Http\Controllers\Tenant\Admin\AdminCertificateController;
use App\Http\Controllers\Tenant\Admin\AdminUserController;

// Controllers — Trainer
use App\Http\Controllers\Tenant\Trainer\TrainerController;
use App\Http\Controllers\Tenant\Trainer\TrainerScheduleController;
use App\Http\Controllers\Tenant\Trainer\TrainerAttendanceController;
use App\Http\Controllers\Tenant\Trainer\TrainerAssessmentController;
use App\Http\Controllers\Tenant\Trainer\TrainerTraineeController;

// Controllers — Trainee
use App\Http\Controllers\Tenant\Trainee\TraineeController;
use App\Http\Controllers\Tenant\Trainee\TraineeCourseController;
use App\Http\Controllers\Tenant\Trainee\TraineeEnrollmentController;
use App\Http\Controllers\Tenant\Trainee\TraineeScheduleController;
use App\Http\Controllers\Tenant\Trainee\TraineeAssessmentController;
use App\Http\Controllers\Tenant\Trainee\TraineeCertificateController;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });

    // ── Guest routes ───────────────────────────────────────────────────────
    Route::middleware('guest')->group(function () {
        Route::get('/login',  [TenantLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [TenantLoginController::class, 'login']);
    });

    // ── Authenticated routes ───────────────────────────────────────────────
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [TenantLoginController::class, 'logout'])->name('logout');

        Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update'); // ADD THIS
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // ── Admin ──────────────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::resource('trainers',          AdminTrainersManagementController::class);
        Route::resource('trainees',          AdminTraineesManagementController::class);
        Route::resource('courses',           AdminCourseController::class);
        Route::resource('enrollments',       AdminEnrollmentController::class);
        Route::resource('training-schedules', AdminTrainingScheduleController::class);
        Route::resource('attendances',       AdminAttendanceController::class);

        // Custom certificate routes BEFORE resource to avoid {certificate} conflict
        Route::get('certificates/{certificate}/preview',  [AdminCertificateController::class, 'preview'])->name('certificates.preview');
        Route::get('certificates/{certificate}/download', [AdminCertificateController::class, 'download'])->name('certificates.download');
        Route::resource('certificates', AdminCertificateController::class);
        Route::resource('users', AdminUserController::class);
    });

    // ── Trainer ────────────────────────────────────────────────────────────
    Route::prefix('trainer')->name('trainer.')->middleware(['auth', 'role:trainer'])->group(function () {
        Route::get('/dashboard', [TrainerController::class, 'dashboard'])->name('dashboard');
        Route::get('/schedules',                    [TrainerScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/{trainingSchedule}', [TrainerScheduleController::class, 'show'])->name('schedules.show');
        Route::resource('attendances', TrainerAttendanceController::class);
        Route::resource('assessments', TrainerAssessmentController::class);
        Route::get('/trainees',           [TrainerTraineeController::class, 'index'])->name('trainees.index');
        Route::get('/trainees/{trainee}', [TrainerTraineeController::class, 'show'])->name('trainees.show');
    });

    // ── Trainee ────────────────────────────────────────────────────────────
    Route::prefix('trainee')->name('trainee.')->middleware(['auth', 'role:trainee'])->group(function () {
        Route::get('/dashboard', [TraineeController::class, 'dashboard'])->name('dashboard');
        Route::get('/courses',                      [TraineeCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}',             [TraineeCourseController::class, 'show'])->name('courses.show');
        Route::post('/courses/{course}/enroll',     [TraineeCourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('/enrollments',                  [TraineeEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('/enrollments/{enrollment}',     [TraineeEnrollmentController::class, 'show'])->name('enrollments.show');
        Route::get('/schedules',                    [TraineeScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/{trainingSchedule}', [TraineeScheduleController::class, 'show'])->name('schedules.show');
        Route::get('/assessments',                  [TraineeAssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/assessments/{assessment}',     [TraineeAssessmentController::class, 'show'])->name('assessments.show');

        // Custom certificate routes BEFORE resource
        Route::get('/certificates/{certificate}/download', [TraineeCertificateController::class, 'download'])->name('certificates.download');
        Route::get('/certificates/{certificate}/preview',  [TraineeCertificateController::class, 'preview'])->name('certificates.preview');
        Route::get('/certificates',             [TraineeCertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [TraineeCertificateController::class, 'show'])->name('certificates.show');
    });

});