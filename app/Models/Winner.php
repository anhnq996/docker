<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'game_reward_id',
        'name',
        'email',
        'phone',
    ];

    protected $casts = [
        'game_id'        => 'integer',
        'game_reward_id' => 'integer',
        'name'           => 'string',
        'email'          => 'string',
        'phone'          => 'string',
        'created_at'     => 'datetime:Y-m-d H:i:s',
        'updated_at'     => 'datetime:Y-m-d H:i:s',
    ];
}
