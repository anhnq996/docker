<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int id
 * @property string code
 * @property string name
 * @property string description
 * @property string banner
 * @property string background
 * @property string email_template
 * @property string rule
 * @property int user_id
 * @property int status
 * @property boolean reward_use_image
 * @property string redirect_url
 * @property Carbon start_at
 * @property Carbon end_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'banner',
        'background',
        'email_template',
        'rule',
        'user_id',
        'status',
        'reward_use_image',
        'start_at',
        'end_at',
        'redirect_url',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'reward_use_image' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class, 'game_id');
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(GameReward::class, 'game_id');
    }
}
