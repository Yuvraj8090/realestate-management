<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\PropertyModerationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SavedSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/my/properties', [PropertyController::class, 'manage'])->name('properties.manage');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::match(['put', 'patch'], '/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::patch('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');

    Route::post('/saved-searches', [SavedSearchController::class, 'store'])->name('saved-searches.store');
    Route::delete('/saved-searches/{savedSearch}', [SavedSearchController::class, 'destroy'])->name('saved-searches.destroy');
});

Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
Route::post('/properties/{property}/inquiries', [InquiryController::class, 'store'])->name('properties.inquiries.store');
Route::post('/properties/{property}/report', [PropertyController::class, 'report'])->middleware('auth')->name('properties.report');

Route::middleware(['auth', 'verified', 'role:'.UserRole::SuperAdmin->value])->group(function () {
    Route::get('/admin/properties', [PropertyModerationController::class, 'index'])->name('admin.properties.index');
    Route::patch('/admin/properties/{property}/moderation', [PropertyModerationController::class, 'update'])->name('admin.properties.update');
    Route::post('/admin/properties/moderation/bulk', [PropertyModerationController::class, 'bulkUpdate'])->name('admin.properties.bulk');
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
