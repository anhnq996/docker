<?php

namespace App\Models;

use App\Enums\RewardType;
use App\Models\Attributes\ImageAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameReward extends Model
{
    use HasFactory, ImageAttribute;

    protected $fillable = [
        'name',
        'image',
        'quantity',
        'percent',
        'game_id',
        'type',
    ];

    protected $casts = [
        'name'       => 'string',
        'image'      => 'string',
        'quantity'   => 'integer',
        'percent'    => 'integer',
        'game_id'    => 'integer',
        'type'       => RewardType::class,
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
