<?php

namespace App\Http\Resources\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class OfferRedeemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'name' => $this->name,
            'customer' => [
                'id' => $this->customer?->id?:'',
                'name' => $this->customer?->name?:''
            ],
            'brand' => [
                'id' => $this->brand?->id?:'',
                'name' => $this->brand?->name?:''
            ],
            'offer' => [
                'id' => $this->offer?->id?:'',
                'title' => $this->offer?->title?:''
            ],
            'start_date' => $this->start_date,
            'expired_date' => $this->expired_date,
            'validity_day' => $this->validity_day,
            'spent_reward_point' => $this->spent_reward_point,
            'offer_redeem_no' => $this->offer_redeem_no,
            'coupon' => $this->coupon,
            'status' => $this->status
        ];
    }
}
