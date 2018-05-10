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
            'number'   => 'required|max:5|unique:rooms,number,'.(int) $this->id.',id,floor,'.$this->floor,
            'floor'    => 'required|numeric|min:-999|max:999',
            'capacity' => 'required|numeric|min:1|digits_between:1,2',
            'price'    => 'required|regex:[^\d+[.,]?\d*$]|max:15',
            'comment'  => 'nullable|string',
        ];
    }
}
