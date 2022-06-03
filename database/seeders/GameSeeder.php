<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    private $games = [
        [
            'id' => 1,
            'code' => 'GAME1',
            'name' => 'Game quay so trung thuong 1/6',
            'description' => 'Game quay so trung thuong 1/6 cho cac em nho',
            'user_id' => 1,
            'status' => 1,
            'reward_use_image' => true,
            'start_at' => '2022-06-01',
            'end_at' => '2022-08-01',
            'redirect_url' => 'https://hostify.vn'
        ],
        [
            'id' => 2,
            'code' => 'GAME2',
            'name' => 'Game quay so trung thuong 2/9',
            'description' => 'Game quay so trung thuong 2/9 cho cac em nho',
            'user_id' => 1,
            'status' => 1,
            'reward_use_image' => true,
            'start_at' => '2022-06-01',
            'end_at' => '2022-08-01',
            'redirect_url' => 'https://hostify.vn'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->games as $game) {
            Game::query()->firstOrCreate(['id' => $game['id']], $game);
        }
    }
}
