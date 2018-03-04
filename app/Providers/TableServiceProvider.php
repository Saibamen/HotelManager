<?php

namespace App\Providers;

use App\Services\GuestTableService;
use App\Services\ReservationTableService;
use App\Services\RoomTableService;
use Illuminate\Support\ServiceProvider;

class TableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\GuestTableService', function ($app) {
            return new GuestTableService();
        });
        $this->app->bind('App\Services\ReservationTableService', function ($app) {
            return new ReservationTableService();
        });
        $this->app->bind('App\Services\RoomTableService', function ($app) {
            return new RoomTableService();
        });
    }
}
