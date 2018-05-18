<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ReservationAddRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $carbon = new Carbon();
        $today = $carbon->today()->toDateString();
        $tomorrow = $carbon->tomorrow()->toDateString();

        return [
            'guest'        => 'required',
            'date_start'   => 'required|date|after_or_equal:'.$today.'|before:date_end',
            'date_end'     => 'required|date|after_or_equal:'.$tomorrow.'|after:date_start',
            'people'       => 'required|numeric|min:1|digits_between:1,2',
        ];
    }
}
