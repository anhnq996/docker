<?php

namespace App\Http\Controllers\V1\Client;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\DialRequest;
use App\Http\Requests\Game\CreateWinnerRequest;
use App\Models\Game;
use App\Models\GameReward;
use App\Models\Winner;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Jobs\CreateWinnnerJob;

class GameController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $games = Game::query()
            ->select(['id', 'code', 'name', 'description', 'start_at', 'end_at', 'status'])
            ->where('user_id', $user?->id)
            ->get();
        return $this->response(ResponseCodes::S1000, $games);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $user = $request->user();
        $game = Game::query()
            ->where('id', $id)
            ->where('user_id', $user?->id)
            ->with('rewards')
            ->first();

        if (!$game) {
            return $this->response(ResponseCodes::E1008);
        }
        return $this->response(ResponseCodes::S1000, $game);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function dial(DialRequest $request): JsonResponse
    {
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
            }
        }

        return $this->response(ResponseCodes::S1000, [
            'id'      => $rewardID,
            'game_id' => $request->get('game_id'),
        ]);
    }

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
