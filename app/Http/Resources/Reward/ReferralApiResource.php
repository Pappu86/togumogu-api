<?php

namespace App\Http\Resources\Reward;

use Illuminate\Http\Resources\Json\JsonResource;

class ReferralApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'status' => $this->status,
            'uid' => $this->uid,
            'type' => $this->type,
            'dynamic_url' => $this->dynamic_url,
        ];
    }
}