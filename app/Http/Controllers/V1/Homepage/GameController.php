<?php

namespace App\Http\Controllers\V1\Homepage;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\DetailGameRequest;
use App\Http\Resources\Homepage\GameDetailResource;
use App\Models\Game;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class GameController extends Controller
{
    use ResponseTrait;

    protected Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
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
