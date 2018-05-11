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
            'first_name' => 'required|alpha_spaces_hyphens_apostrophes|min:2',
            'last_name'  => 'required|alpha_spaces_hyphens_apostrophes|min:2',
            'address'    => 'required|min:2',
            'zip_code'   => 'required|regex:/^\d{2}-\d{3}$/|size:6',
            'place'      => 'required|alpha_spaces_hyphens_apostrophes_parentheses_slashes_dots|min:2',
            'PESEL'      => 'required|digits:11',
            'contact'    => 'nullable|string',
        ];
    }
}
