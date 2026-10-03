<?php

use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\RequirementController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Admin login is kept completely separate from the public OTP auth flow —
// same users table/web guard, but its own URL, controller, and view so
// admins never see public chrome and public users never land on admin UI.
Route::prefix('admin')->name('admin.')->middleware('guest')->group(function () {
    Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AdminAuthenticatedSessionController::class, 'store']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:super_admin|admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/', [PropertyController::class, 'index'])->name('index');
        Route::get('/{property}', [PropertyController::class, 'show'])->name('show');
        Route::post('/{property}/approve', [PropertyController::class, 'approve'])->name('approve');
        Route::post('/{property}/reject', [PropertyController::class, 'reject'])->name('reject');
        Route::post('/{property}/request-changes', [PropertyController::class, 'requestChanges'])->name('request-changes');
        Route::post('/{property}/mark-sold', [PropertyController::class, 'markSold'])->name('mark-sold');
        Route::post('/{property}/toggle-featured', [PropertyController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{property}/toggle-verified', [PropertyController::class, 'toggleVerified'])->name('toggle-verified');
        Route::post('/{property}/status', [PropertyController::class, 'updateStatus'])->name('status');
        Route::get('/{property}/documents/{media}', [PropertyController::class, 'downloadDocument'])->name('documents.download');
    });

    Route::prefix('requirements')->name('requirements.')->group(function () {
        Route::get('/', [RequirementController::class, 'index'])->name('index');
        Route::post('/{propertyRequirement}/close', [RequirementController::class, 'close'])->name('close');
    });

    Route::prefix('enquiries')->name('enquiries.')->group(function () {
        Route::get('/', [EnquiryController::class, 'index'])->name('index');
        Route::post('/{enquiry}/status', [EnquiryController::class, 'updateStatus'])->name('status');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('amenities')->name('amenities.')->group(function () {
        Route::get('/', [AmenityController::class, 'index'])->name('index');
        Route::post('/', [AmenityController::class, 'store'])->name('store');
        Route::delete('/{amenity}', [AmenityController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::post('/', [LocationController::class, 'store'])->name('store');
        Route::post('/{location}/toggle-featured', [LocationController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/{user}/role', [UserController::class, 'updateRole'])->name('role');
        Route::post('/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('toggle-active');
    });
});
