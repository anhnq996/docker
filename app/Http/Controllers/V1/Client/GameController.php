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

    public function createWinner(CreateWinnerRequest $request): JsonResponse
    {
        $data = $request->only(['game_id', 'game_reward_id', 'quantity', 'email', 'phone', 'name']);

        Winner::query()->create($data);

        return $this->response(ResponseCodes::S1000);
    }

    public function dial(DialRequest $request): JsonResponse
    {
        // $games = GameReward::query()->select(['id', 'quantity', 'percent'])->where('game_id', $request->get('game_id'))->get();

        // foreach ($games as $reward) {
        //     Redis::set('reward_' . $reward->id, $reward->quantity . '/' . $reward->percent);
        // }

        // $rewardID = GameReward::query()->where('game_id', $request->get('game_id'))->pluck('id');

        // $rewardArr = [];
        // foreach ($rewardID as $id) {
        //     if (Redis::get('reward_' . $id)) {
        //         $reward = explode('/', Redis::get('reward_' . $id));
        //     }
        // }
        // return $this->response(ResponseCodes::S1000);
    }
}
