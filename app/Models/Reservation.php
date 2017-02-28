<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Reservation.
 *
 * @property int $id
 * @property int $room_id
 * @property int $guest_id
 * @property string $date_start
 * @property string $date_end
 * @property int $people
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reservation whereDateEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reservation whereDateStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reservation whereGuestId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reservation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reservation wherePeople($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reservation whereRoomId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Reservation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    protected $fillable = [
        'room_id', 'guest_id', 'date_start', 'date_end', 'people',
    ];
}
