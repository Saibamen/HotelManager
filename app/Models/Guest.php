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
 * @property string $contact
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest whereContact($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest wherePESEL($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest wherePlace($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Guest whereZipCode($value)
 * @mixin \Eloquent
 */
class Guest extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact',
    ];
}
