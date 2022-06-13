<?php

namespace App\Http\Resources\Game;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class GameDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'code'             => $this->code,
            'name'             => $this->name,
            'description'      => $this->description,
            'banner'           => $this->banner,
            'background'       => $this->background,
            'email_template'   => $this->email_template,
            'rule'             => $this->rule,
            'user_id'          => $this->user_id,
            'status'           => $this->status,
            'reward_use_image' => $this->reward_use_image,
            'start_at'         => $this->start_at,
            'end_at'           => $this->end_at,
            'redirect_url'     => $this->redirect_url,
            'created_at'       => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at'       => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
