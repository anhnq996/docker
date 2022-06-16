<?php

namespace App\Http\Resources\Plan;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPlanResource extends JsonResource
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
            'name'          => $this->name,
            'price'         => $this->price,
            'properties'    => $this->properties,
            'duration_time' => $this->duration_time,
            'created_at'    => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
