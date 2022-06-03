<?php

namespace App\Http\Controllers\V1\Admin;

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
    public function index()
    {
        $games = Game::query()
            ->select(['id', 'code', 'name', 'description', 'start_at', 'end_at', 'status'])
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
    public function show($id)
    {
        $game = Game::query()
            ->where('id', $id)
            ->with('rewards')
            ->first();
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
