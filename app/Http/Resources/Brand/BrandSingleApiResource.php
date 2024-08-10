<?php

namespace App\Http\Resources\Brand;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Brand\CategoryResource;

class BrandSingleApiResource extends JsonResource
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
            'created_at' => $this->created_at->toDayDateTimeString(),
            'is_togumogu_partner' => $this->is_togumogu_partner,
            'website_link' => $this->website_link,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'logo' => $this->logo,
            'slug' => $this->slug,
            'tags' => $this->tags,
            'categories' => $this->categories,
            'banner' => $this->banner,
            'video_url' => $this->video_url,
            'company_id' => $this->company_id,
            'area_id' => $this->area_id,
            'district_id' => $this->district_id,
            'division_id' => $this->division_id,
            'address_line' => $this->address_line,
            'country' => $this->country,
            'social_links' => $this->social_links ? $this->social_links : [],
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
        ];
    }

    /**
     * @param $duration
     */
    private function getDuration($duration)
    {
        $duration_list = explode(":",$duration);

        $hour = $duration_list[0];
        $hour = $hour === '00' || $hour === '0'? "":"$hour:";
        $minute = $duration_list[1];
        $second = $duration_list[2];
        $second = $second>9? $second:"0$second";
  
        return "$hour$minute:$second";
    }

}
