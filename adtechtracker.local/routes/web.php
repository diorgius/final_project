<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdvertiserController;
use App\Http\Controllers\WebmasterController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OfferThemeController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;


require __DIR__ . '/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'checkactive', 'checkadmin', 'verified'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::patch('/admin/commission/{id}', [CommissionController::class, 'update'])->name('commission.update');
    Route::resource('/admin/users', AdminUserController::class);
    Route::get('/admin/offers', [OfferController::class, 'index'])->name('admin.offers');
    Route::post('/admin/offers/{offer}/status', [OfferController::class, 'status'])->name('admin.offers.status');
    Route::delete('/admin/offers/{id}', [OfferController::class, 'destroy'])->name('admin.offers.destroy');
    Route::get('/admin/statistics', [StatisticController::class, 'index'])->name('admin.statistics');
});

// Advertiser routes
Route::middleware(['auth', 'checkactive', 'checkadvertiser', 'verified'])->group(function () {
    Route::get('/advertiser', [AdvertiserController::class, 'index'])->name('advertiser.dashboard');
    Route::get('/advertiser/offers', [OfferController::class, 'index'])->name('advertiser.offers');
    Route::get('/advertiser/offers/create', [OfferController::class, 'create'])->name('offers.create');
    Route::post('/advertiser/offers/create', [OfferController::class, 'store'])->name('offers.store');
    Route::post('/advertiser/offers/{offer}/status', [OfferController::class, 'status'])->name('advertiser.offers.status'); // переделать маршрут
    Route::delete('/advertiser/offers/{id}', [OfferController::class, 'destroy'])->name('advertiser.offers.destroy');
    Route::resource('/advertiser/offers/themes', OfferThemeController::class)->only(['index', 'store']);
    Route::get('/advertiser/statistics', [StatisticController::class, 'index'])->name('advertiser.statistics');
});

// Webmaster routes
Route::middleware(['auth', 'checkactive', 'checkwebmaster', 'verified'])->group(function () {
    Route::get('/webmaster', [WebmasterController::class, 'index'])->name('webmaster.dashboard');
    Route::get('/webmaster/offers', [OfferController::class, 'index'])->name('webmaster.offers');
    Route::post('/webmaster/offers/subscribe/{offer}', [OfferController::class, 'subscribe'])->name('webmaster.offers.subscribe');
    Route::post('/webmaster/offers/unsubscribe/{offer}', [OfferController::class, 'unsubscribe'])->name('webmaster.offers.unsubscribe');
    Route::get('/webmaster/statistics', [StatisticController::class, 'index'])->name('webmaster.statistics');
});
    
// Route::get('/admin', [AdminController::class, 'index'])->middleware(['auth', 'checkadmin', 'verified'])->name('admin.dashboard');
// Route::get('/advertiser', [AdvertiserController::class, 'index'])->middleware(['auth', 'checkactive', 'checkadvertiser', 'verified'])->name('advertiser.dashboard');
// Route::get('/webmaster', [WebmasterController::class, 'index'])->middleware(['auth', 'checkactive', 'checkwebmaster', 'verified'])->name('webmaster.dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
