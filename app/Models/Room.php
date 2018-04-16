<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Room.
 *
 * @property int $id
 * @property string $number
 * @property int $floor
 * @property int $capacity
 * @property float $price
 * @property string|null $comment
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
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

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFreeRoomsForReservation($query, $dateStart, $dateEnd, $people)
    {
        return $query->whereNotIn('id', function ($query) use ($dateStart, $dateEnd) {
            $query->select('room_id')->from('reservations')
                ->where('date_start', '<', $dateEnd)
                ->where('date_end', '>', $dateStart);
            })
            ->where('capacity', '>=', $people)
            ->orderBy('capacity');
    }
}
