<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\MsatuanController;
use App\Http\Controllers\MassetTransController;
use App\Http\Controllers\HistoryTransactionController;
use App\Http\Controllers\StockCardController;
use App\Http\Controllers\AssetReminderController;
use App\Http\Controllers\AuditController;

// AUTH
Route::get('/', fn() => redirect('/login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// PROTECTED ROUTES
Route::middleware(['auth'])->group(function () {

    // ASSET
    Route::prefix('asset')->name('asset.')->group(function () {

        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::post('/', [AssetController::class, 'store'])->name('store');

        // KATEGORI
        Route::prefix('kategori')->name('kategori.')->group(function () {
            Route::post('/', [AssetController::class, 'storeKategori'])->name('store');
            Route::put('/{id}', [AssetController::class, 'updateKategori'])->name('update');
            Route::delete('/{id}', [AssetController::class, 'deleteKategori'])->name('destroy');
        });

        // SUB KATEGORI
        Route::prefix('subkategori')->name('subkategori.')->group(function () {
            Route::post('/', [AssetController::class, 'storeSubKategori'])->name('store');
            Route::put('/{id}', [AssetController::class, 'updateSubKategori'])->name('update');
            Route::delete('/{id}', [AssetController::class, 'deleteSubKategori'])->name('destroy');
        });

        //Pemusnahan
        Route::post('/pemusnahan', [AssetController::class, 'pemusnahan'])
            ->name('pemusnahan');

        //Perbaikan
        Route::post('/qr/perbaikan', [AssetController::class, 'perbaikanQr'])
            ->name('qr.perbaikan');

        //Mutasi
        Route::post('/mutasi', [AssetController::class, 'mutasi'])
            ->name('mutasi');

        Route::get('/get-asset-by-status', [AssetController::class, 'getAssetByStatus']);
    });

    // MSATUAN
    Route::prefix('msatuan')->name('msatuan.')->group(function () {
        Route::get('/', [MsatuanController::class, 'index'])->name('index');
        Route::post('/', [MsatuanController::class, 'store'])->name('store');
        Route::put('/{id}', [MsatuanController::class, 'update'])->name('update');
        Route::delete('/{id}', [MsatuanController::class, 'destroy'])->name('destroy');
    });

    // MASSETTRANS
    Route::get('/asset/transaksi', [MassetTransController::class, 'index'])
        ->name('asset.trans.index');
    Route::post('/asset/transaksi', [MassetTransController::class, 'store'])
        ->name('asset.trans.store');
    Route::get(
        '/asset/transaksi/generate-kode',
        [MassetTransController::class, 'generateQrCode']
    )->name('asset.trans.generate');

    // HISTORY TRANSAKSI
    Route::get('/asset/history', [HistoryTransactionController::class, 'index'])
        ->name('Asset.history');
    Route::get('/asset/transaksi/ajax', [MassetTransController::class, 'transaksiAjax'])
        ->name('asset.transaksi.ajax');

    //Kartu Stok Controller
    Route::get('/kartu-stok', [StockCardController::class, 'index'])
        ->name('kartu.stok');

    // AUDIT (VIEW ONLY)
    Route::get('/audit', [AuditController::class, 'index'])
        ->name('audit.index');

    // ASSET REMINDER
    Route::prefix('asset/reminder')->name('asset.reminder.')->group(function () {
        Route::get('/', [AssetReminderController::class, 'index'])->name('index');
        Route::post('/', [AssetReminderController::class, 'store'])->name('store');
        Route::get('/{id}', [AssetReminderController::class, 'show'])->name('show');
        Route::delete('/{id}', [AssetReminderController::class, 'destroy'])->name('destroy');
        Route::put('/{id}', [AssetReminderController::class, 'update'])->name('update');
    });
});
