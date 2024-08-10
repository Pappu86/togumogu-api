<?php

namespace App\Http\Resources\Corporate;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PartnershipEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $is_free_shipping = $this->is_free_shipping;
        return [
            'id' => $this->id,
            'status' => $this->status,
            'details' => $this->details,
            'company_id' => $this->company_id,
            'coupon_id' => $this->coupon_id,
            'group_id' => $this->group_id,
            'discount' => $this->discount,
            'special_note' => $this->special_note,
            'is_free_shipping' => $is_free_shipping?"$is_free_shipping":'0',
            'free_shipping_spend' => $this->free_shipping_spend,
            'pse' => $this->pse,
            'hotline_number' => $this->hotline_number,
            'offer_image' => $this->offer_image,
            'offer_code' => $this->offer_code,
            'offer_text' => $this->offer_text,
            'offer_instruction' => $this->offer_instruction,
            'togumogu_customer_offer' => $this->togumogu_customer_offer,
            'start_date' => $this->start_date,
            'expiration_date' => $this->expiration_date,
            'created_at' => $this->created_at,
        ];
    }
}