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
use App\Jobs\UpdatePlayerJob;

class GameController extends Controller
{
    use ResponseTrait;

    /**
     * Dial
     *
     * @param DialRequest $request
     * @return JsonResponse
     */
    public function dial(DialRequest $request): JsonResponse
    {
        // check turn
        if (Redis::exists($request->get('game_id') . '_' . $request->get('phone'))) {
            if (!Redis::get($request->get('game_id') . '_' . $request->get('phone'))) {
                return $this->reponse(ResponseCodes::E2016);
            }
        }

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

                CreateWinnnerJob::dispatch($rewardID, $request->get('game_id'), $request->get('email'), $request->get('phone'), $request->get('name'));

                if (Redis::exists($request->get('game_id') . '_' . $request->get('phone'))) {
                    $turn = $quantity + Redis::get($request->get('game_id') . '_' . $request->get('phone'));

                    UpdatePlayerJob::dispatch($turn, $request->get('phone'), $request->get('game_id'));
                }

            }
        }

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
        if (Redis::exists($request->get('game_id') . '_' . $request->get('phone'))) {
            $data               = $request->only(['name', 'phone', 'email', 'game_id']);
            $data['user_agent'] = $request->header('user_agent');
            $data['ip']         = $request->ip();
            $data['turn']       = Game::query()->find($data['game_id'])?->free_turns;

            Redis::set($data['game_id'] . '_' . $data['phone'], $data['turn']);

            // create player
            CreatePlayerJob::dispatch($data);
        }

        return $this->response(ResponseCodes::S1000, [
            'turn' => Redis::get($request->get('game_id') . '_' . $request->get('phone'))
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
        //clear key
        Redis::del(Redis::keys('key:*'));

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

        return $rewardID;
    }
}
