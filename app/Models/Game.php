<?php

namespace App\Models;

use App\Enums\GameStatus;
use App\Models\Attributes\BackGroundAttribute;
use App\Models\Attributes\BannerAttribute;
use App\Models\Attributes\FrameAttribute;
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
    use HasFactory, BannerAttribute, BackGroundAttribute, FrameAttribute;

    protected $fillable = [
        'code',
        'name',
        'description',
        'banner',
        'background',
        'frame',
        'email_template',
        'rule',
        'user_id',
        'status',
        'reward_use_image',
        'start_at',
        'end_at',
        'redirect_url',
        'font_size',
        'color',
        'free_turns',
        'code_prefix',
        'title_game',
        'reward_form',
        'show_suffix',
        'banner_image_share',
        'content_share',
        'hashtag',
        'create_winner',
        'is_publish',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'reward_use_image'   => 'boolean',
        'code'               => 'string',
        'name'               => 'string',
        'description'        => 'string',
        'banner'             => 'string',
        'background'         => 'string',
        'frame'              => 'string',
        'email_template'     => 'string',
        'rule'               => 'string',
        'user_id'            => 'integer',
        'status'             => GameStatus::class,
        'reward_use_image'   => 'string',
        'start_at'           => 'datetime:Y-m-d H:i:s',
        'end_at'             => 'datetime:Y-m-d H:i:s',
        'redirect_url'       => 'string',
        'font_size'          => 'string',
        'color'              => 'string',
        'free_turns'         => 'integer',
        'code_prefix'        => 'string',
        'title_game'         => 'string',
        'reward_form'        => 'boolean',
        'show_suffix'        => 'boolean',
        'banner_image_share' => 'string',
        'content_share'      => 'string',
        'hashtag'            => 'array',
        'create_winner'      => 'boolean',
        'is_publish'         => 'boolean',
        'created_at'         => 'datetime:Y-m-d H:i:s',
        'updated_at'         => 'datetime:Y-m-d H:i:s',
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

    public function detail($request)
    {
        $id    = $request->get('id') ?? null;
        $limit = $request->get('limit') ?? 20;

        return $this->select([
            'games.code',
            'games.name',
            'games.description',
            'games.banner',
            'games.background',
            'games.frame',
            'games.email_template',
            'games.rule',
            'games.user_id',
            'games.status',
            'games.reward_use_image',
            'games.start_at',
            'games.end_at',
            'games.redirect_url',
            'games.font_size',
            'games.color',
            'games.free_turns',
            'games.code_prefix',
            'games.title_game',
            'games.reward_form',
            'games.show_suffix',
            'games.banner_image_share',
            'games.content_share',
            'games.hashtag',
            'games.create_winner',
            'games.is_publish',
            'games.created_at',
            'games.updated_at',
        ])
            ->with('rewards')
            ->where('id', $id)
            ->paginate($limit);
    }

    public function listGame($request)
    {
        $keyword = $request->get('keyword') ?? null;
        $limit   = $request->get('limit') ?? null;
        $status  = $request->get('status') ?? null;

        return $this->select([
            'games.id',
            'games.code',
            'games.name',
            'games.description',
            'games.banner',
            'games.background',
            'games.frame',
            'games.email_template',
            'games.rule',
            'games.user_id',
            'games.status',
            'games.reward_use_image',
            'games.start_at',
            'games.end_at',
            'games.redirect_url',
            'games.font_size',
            'games.color',
            'games.free_turns',
            'games.code_prefix',
            'games.title_game',
            'games.reward_form',
            'games.show_suffix',
            'games.banner_image_share',
            'games.content_share',
            'games.hashtag',
            'games.create_winner',
            'games.is_publish',
            'games.created_at',
            'games.updated_at',
        ])
        ->with('rewards')
        ->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('code', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('name', 'LIKE', '%' . $keyword . '%');
            });
        })
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->paginate($limit);

    }
}
