<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DeviceTokenController;

Route::post('/audit/store', [AuditController::class, 'apiStore']);
Route::post('/login-asset', [LoginController::class, 'apiLogin'])
    ->middleware('throttle:5,1');
Route::post('/audit/nonqr/store', [AuditController::class, 'apiStoreNonQr']);
Route::post('/audit/qr/store', [AuditController::class, 'apiStoreQr']);
Route::get('/cabang', [AuditController::class, 'apiCabang']);
Route::get('/asset-nonqr', [AuditController::class, 'apiAssetNonQr']);

// Notifikasi FCM Routes
Route::post('/save-token', [DeviceTokenController::class, 'store']);
Route::post('/send-notif', [DeviceTokenController::class, 'sendNotif']);
