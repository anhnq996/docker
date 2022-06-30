<?php

namespace App\Http\Controllers\V1\Client;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreatePlayerRequest;
use App\Http\Requests\Client\DialRequest;
use App\Models\Game;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use App\Jobs\CreateWinnnerJob;
use App\Jobs\CreatePlayerJob;
use App\Http\Requests\Client\UpdatePlayerRequest;
use App\Http\Requests\Game\DetailGameRequest;
use App\Jobs\UpdatePlayerJob;
use Illuminate\Support\Facades\Crypt;

class GameController extends Controller
{
    use ResponseTrait;

    protected Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Dial
     *
     * @param DialRequest $request
     * @return JsonResponse
     */
    public function dial(DialRequest $request): JsonResponse
    {
        $phone = Crypt::decryptString($request->get('token'));
        // check turn
        if (Redis::exists($request->get('game_id') . '_' . $phone)) {
            if (!Redis::get($request->get('game_id') . '_' . $phone)) {
                return $this->response(ResponseCodes::E2016);
            }
        }

        $remainTurn = Redis::get($request->get('game_id') . '_' . $phone) - 1;

        $rewardID = $this->createRedis($request->get('game_id'));

        if (Redis::exists("reward_$rewardID")) {
            $rewards  = explode('/', Redis::get("reward_$rewardID"));
            if ($rewards[0] == 0) {
                Redis::del("reward_$rewardID");
                $this->dial($request->get('game_id'));
            }

            if ($rewards[0] > 0) {
                $quantity = $rewards[0] - 1;
                Redis::set("reward_$rewardID", $quantity . '/' . $rewards[1]);

                if (Redis::exists($request->get('game_id') . '_reward_' . $rewardID)) {
                    $remainTurn = $remainTurn + Redis::get($request->get('game_id') . '_reward_' . $rewardID);

                    UpdatePlayerJob::dispatch($remainTurn, $phone, $request->get('game_id'));
                } else {
                    if (Redis::exists('game_' . $request->get('game_id') . '_' . $phone)) {
                        $winner = explode('/', Redis::get('game_' . $request->get('game_id') . '_' . $phone));

                        CreateWinnnerJob::dispatch($rewardID, $request->get('game_id'), $winner[0], $winner[1], $winner[2]);
                    }
                }
            }
        }

        Redis::set($request->get('game_id') . '_' . $phone, $remainTurn);

        return $this->response(ResponseCodes::S1000, [
            'id'      => $rewardID,
            'game_id' => $request->get('game_id'),
        ]);
    }

    /**
     * Create playder
     *
     * @param CreatePlayerRequest $request
     * @return JsonResponse
     */
    public function createPlayer(CreatePlayerRequest $request): JsonResponse
    {
        if (!Redis::exists($request->get('game_id') . '_' . $request->get('phone'))) {
            $data               = $request->only(['name', 'phone', 'email', 'game_id']);
            $data['user_agent'] = $request->header('user_agent');
            $data['ip']         = $request->ip();
            $data['turn']       = Game::query()->find($data['game_id'])?->free_turns;

            Redis::set($data['game_id'] . '_' . $data['phone'], $data['turn']);
            Redis::set('game_' . $data['game_id'] . '_' . $data['phone'], $data['email']. '/' . $data['phone'] . '/' . $data['name']);
            // create player
            CreatePlayerJob::dispatch($data);
        }

        return $this->response(ResponseCodes::S1000, [
            'turn'  => Redis::get($request->get('game_id') . '_' . $request->get('phone')),
            'token' => Crypt::encryptString($request->get('phone')),
        ]);
    }

    /**
     * Create redis to dial
     *
     * @param $gameID
     * @return $rewardID
     */
    private function createRedis($gameID)
    {
        $rewardID  = explode(',', (Redis::get('game_' . $gameID)));
        $arr       = [];
        $percent   = 0;
        foreach ($rewardID as $id) {
            if (Redis::exists('reward_' . $id)) {
                $rewards   = explode('/', Redis::get('reward_' . $id));
                for ($i = $percent + 1; $i <= (($rewards[1] - 1) + $percent); $i++) {
                    $arr[$i] = $id;
                }
                $percent = $percent + $rewards[1];
            }
        }

        Redis::pipeline(function ($pipe) use ($arr) {
            foreach ($arr as $key => $value) {
                $pipe->set("key:$key", $value);
            }
        });

        $rewardID = Redis::get("key:" . rand(1, $percent));

        // clear key
        foreach ($arr as $key => $value) {
            Redis::del("key:$key");
        }

        return $rewardID;
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

        return $this->response(ResponseCodes::S1000, $detail);
    }
}
