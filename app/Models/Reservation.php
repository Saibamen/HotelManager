<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Reservation.
 *
 * @property int                        $id
 * @property int                        $room_id
 * @property int                        $guest_id
 * @property string                     $date_start
 * @property string                     $date_end
 * @property int                        $people
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Guest $guest
 * @property-read \App\Models\Room $room
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation getCurrentReservations()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation getFutureReservations()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation wherePeople($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    protected $fillable = [
        'room_id', 'guest_id', 'date_start', 'date_end', 'people',
    ];

    public function setDateStartAttribute($value)
    {
        $this->attributes['date_start'] = Carbon::parse($value);
    }

    public function setDateEndAttribute($value)
    {
        $this->attributes['date_end'] = Carbon::parse($value);
    }

    public function getDateStartAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y');
    }

    public function getDateEndAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y');
    }

    public function scopeGetCurrentReservations($query)
    {
        return $query->where('date_end', '>=', Carbon::today())
            ->where('date_start', '<=', Carbon::today());
    }

    public function scopeGetFutureReservations($query)
    {
        return $query->where('date_start', '>', Carbon::today());
    }

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    public function guest()
    {
        return $this->belongsTo('App\Models\Guest');
    }
}
