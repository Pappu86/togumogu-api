<?php

namespace App\Http\Resources\Reward;

use App\Traits\CommonHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $commentHelpers = new CommonHelpers;

        return [
            'id' => $this->id,
            'status' => $this->status,
            'balance' => $this->balance?:0,
            'redeem' => $this->redeem?:0,
            'total' => $this->total?:0,
            'pending' => $commentHelpers->getPendingPoints($this->customer_id),
        ];
    }
}