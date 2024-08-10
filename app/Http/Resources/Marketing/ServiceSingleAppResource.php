<?php

namespace App\Http\Resources\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Brand\CategoryResource;
use App\Http\Resources\Common\TagResource;

class ServiceSingleAppResource extends JsonResource
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
            'is_promoted' => $this->is_promoted?:false,
            'is_featured' => $this->is_featured?:false,
            'status' => $this->status,
            'slug' => $this->slug,
            'view_count' => $this->view_count,
            'registration_count' => $this->registration_count,
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
            'is_payment' => $this->is_payment?:false,
            'price' => $this->price,
            'special_price' => $this->special_price,
            'special_price_start_date' => $this->special_price_start_date,
            'special_price_end_date' => $this->special_price_end_date,
            'payment_method' => $this->payment_method ? json_decode($this->payment_method) : [],
            'is_booking' => $this->is_booking?:false,
            'booking_type' => $this->booking_type,
            'is_reg' => $this->is_reg?:false,
            'is_customer_name' => $this->is_customer_name?:false,
            'is_customer_phone' => $this->is_customer_phone?:false,
            'is_customer_email' => $this->is_customer_email?:false,
            'is_child_name' => $this->is_child_name?:false,
            'is_child_age' => $this->is_child_age?:false,
            'is_child_gender' => $this->is_child_gender?:false,
            'external_url' => $this->external_url,
            'external_url_btn_text' => $this->external_url_btn_text,
            'later_btn_text' => $this->later_btn_text,
            'now_btn_text' => $this->now_btn_text,
            'reg_btn_text' => $this->reg_btn_text,
            'booking_btn_text' => $this->booking_btn_text,
            'cta_btn_text' => $this->cta_btn_text,
            'special_price_message' => $this->special_price_message,
            'external_btn_text' => $this->external_btn_text,
            'external_payment_url' => $this->external_payment_url,
            'is_additional_qus' => $this->is_additional_qus?:false,
            'questions' => $this->questions ? json_decode($this->questions) : [],
            'brand' => [
                'id' => $this->brand?->id,
                'name' => $this->brand?->name,
                'slug' => $this->brand?->slug,
                'logo' => $this->brand?->logo,
                'banner' => $this->brand?->banner,
            ],
            'tags' => TagResource::collection($this->tags),
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
