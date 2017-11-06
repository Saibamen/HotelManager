<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuestRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name'   => 'required|alpha_spaces|min:2',
            'last_name'    => 'required|alpha_spaces|min:2',
            'address'      => 'required|min:2',
            'zip_code'     => 'required|size:6',
            'place'        => 'required|alpha_spaces_hyphens',
            'PESEL'        => 'required|numeric|size:11',
            'contact'      => 'nullable',
        ];
    }
}
