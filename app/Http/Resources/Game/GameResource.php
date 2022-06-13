<?php

namespace App\Http\Resources\Game;

use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            'id'               => $this->id,
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
            'reward'           => $this->reward,
            'created_at'       => $this->id,
            'updated_at'       => $this->id
        ];
    }
}
