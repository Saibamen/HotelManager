<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Guest.
 *
 * @property int                        $id
 * @property string                     $first_name
 * @property string                     $last_name
 * @property string                     $address
 * @property string                     $zip_code
 * @property string                     $place
 * @property string                     $PESEL
 * @property string|null                $contact
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Room[] $rooms
 * @property-read int|null $rooms_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Guest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Guest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Guest query()
 * @method static \Illuminate\Database\Eloquent\Builder|Guest whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guest whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guest whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guest whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guest wherePESEL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guest wherePlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guest whereZipCode($value)
 * @mixin \Eloquent
 */
class Guest extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function reservations()
    {
        return $this->hasMany('App\Models\Reservation');
    }

    public function rooms()
    {
        return $this->belongsToMany('App\Models\Room', 'reservations');
    }
}
