<?php

use App\Http\Controllers\AdminController;
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

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'checkactive', 'checkadmin', 'verified'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('/admin/users', AdminUserController::class);
    Route::get('/admin/offers', [OfferController::class, 'index'])->name('admin.offers');
});

// Advertiser routes
// на офферы делаем контроллер, на главном экране выводим все созданные офферыЮ и кнопки создать темы офферов и создать оффер, а на него русурсный контроллер
Route::middleware(['auth', 'checkactive', 'checkadvertiser', 'verified'])->group(function () {
    Route::get('/advertiser', [AdvertiserController::class, 'index'])->name('advertiser.dashboard');
    Route::get('/advertiser/offers', [OfferController::class, 'index'])->name('advertiser.offers');
    Route::get('/advertiser/offers/create', [OfferController::class, 'create'])->name('offers.create');
    Route::post('/advertiser/offers/create', [OfferController::class, 'store'])->name('offers.store');
    Route::delete('/advertiser/offers/{id}', [OfferController::class, 'destroy'])->name('offers.destroy');
    Route::resource('/advertiser/offers/themes', OfferThemeController::class)->only(['index', 'store']);
    Route::get('/advertiser/statistics', [StatisticController::class, 'index'])->name('advertiser.statistics');
});


// Route::get('/admin', [AdminController::class, 'index'])->middleware(['auth', 'checkadmin', 'verified'])->name('admin.dashboard');
// Route::get('/advertiser', [AdvertiserController::class, 'index'])->middleware(['auth', 'checkactive', 'checkadvertiser', 'verified'])->name('advertiser.dashboard');
Route::get('/webmaster', [WebmasterController::class, 'index'])->middleware(['auth', 'checkactive', 'checkwebmaster', 'verified'])->name('webmaster.dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
