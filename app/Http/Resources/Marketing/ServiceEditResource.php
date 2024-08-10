<?php

namespace App\Http\Resources\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceEditResource extends JsonResource
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
            'is_promoted' => $this->is_promoted,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'slug' => $this->slug,
            'view_count' => $this->view_count,
            'created_at' => $this->created_at,
            'tags' => $this->tags,
            'categories' => $this->categories,
            'image' => $this->image,
            'video_url' => $this->video_url,
            'brand_id' => $this->brand_id,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'tracker' => $this->tracker,
            'tracker_start_day' => $this->tracker_start_day,
            'tracker_end_day' => $this->tracker_end_day,
            'tracker_range' => $this->tracker_range ? json_decode($this->tracker_range):null,
            'is_payment' => $this->is_payment,
            'price' => $this->price,
            'special_price' => $this->special_price,
            'special_price_start_date' => $this->special_price_start_date,
            'special_price_end_date' => $this->special_price_end_date,
            'payment_method' => $this->payment_method ? json_decode($this->payment_method) : [],
            'is_booking' => $this->is_booking,
            'booking_type' => $this->booking_type,
            'booking_btn_text' => $this->booking_btn_text,
            'booking_start_date' => $this->booking_start_date,
            'booking_end_date' => $this->booking_end_date,
            'is_reg' => $this->is_reg,
            'is_customer_name' => $this->is_customer_name,
            'is_customer_phone' => $this->is_customer_phone,
            'is_customer_email' => $this->is_customer_email,
            'is_child_name' => $this->is_child_name,
            'is_child_age' => $this->is_child_age,
            'is_child_gender' => $this->is_child_gender,
            'external_url' => $this->external_url,
            'later_btn_text' => $this->later_btn_text,
            'now_btn_text' => $this->now_btn_text,
            'reg_btn_text' => $this->reg_btn_text,
            'cta_btn_text' => $this->cta_btn_text,
            'special_price_message' => $this->special_price_message,
            'external_btn_text' => $this->external_btn_text,
            'external_url_btn_text' => $this->external_url_btn_text,
            'is_additional_qus' => $this->is_additional_qus,
            'questions' => $this->questions ? json_decode($this->questions) : [],
            'provider_email' => $this->provider_email,
            'provider_phone' => $this->provider_phone,
        ];
    }
}
