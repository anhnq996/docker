<?php

namespace App\Jobs;

use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateWinnnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rewardID;
    protected $gameID;
    protected $email;
    protected $phone;
    protected $name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($rewardID, $gameID, $email, $phone, $name)
    {
        $this->rewardID = $rewardID;
        $this->gameID   = $gameID;
        $this->email    = $email;
        $this->phone    = $phone;
        $this->name     = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Winner::query()->create([
            'reward_id' => $this->rewardID,
            'game_id'   => $this->gameID,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'name'      => $this->name,
        ]);
    }
}
