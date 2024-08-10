<?php

namespace App\Http\Resources\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'discount' => $this->discount,
            'total_amount' => $this->total_amount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'uses_per_coupon' => $this->uses_per_coupon,
            'uses_per_customer' => $this->uses_per_customer,
            'platforms' => $this->platforms,
            'area' => $this->area,
            'status' => $this->status,
            'category' => $this->category,
        ];
    }
}
