<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact'
    ];
}
