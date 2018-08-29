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
        Validator::extend('alpha_spaces_hyphens_apostrophes', function ($attribute, $value) {
            // Accept only alpha, spaces, hyphens and apostrophes.
            return preg_match('/^[\pL\s\-\']+$/u', $value);
        });

        Validator::extend('alpha_spaces_hyphens_apostrophes_parentheses_slashes_dots', function ($attribute, $value) {
            // Accept only alpha, spaces, hyphens, apostrophes, parentheses, slashes and dots.
            return preg_match('/^[\pL\s\-\'()\/.]+$/u', $value);
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
