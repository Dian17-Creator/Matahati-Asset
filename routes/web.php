<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AssetController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // Route Assets----------------------------------------------------------------------------
    Route::get('/asset', [AssetController::class, 'index'])
        ->middleware('auth')
        ->name('asset.index');

    Route::post('/asset/store', [AssetController::class, 'store'])
        ->name('asset.store');

    Route::get('/asset/master', [AssetController::class, 'index']);

    Route::post('/asset/kategori', [AssetController::class, 'storeKategori'])
        ->name('asset.kategori.store');

    Route::post('/asset/subkategori', [AssetController::class, 'storeSubKategori'])
        ->name('asset.subkategori.store');
});
