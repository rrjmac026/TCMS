<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminTrainersManagementController;
use App\Http\Controllers\Trainer\TrainerController;
use App\Http\Controllers\Trainee\TraineeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');})->name('dashboard');

    Route::resource('trainers', AdminTrainersManagementController::class);
});

Route::prefix('trainer')->name('trainer.')->middleware(['auth', 'role:trainer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('trainer.dashboard');})->name('dashboard');
});

Route::prefix('trainee')->name('trainee.')->middleware(['auth', 'role:trainee'])->group(function () {
    Route::get('/dashboard', function () {
        return view('trainee.dashboard');})->name('dashboard');
});

require __DIR__.'/auth.php';
