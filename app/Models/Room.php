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
 * @property string $comment
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Room whereCapacity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Room whereComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Room whereFloor($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Room whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Room whereNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Room wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Room extends Model
{
    protected $fillable = [
        'number', 'floor', 'capacity', 'price', 'comment',
    ];
}
