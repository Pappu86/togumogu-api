<?php

namespace App\Http\Resources\Brand;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandOutletEditResource extends JsonResource
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
            'name' => $this->name,
            'website_link' => $this->website_link,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'brand_id' => $this->brand_id,
            'area_id' => $this->area_id,
            'district_id' => $this->district_id,
            'division_id' => $this->division_id,
            'address_line' => $this->address_line,
            'country' => $this->country,
            'short_description' => $this->short_description,
        ];
    }
}
