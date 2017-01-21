<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'room_id', 'guest_id', 'date_start', 'date_end', 'people'
    ];
}
