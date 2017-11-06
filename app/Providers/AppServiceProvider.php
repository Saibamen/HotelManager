<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * Custom validation rules
         */
        Validator::extend('alpha_spaces', function ($attribute, $value) {
            // Accept only alpha and spaces.
            return preg_match('/^[\pL\s]+$/u', $value);
        });

        Validator::extend('alpha_spaces_hyphens', function ($attribute, $value) {
            // Accept only alpha, spaces and hyphens.
            return preg_match('/^[\pL\s-]+$/u', $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
