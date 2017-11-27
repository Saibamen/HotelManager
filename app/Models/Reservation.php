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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Guest $guest
 * @property-read \App\Models\Room $room
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reservation whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reservation whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reservation whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reservation wherePeople($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reservation whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reservation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    protected $fillable = [
        'room_id', 'guest_id', 'date_start', 'date_end', 'people',
    ];

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    public function guest()
    {
        return $this->belongsTo('App\Models\Guest');
    }
}
