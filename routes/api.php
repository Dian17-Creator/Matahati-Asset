<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BackofficeController;

Route::get('/logs/{userId}', [BackofficeController::class, 'apiLogs']);
Route::get('/schedule/{userId}', [ScheduleController::class, 'apiUserSchedule']);
Route::get('/schedule/today/{userId}', [ScheduleController::class, 'apiTodayShift']);
