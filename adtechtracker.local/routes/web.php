<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminMainController;
use App\Http\Controllers\AdvertiserController;
use App\Http\Controllers\WebmasterController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OfferThemeController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


require __DIR__ . '/auth.php';

// Welcome routes
Route::get('/', function () {
    return view('welcome');
});

// Set locale
Route::post('/locale', function (Request $request) {
    $request->validate([
        'locale' => ['required', Rule::in(array_keys(config('app.available_locales')))],
    ]);
    session([
        'locale' => $request->input('locale'),
    ]);
    return back();
})->name('locale');

// Common dashboard routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'verified', 'checkactive', 'checkadmin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/admin/main', [AdminMainController::class, 'index'])->name('admin.main');
    Route::patch('/admin/commission/{id}', [CommissionController::class, 'update'])->name('commission.update');
    Route::resource('/admin/users', AdminUserController::class)->except(['show', 'create']);
    Route::patch('/admin/users/{user}/restore', [AdminUserController::class, 'restore'])->name('users.restore');
    Route::get('/admin/offers', [OfferController::class, 'index'])->name('admin.offers');
    Route::post('/admin/offers/{offer}/status', [OfferController::class, 'status'])->name('admin.offers.status');;
    Route::delete('/admin/offers/{id}', [OfferController::class, 'destroy'])->name('admin.offers.destroy');
    Route::get('/admin/statistics', [StatisticController::class, 'index'])->name('admin.statistics');
    Route::get('/admin/statistics/summary', [StatisticController::class, 'summary']);
});

// Advertiser routes
Route::middleware(['auth', 'verified', 'checkactive', 'checkadvertiser'])->group(function () {
    Route::get('/advertiser', [AdvertiserController::class, 'index'])->name('dashboard');
    Route::get('/advertiser/offers', [OfferController::class, 'index'])->name('advertiser.offers');
    Route::post('/advertiser/offers/check', [OfferController::class, 'check'])->name('offers.check');
    Route::get('/advertiser/offers/create', [OfferController::class, 'create'])->name('offers.create');
    Route::post('/advertiser/offers/create', [OfferController::class, 'store'])->name('offers.store');
    Route::get('/advertiser/offers/{id}/edit', [OfferController::class, 'edit'])->name('offers.edit');
    Route::patch('/advertiser/offers/{id}/update', [OfferController::class, 'update'])->name('offers.update');
    Route::patch('/advertiser/offers/{id}/restore', [OfferController::class, 'restore'])->name('offers.restore');
    Route::delete('/advertiser/offers/{id}', [OfferController::class, 'destroy'])->name('advertiser.offers.destroy');
    Route::resource('/advertiser/offers/themes', OfferThemeController::class)->only(['index', 'store']);
    Route::post('/advertiser/offers/{offer}/status', [OfferController::class, 'status'])->name('advertiser.offers.status');
    Route::get('/advertiser/statistics', [StatisticController::class, 'index'])->name('advertiser.statistics');
    Route::get('/advertiser/statistics/summary', [StatisticController::class, 'summary']);
});

// Webmaster routes
Route::middleware(['auth', 'verified', 'checkactive', 'checkwebmaster'])->group(function () {
    Route::get('/webmaster', [WebmasterController::class, 'index'])->name('dashboard');
    Route::get('/webmaster/offers', [OfferController::class, 'index'])->name('webmaster.offers');
    Route::post('/webmaster/offers/{offer}/subscribe', [OfferController::class, 'subscribe']);
    Route::post('/webmaster/offers/{offer}/unsubscribe', [OfferController::class, 'unsubscribe']);
    Route::get('/r/{ref}', [RedirectController::class, 'handle']);
    Route::get('/webmaster/statistics', [StatisticController::class, 'index'])->name('webmaster.statistics');
    Route::get('/webmaster/statistics/summary', [StatisticController::class, 'summary']);
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/language', [ProfileController::class, 'updateLocale'])->name('profile.language.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Test routes
Route::middleware(['auth', 'checkadmin'])->get('/test-admin', fn() => response('OK'));
Route::middleware(['auth', 'checkadvertiser'])->get('/test-advertiser', fn() => response('OK'));
Route::middleware(['auth', 'checkwebmaster'])->get('/test-webmaster', fn() => response('OK'));
