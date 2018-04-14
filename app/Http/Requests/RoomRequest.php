<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number'   => 'required',
            'floor'    => 'required|numeric',
            'capacity' => 'required|numeric|min:1|digits_between:1,2',
            'price'    => 'required|numeric|min:0',
            'comment'  => 'nullable',
        ];
    }
}
