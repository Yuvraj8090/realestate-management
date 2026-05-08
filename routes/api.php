<?php

use App\Http\Controllers\Admin\PropertyModerationController;
use App\Http\Controllers\Api\PropertyApiController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SavedSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/properties', [PropertyApiController::class, 'index'])->name('api.properties.index');
Route::get('/properties/{property}', [PropertyApiController::class, 'show'])->name('api.properties.show');
Route::post('/properties/{property}/inquiries', [InquiryController::class, 'store'])->name('api.properties.inquiries.store');

Route::middleware('auth')->group(function () {
    Route::post('/properties', [PropertyApiController::class, 'store'])->name('api.properties.store');
    Route::match(['put', 'patch'], '/properties/{property}', [PropertyApiController::class, 'update'])->name('api.properties.update');
    Route::delete('/properties/{property}', [PropertyApiController::class, 'destroy'])->name('api.properties.destroy');

    Route::get('/leads', [LeadController::class, 'index'])->name('api.leads.index');
    Route::patch('/leads/{lead}', [LeadController::class, 'update'])->name('api.leads.update');

    Route::post('/saved-searches', [SavedSearchController::class, 'store'])->name('api.saved-searches.store');
    Route::delete('/saved-searches/{savedSearch}', [SavedSearchController::class, 'destroy'])->name('api.saved-searches.destroy');

    Route::get('/admin/properties', [PropertyModerationController::class, 'index'])->name('api.admin.properties.index');
    Route::patch('/admin/properties/{property}/moderation', [PropertyModerationController::class, 'update'])->name('api.admin.properties.update');
    Route::post('/admin/properties/moderation/bulk', [PropertyModerationController::class, 'bulkUpdate'])->name('api.admin.properties.bulk');
});
