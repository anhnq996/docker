<?php

namespace App\Http\Controllers\V1\Admin;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\CreateGameRequest;
use App\Http\Requests\Game\DeleteGameRequest;
use App\Http\Requests\Game\DetailGameRequest;
use App\Http\Requests\Game\GetListGameRequest;
use App\Http\Requests\Game\UpdateGameRequest;
use App\Http\Resources\Game\GameCollection;
use App\Http\Resources\Game\GameDetailResource;
use App\Models\Game;
use App\Models\GameReward;
use App\Traits\CommonTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
    public function create(CreateGameRequest $request)
    {
        $data = $request->only(['name', 'description', 'email_template', 'rule', 'redirect_url', 'status', 'start_at', 'end_at', 'redirect_url', 'reward_use_image']);

        if ($request->file('banner')) {
            $image          = $this->uploadImage($request->file('banner'), 'banner');
            $data['banner'] = $image['path'];
        }

        if ($request->file('background')) {
            $image              = $this->uploadImage($request->file('background'), 'background');
            $data['background'] = $image['path'];
        }

        $data['code'] = strtoupper(Str::random(10));
        $data['user_id'] = auth()->user()?->id;

        $game = Game::query()->create($data);

        $rewardInsert = [];
        foreach ($request->reward as $reward) {
            if ($request->file('image')) {
                $image           = $this->uploadImage($request->file('image'), 'image');
                $reward['image'] = $image['path'];
            }
            $rewardInsert = [
                'name'       => $reward['quantity'],
                'image'      => $reward['image'],
                'quantity'   => $reward['quantity'],
                'percent'    => $reward['percent'],
                'game_id'    => $game->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        GameReward::query()->insert($rewardInsert);

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
        $data = $request->only(['name', 'description', 'email_template', 'rule', 'redirect_url', 'status', 'start_at', 'end_at', 'redirect_url', 'reward_use_image']);

        if ($request->file('banner')) {
            $image          = $this->uploadImage($request->file('banner'), 'banner');
            $data['banner'] = $image['path'];
        }

        if ($request->file('background')) {
            $image              = $this->uploadImage($request->file('background'), 'background');
            $data['background'] = $image['path'];
        }

        $data['code'] = strtoupper(Str::random(10));
        $data['user_id'] = auth()->user()?->id;

        Game::query()->find($request->get('id'))->update($data);

        $rewards = $request->get('reward');
        if ($rewards) {
            GameReward::query()->where('game_id', $request->get('id'))->delete();

            $rewardInsert = [];
            foreach ($request->reward as $reward) {
                if ($request->file('image')) {
                    $image           = $this->uploadImage($request->file('image'), 'image');
                    $reward['image'] = $image['path'];
                }
                $rewardInsert = [
                    'name'       => $reward['quantity'],
                    'image'      => $reward['image'],
                    'quantity'   => $reward['quantity'],
                    'percent'    => $reward['percent'],
                    'game_id'    => $request->get('id'),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            GameReward::query()->insert($rewardInsert);
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

        GameReward::query()->where('game_id', $request->get('id'))->delete();

        return $this->response(ResponseCodes::S1000);
    }
}
