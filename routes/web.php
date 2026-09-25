<?php

use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyRequirementController;
use App\Http\Controllers\PropertySearchController;
use App\Http\Controllers\RequirementResponseController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/properties', PropertySearchController::class)->name('properties.index');

Route::middleware('auth')->group(function () {
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
});

Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
Route::post('/properties/{property}/enquire', EnquiryController::class)->name('properties.enquire');

Route::middleware('auth')->group(function () {
    Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourites.index');
    Route::post('/favourites/{property}/toggle', [FavouriteController::class, 'toggle'])->name('favourites.toggle');

    Route::get('/requirements/post', [PropertyRequirementController::class, 'create'])->name('requirements.create');
    Route::get('/requirements/mine', [PropertyRequirementController::class, 'mine'])->name('requirements.mine');
    Route::get('/requirements/{propertyRequirement}/edit', [PropertyRequirementController::class, 'edit'])->name('requirements.edit');
    Route::post('/requirements/{propertyRequirement}/pause', [PropertyRequirementController::class, 'pause'])->name('requirements.pause');
    Route::post('/requirements/{propertyRequirement}/resume', [PropertyRequirementController::class, 'resume'])->name('requirements.resume');
    Route::post('/requirements/{propertyRequirement}/close', [PropertyRequirementController::class, 'close'])->name('requirements.close');
    Route::post('/requirements/{propertyRequirement}/fulfil', [PropertyRequirementController::class, 'fulfil'])->name('requirements.fulfil');
    Route::post('/requirements/{propertyRequirement}/renew', [PropertyRequirementController::class, 'renew'])->name('requirements.renew');
    Route::delete('/requirements/{propertyRequirement}', [PropertyRequirementController::class, 'destroy'])->name('requirements.destroy');
    Route::post('/requirements/{propertyRequirement}/respond', RequirementResponseController::class)->name('requirements.respond');
});

Route::get('/requirements/{propertyRequirement}', [PropertyRequirementController::class, 'show'])->name('requirements.show');

Route::view('/dashboard', 'marketplace.dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
