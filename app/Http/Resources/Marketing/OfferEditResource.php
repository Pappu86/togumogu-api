<?php

namespace App\Http\Resources\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'is_togumogu_offer' => $this->is_togumogu_offer,
            'is_free' => $this->is_free,
            'is_promoted' => $this->is_promoted,
            'is_featured' => $this->is_featured,
            'coupon' => $this->coupon,
            'status' => $this->status,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'tags' => $this->tags,
            'categories' => $this->categories,
            'image' => $this->image,
            'card_image' => $this->card_image,
            'video_url' => $this->video_url,
            'website_url' => $this->website_url,
            'website_btn' => $this->website_btn,
            'brand_id' => $this->brand_id,
            'reward_amount' => $this->reward_amount,
            'store_location_url' => $this->store_location_url,
            'store_location_btn' => $this->store_location_btn,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'validity_day' => $this->validity_day,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
        ];
    }
}
