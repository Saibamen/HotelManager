<?php

namespace App\Providers;

use App\Services\GuestTableService;
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
    }
}
