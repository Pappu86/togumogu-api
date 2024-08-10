<?php

namespace App\Http\Resources\Corporate;

use App\Http\Resources\Marketing\CouponResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PartnershipApiResource extends JsonResource
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
            'coupon_id' => $this->coupon_id,
            'coupon' => new CouponResource($this->coupon),
            'discount' => $this->discount,
            'is_free_shipping' => $this->is_free_shipping,
            'free_shipping_spend' => $this->free_shipping_spend,
            'pse' => $this->pse,
            'start_date' => $this->start_date,
            'expiration_date' => $this->expiration_date,
        ];
    }
}