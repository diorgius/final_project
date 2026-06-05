<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\AdvertiserController;
use App\Http\Controllers\WebmasterController;
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

Route::middleware(['auth', 'checkactive', 'checkadmin', 'verified'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('/admin/users', AdminUsersController::class);

});

// Route::get('/admin', [AdminController::class, 'index'])->middleware(['auth', 'checkadmin', 'verified'])->name('admin.dashboard');
Route::get('/advertiser', [AdvertiserController::class, 'index'])->middleware(['auth', 'checkactive', 'checkadvertiser', 'verified'])->name('advertiser.dashboard');
Route::get('/webmaster', [WebmasterController::class, 'index'])->middleware(['auth', 'checkactive', 'checkwebmaster', 'verified'])->name('webmaster.dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
