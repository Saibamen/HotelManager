<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Config;
use Crypt;
use Illuminate\Http\Request;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->cookie('lang')) {
            $locale = Crypt::decrypt($request->cookie('lang'));
        } else {
            $locale = Config::get('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
