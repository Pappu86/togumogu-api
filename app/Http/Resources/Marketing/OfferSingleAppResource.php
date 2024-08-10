<?php

namespace App\Http\Resources\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Brand\CategoryResource;
use App\Http\Resources\Common\TagResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Marketing\OfferRedeem;
use Carbon\Carbon;

class OfferSingleAppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $customer = Auth::user();
        $isLock = true;

        //Checking customer has already given the offer
        $lastOfferRedeem = OfferRedeem::latest()->where('customer_id', $customer->id)->where('offer_id', $this->id)->first();
        $isOfferContinue = $lastOfferRedeem?Carbon::now()->isBefore($lastOfferRedeem->expired_date):false;
        
        if($isOfferContinue) {
           $isLock = false;
        };

        return [
            'id' => $this->id,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'title' => $this->title,
            'is_togumogu_offer' => $this->is_togumogu_offer,
            'is_free' => $this->is_free,
            'is_promoted' => $this->is_promoted,
            'is_featured' => $this->is_featured,
            'coupon' => $this->coupon,
            'status' => $this->status,
            'slug' => $this->slug,
            'image' => $this->image,
            'card_image' => $this->card_image,
            'video_url' => $this->video_url,
            'website_url' => $this->website_url,
            'website_btn' => $this->website_btn,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'reward_amount' => $this->reward_amount,
            'store_location_url' => $this->store_location_url,
            'store_location_btn' => $this->store_location_btn,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,       
            'validity_day' => $this->validity_day,
            'is_lock' => $isLock,
            'customer' => !$isLock?[
                'id' => $customer?->id,
                'name' => $customer?->name,
                'avatar' => $customer?->avatar,
                'mobile' => $customer?->mobile,
                'email' => $customer?->email,
            ]:null,
            'redeem' => !$isLock?[
                'id' => $lastOfferRedeem?->id,
                'offer_redeem_no' => $lastOfferRedeem?->offer_redeem_no,
                'passed_day' => Carbon::parse($lastOfferRedeem?->start_date)->diffInDays(Carbon::now()),
                'remain_day' => Carbon::now()->diffInDays($lastOfferRedeem?->expired_date, false),
            ]:null,
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
