<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use App\Models\Mrequest;
use App\Models\MscanManual;
use App\Models\Tusercontract;
use App\Observers\TusercontractObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Tusercontract::observe(TusercontractObserver::class);
        Carbon::setLocale('id');
    }
}
