<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $table = 'player';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'ip',
        'user_agent',
        'game_id',
        'turn',
    ];

    protected $casts = [
        'name'       => 'string',
        'phone'      => 'string',
        'email'      => 'string',
        'ip'         => 'string',
        'user_agent' => 'string',
        'game_id'    => 'integer',
        'turn'       => 'integer',
    ];
}
