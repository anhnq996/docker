<?php

namespace App\Http\Controllers\V1\Admin;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\CreateGameRequest;
use App\Http\Requests\Game\DeleteGameRequest;
use App\Http\Requests\Game\DetailGameRequest;
use App\Http\Requests\Game\GetListGameRequest;
use App\Http\Requests\Game\UpdateGameRequest;
use App\Http\Requests\Game\UploadFileRequest;
use App\Http\Resources\Game\GameCollection;
use App\Http\Resources\Game\GameDetailResource;
use App\Models\Game;
use App\Models\GameReward;
use App\Models\Winner;
use App\Traits\CommonTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class GameController extends Controller
{
    use ResponseTrait, CommonTrait;

    private Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GetListGameRequest $request)
    {
        $games = $this->game->listGame($request);

        return $this->response(ResponseCodes::S1000, GameCollection::make($games));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateGameRequest $request): JsonResponse
    {
        $data = $request->only([
            'name', 'description', 'email_template', 'rule', 'redirect_url', 'status', 'start_at', 'end_at', 'redirect_url',
            'reward_use_image', 'banner', 'background', 'font_size', 'color', 'free_turns', 'code_prefix', 'title_game', 'reward_form',
            'show_suffix', 'image_share', 'content_share', 'hashtag', 'create_winner', 'is_publish', 'frame'
        ]);

        $data['code']    = strtoupper(Str::random(10));
        $data['user_id'] = auth()->user()?->id;

        if ($request->file('image_share')) {
            $image                      = $this->uploadImage($request->file('image_share'), 'banner_share');
            $data['banner_image_share'] = $image['path'];
        }

        $game = Game::query()->create($data);

        $rewardInsert = [];
        foreach ($request->reward as $reward) {
            if ($request->file('image')) {
                $image           = $this->uploadImage($request->file('image'), 'image');
                $reward['image'] = $image['path'];
            }
            $rewardInsert[] = [
                'name'       => $reward['quantity'],
                'image'      => $reward['image'],
                'quantity'   => $reward['quantity'],
                'percent'    => $reward['percent'],
                'add_turn'   => $reward['add_turn'] ?? 0,
                'game_id'    => $game->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        GameReward::query()->insert($rewardInsert);

        if ($request->get('create_winner')) {
            $faker = Factory::create();
            $rewardID = GameReward::query()->where('game_id', $game->id)?->pluck('id');

            $data = [];
            if ($rewardID) {
                foreach ($rewardID as $reward) {
                    $data[] = [
                        'game_id'         => $game->id,
                        'game_reward_id'  => $reward,
                        'name'            => $faker->name(),
                        'email'           => $faker->unique()->safeEmail(),
                        'phone'           => $faker->phoneNumber,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }
            Winner::query()->insert($data);
        }

        // set redis reward
        $this->setRedisReward($game->id);

        return $this->response(ResponseCodes::S1000);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(DetailGameRequest $request)
    {
        $detail = $this->game->detail($request);

        return $this->response(ResponseCodes::S1000, GameDetailResource::collection($detail));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGameRequest $request): JsonResponse
    {
        $data            = $request->only([
            'name', 'description', 'email_template', 'rule', 'redirect_url', 'status', 'start_at', 'end_at', 'redirect_url',
            'reward_use_image', 'banner', 'background', 'font_size', 'color', 'free_turns', 'code_prefix', 'title_game', 'reward_form',
            'show_suffix', 'image_share', 'content_share', 'hashtag', 'create_winner', 'is_publish', 'frame'
        ]);
        $data['code']    = strtoupper(Str::random(10));
        $data['user_id'] = auth()->user()?->id;

        if ($request->file('image_share')) {
            $image                      = $this->uploadImage($request->file('image_share'), 'banner_share');
            $data['banner_image_share'] = $image['path'];
        }

        Game::query()->find($request->get('id'))->update($data);

        $rewards = $request->get('reward');
        if ($rewards) {
            $gameRewards = GameReward::query()->where('game_id', $request->get('id'))->pluck('id');

            foreach ($gameRewards as $reward) {
                if (Redis::get('reward_' . $reward)) {
                    Redis::del('reward_' . $reward);
                }
            }

            // Delete the winner of the old award
            Winner::query()->where('game_id', $request->get('id'))->whereIn('id',  $gameRewards)->delete();

            GameReward::query()->where('game_id', $request->get('id'))->delete();

            $rewardInsert = [];
            $rewards      = $request->reward;
            foreach ($rewards as $reward) {
                if ($request->file('image')) {
                    $image           = $this->uploadImage($request->file('image'), 'image');
                    $reward['image'] = $image['path'];
                }
                $rewardInsert[] = [
                    'name'       => $reward['quantity'],
                    'image'      => $reward['image'],
                    'quantity'   => $reward['quantity'],
                    'percent'    => $reward['percent'],
                    'add_turn'   => $reward['add_turn'] ?? 0,
                    'game_id'    => $request->get('id'),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            GameReward::query()->insert($rewardInsert);

            // create sample data
            if ($request->get('create_winner')) {
                $faker = Factory::create();
                $rewardID = GameReward::query()->where('game_id', $request->get('id'))?->pluck('id');

                $data = [];
                if ($rewardID) {
                    foreach ($rewardID as $reward) {
                        $data[] = [
                            'game_id'         => $request->get('id'),
                            'game_reward_id'  => $reward,
                            'name'            => $faker->name(),
                            'email'           => $faker->unique()->safeEmail(),
                            'phone'           => $faker->phoneNumber,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }
                Winner::query()->insert($data);
            }

            // set redis reward
            $this->setRedisReward($request->get('id'));
        }

        return $this->response(ResponseCodes::S1000);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteGameRequest $request)
    {
        $game = Game::query()->where('id', $request->get('id'))
            ->where('start_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('end_at', '>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->first();

        if ($game) {
            return $this->response(ResponseCodes::E2008);
        }

        Game::query()->find($request->get('id'))->delete();

        $rewardID = GameReward::query()->where('game_id', $request->get('id'))->pluck('id');
        foreach ($rewardID as $id) {
            Redis::del('reward_' . $id);
        }

        GameReward::query()->where('game_id', $request->get('id'))->delete();

        return $this->response(ResponseCodes::S1000);
    }

    public function setRedisReward($gameID)
    {
        // set redis for game
        $rewardIdArr = GameReward::query()->where('game_id', $gameID)->get();
        $rewardIds   = null;
        foreach ($rewardIdArr as $reward) {
            $rewardIds = $rewardIds ? $rewardIds . ',' . $reward->id : $reward->id;
            Redis::set('reward_' . $reward->id, $reward->quantity . '/' . $reward->percent);
            if ($reward->add_turn) {
                Redis::set($gameID . '_reward_' . $reward->id, $reward->add_turn);
            }
        }
        Redis::set('game_' . $gameID, $rewardIds);
    }
}
