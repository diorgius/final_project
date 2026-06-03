<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertiserController;
use App\Http\Controllers\WebmasterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin', [AdminController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboards.admin');
Route::get('/advertiser', [AdvertiserController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboards.advertiser');
Route::get('/webmaster', [WebmasterController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboards.webmaster');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
