<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\LoginController;

Route::post('/audit/store', [AuditController::class, 'apiStore']);
Route::post('/login-asset', [LoginController::class, 'apiLogin'])
    ->middleware('throttle:5,1');
Route::post('/audit/nonqr/store', [AuditController::class, 'apiStoreNonQr']);
Route::get('/cabang', [AuditController::class, 'apiCabang']);
Route::get('/asset-nonqr', [AuditController::class, 'apiAssetNonQr']);
