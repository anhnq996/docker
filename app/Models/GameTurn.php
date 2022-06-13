<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameTurn extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'user_id',
        'game_id',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'user_id'    => 'string',
        'game_id'    => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
