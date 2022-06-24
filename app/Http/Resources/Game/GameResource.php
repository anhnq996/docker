<?php

namespace App\Http\Resources\Game;

use Carbon\Carbon;
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
            'id'                 => $this->id,
            'code'               => $this->code,
            'name'               => $this->name,
            'description'        => $this->description,
            'banner'             => $this->banner,
            'frame'              => $this->frame,
            'background'         => $this->background,
            'email_template'     => $this->email_template,
            'rule'               => $this->rule,
            'user_id'            => $this->user_id,
            'status'             => $this->status,
            'reward_use_image'   => $this->reward_use_image,
            'start_at'           => $this->start_at,
            'end_at'             => $this->end_at,
            'redirect_url'       => $this->redirect_url,
            'rewards'            => $this->rewards,
            'font_size'          => $this->font_size,
            'color'              => $this->color,
            'free_turns'         => $this->free_turns,
            'code_prefix'        => $this->code_prefix,
            'title_game'         => $this->title_game,
            'reward_form'        => $this->reward_form,
            'show_suffix'        => $this->show_suffix,
            'banner_image_share' => $this->banner_image_share,
            'content_share'      => $this->content_share,
            'hashtag'            => $this->hashtag,
            'create_winner'      => $this->create_winner,
            'is_publish'         => $this->is_publish,
            'created_at'         => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at'         => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
