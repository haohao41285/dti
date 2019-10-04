<?php

namespace App\Providers;

use App\Models\MainTask;
use App\Observers\TaskObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\MainComboServiceBought;
use App\Observers\OrderObserver;
use App\Observers\TrackingHistoryObserver;
use App\Models\MainTrackingHistory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        MainComboServiceBought::observe(OrderObserver::class);
        MainTrackingHistory::observe(TrackingHistoryObserver::class);
        MainTask::observe(TaskObserver::class);
    }
}
