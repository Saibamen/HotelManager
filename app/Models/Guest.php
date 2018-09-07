<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Guest.
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property string $zip_code
 * @property string $place
 * @property string $PESEL
 * @property string|null $contact
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reservation[] $reservations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Room[] $rooms
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest wherePESEL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest wherePlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Guest whereZipCode($value)
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
