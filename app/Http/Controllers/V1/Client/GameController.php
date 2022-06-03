<?php

namespace App\Http\Controllers\V1\Client;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

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
}
