<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ⬇️ TAMBAHKAN INI
Schedule::command('app:send-kartu-stok-daily')
    ->dailyAt('07:00')
    ->appendOutputTo(storage_path('logs/kartu_stok.log'));

Schedule::command('app:send-kartu-stok-daily')
    ->dailyAt('15:35')
    ->appendOutputTo(storage_path('logs/kartu_stok.log'));
