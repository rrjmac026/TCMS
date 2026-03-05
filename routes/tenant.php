<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Controllers — Auth
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;

// Controllers — Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminTrainersManagementController;
use App\Http\Controllers\Admin\AdminTraineesManagementController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\AdminTrainingScheduleController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminCertificateController;
use App\Http\Controllers\Admin\AdminUserController;

// Controllers — Trainer
use App\Http\Controllers\Trainer\TrainerController;
use App\Http\Controllers\Trainer\TrainerScheduleController;
use App\Http\Controllers\Trainer\TrainerAttendanceController;
use App\Http\Controllers\Trainer\TrainerAssessmentController;
use App\Http\Controllers\Trainer\TrainerTraineeController;

// Controllers — Trainee
use App\Http\Controllers\Trainee\TraineeController;
use App\Http\Controllers\Trainee\TraineeCourseController;
use App\Http\Controllers\Trainee\TraineeEnrollmentController;
use App\Http\Controllers\Trainee\TraineeScheduleController;
use App\Http\Controllers\Trainee\TraineeAssessmentController;
use App\Http\Controllers\Trainee\TraineeCertificateController;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // ── Guest auth (Breeze) ────────────────────────────────────────────────
    Route::middleware('guest')->group(function () {
        Route::get('register',  [RegisteredUserController::class, 'create'])->name('register');
        Route::post('register', [RegisteredUserController::class, 'store']);

        Route::get('login',  [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password',  [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password',         [NewPasswordController::class, 'store'])->name('password.store');
    });

    // ── Authenticated auth (Breeze) ────────────────────────────────────────
    Route::middleware('auth')->group(function () {
        Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')->name('verification.send');

        Route::get('confirm-password',  [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
        Route::put('password',          [PasswordController::class, 'update'])->name('password.update');
        Route::post('logout',           [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // ── Admin ──────────────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::resource('trainers', AdminTrainersManagementController::class);
        Route::resource('trainees', AdminTraineesManagementController::class);
        Route::resource('courses', AdminCourseController::class);
        Route::resource('enrollments', AdminEnrollmentController::class);
        Route::resource('training-schedules', AdminTrainingScheduleController::class);
        Route::resource('attendances', AdminAttendanceController::class);

        Route::get('certificates/{certificate}/preview',  [AdminCertificateController::class, 'preview'])->name('certificates.preview');
        Route::get('certificates/{certificate}/download', [AdminCertificateController::class, 'download'])->name('certificates.download');
        Route::resource('certificates', AdminCertificateController::class);
        Route::resource('users', AdminUserController::class);
    });

    // ── Trainer ────────────────────────────────────────────────────────────
    Route::prefix('trainer')->name('trainer.')->middleware(['auth', 'role:trainer'])->group(function () {
        Route::get('/dashboard', [TrainerController::class, 'dashboard'])->name('dashboard');
        Route::get('/schedules',                        [TrainerScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/{trainingSchedule}',     [TrainerScheduleController::class, 'show'])->name('schedules.show');
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
        Route::get('/certificates',                          [TraineeCertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}',            [TraineeCertificateController::class, 'show'])->name('certificates.show');
        Route::get('/certificates/{certificate}/download',   [TraineeCertificateController::class, 'download'])->name('certificates.download');
        Route::get('/certificates/{certificate}/preview',    [TraineeCertificateController::class, 'preview'])->name('certificates.preview');
    });

});