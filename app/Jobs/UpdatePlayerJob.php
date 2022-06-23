<?php

namespace App\Jobs;

use App\Models\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePlayerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $turn;
    protected $phone;
    protected $gameID;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($turn, $phone, $gameID)
    {
        $this->turn  = $turn;
        $this->phone = $phone;
        $this->gameID = $gameID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Player::query()->where('phone', $this->phone)
            ->where('game_id', $this->gameID)
            ->update([
                'turn', $this->turn
            ]);
    }
}
