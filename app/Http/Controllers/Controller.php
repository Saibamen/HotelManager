<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected function getItemsPerPage()
    {
        return 20;
    }

    protected function addFlashMessage($message, $alertClass = null)
    {
        // Display only one message
        if (!Session::has('message')) {
            Session::flash('message', $message);

            if ($alertClass) {
                Session::flash('alert-class', $alertClass);
            }
        }
    }

    protected function returnBack($data)
    {
        // Zapobiegaj infinite loop
        if (back()->getTargetUrl() === url()->current()) {
            Log::info('Request loop: '.back()->getTargetUrl());

            return redirect(route('room.index'))->with($data);
        }

        return back()->with($data);
    }

    public function changeLanguage($language)
    {
        // 90 dni w minutach
        return back()->cookie('lang', $language, 129600);
    }
}
