<?php

namespace App\Http\Resources\Reward;

use Illuminate\Http\Resources\Json\JsonResource;

class RewardPointResource extends JsonResource
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
            'category' => $this->category,
            'type' => $this->type,
            'award_points' => $this->award_points,
            'min_amount' => $this->min_amount,
            'max_award_points' => $this->max_award_points,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'platforms' => $this->platforms,
        ];
    }
}