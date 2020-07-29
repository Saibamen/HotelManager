<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * App\Models\Room.
 *
 * @property int                        $id
 * @property string                     $number
 * @property int                        $floor
 * @property int                        $capacity
 * @property float                      $price
 * @property string|null                $comment
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Guest[] $guests
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reservation[] $reservations
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room currentlyFreeRooms()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room currentlyOccupiedRooms()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room freeRoomsForReservation($dateStart, $dateEnd, $people)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Room extends Model
{
    protected $fillable = [
        'number', 'floor', 'capacity', 'price', 'comment',
    ];

    public function setPriceAttribute($value)
    {
        $value = str_replace(',', '.', $value);
        $this->attributes['price'] = round($value, 2);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $dateStart
     * @param $dateEnd
     * @param $people
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFreeRoomsForReservation($query, $dateStart, $dateEnd, $people)
    {
        return $query->whereNotIn('id', function (Builder $query) use ($dateStart, $dateEnd) {
            $query->select('room_id')->from('reservations')
                ->where('date_start', '<', $dateEnd)
                ->where('date_end', '>', $dateStart);
        })
            ->where('capacity', '>=', $people)
            ->orderBy('capacity');
    }

    public function scopeCurrentlyFreeRooms($query)
    {
        return $query->whereNotIn('id', function (Builder $query) {
            $query->select('room_id')->from('reservations')
                ->where('date_start', '<=', Carbon::today())
                ->where('date_end', '>', Carbon::today());
        });
    }

    public function scopeCurrentlyOccupiedRooms($query)
    {
        return $query->whereIn('id', function (Builder $query) {
            $query->select('room_id')->from('reservations')
                ->where('date_start', '<=', Carbon::today())
                ->where('date_end', '>', Carbon::today());
        });
    }

    public function isFree($dateStart, $dateEnd)
    {
        return !$this->reservations()
            ->where('date_start', '<', $dateEnd)
            ->where('date_end', '>', $dateStart)
            ->exists();
    }

    public function reservations()
    {
        return $this->hasMany('App\Models\Reservation');
    }

    public function guests()
    {
        return $this->belongsToMany('App\Models\Guest', 'reservations');
    }
}
