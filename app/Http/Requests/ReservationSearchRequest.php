<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ReservationSearchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $carbon = new Carbon();
        $yesterday = $carbon->yesterday()->toDateString();
        $today = $carbon->today()->toDateString();

        return [
            'guest'        => 'required',
            'date_start'   => 'required|date|after:'.$yesterday.'|before:date_end',
            'date_end'     => 'required|date|after:'.$today.'|after:date_start',
            'people'       => 'required|numeric|min:1|digits_between:1,2',
        ];
    }
}
