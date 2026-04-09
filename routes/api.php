<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\LoginController;

Route::post('/audit/store', [AuditController::class, 'apiStore']);
Route::post('/login-asset', [LoginController::class, 'apiLogin'])
    ->middleware('throttle:5,1');
