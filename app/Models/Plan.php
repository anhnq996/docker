<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int id
 * @property string name
 * @property int price
 * @property object properties
 * @property Carbon created_at
 * @property Carbon updated_at
*/
class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'.
        'code',
        'price',
        'properties',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'properties' => 'array'
    ];
}
