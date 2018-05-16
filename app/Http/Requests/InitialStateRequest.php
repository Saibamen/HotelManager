<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitialStateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rooms'    => 'required|numeric|min:1',
            'guests'   => 'required|numeric|min:1',
        ];
    }
}
