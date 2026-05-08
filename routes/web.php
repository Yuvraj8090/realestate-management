<?php

use App\Enums\UserRole;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:'.UserRole::SuperAdmin->value])->group(function () {
    Route::get('/admin/users', [DashboardController::class, 'admin'])->name('admin.users');
});

Route::middleware(['auth', 'verified', 'role:'.UserRole::Company->value])->group(function () {
    Route::get('/company/properties', [DashboardController::class, 'company'])->name('company.properties');
});

Route::middleware(['auth', 'verified', 'role:'.UserRole::PropertyOwner->value])->group(function () {
    Route::get('/owner/properties', [DashboardController::class, 'owner'])->name('owner.properties');
});

Route::middleware(['auth', 'verified', 'role:'.UserRole::Broker->value])->group(function () {
    Route::get('/broker/leads', [DashboardController::class, 'broker'])->name('broker.leads');
});

require __DIR__.'/auth.php';
