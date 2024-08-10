<?php

namespace App\Http\Resources\Daycare;

use Illuminate\Http\Resources\Json\JsonResource;

class DaycareEditResource extends JsonResource
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
            'code' => $this->code,
            'tgmg_rating' => $this->tgmg_rating,
            'customer_rating' => $this->customer_rating,
            'is_featured' => $this->is_featured,
            'categories' => $this->categories,
            'contact' => $this->contact,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'social_links' => $this->social_links ? $this->social_links : [],
            'year' => $this->year,
            'rooms' => $this->rooms,
            'care_givers' => $this->care_givers,
            'capacity' => $this->capacity,
            'booked' => $this->booked,
            'area' => $this->area,
            'age_range' => $this->age_range,
            'time_range' => $this->time_range,
            'opening_days' => $this->opening_days ? $this->opening_days : [],
            'fees' => $this->fees ? $this->fees : [],
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->getDefaultImage($this->image),
            'meta_image' => $this->meta_image,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keyword' => $this->meta_keyword,
            'hospital_address' => $this->hospital_address,
            'location' => $this->location,
            'datetime' => $this->created_at,
            'features' => $this->features,
            'ratings' => new DaycareRatingEditResource($this->adminRatings),
            'images' => $this->images ? $this->images : [],
            'division_id' => $this->division_id,
            'district_id' => $this->district_id,
            'area_id' => $this->area_id,
            'house' => $this->house,
            'street' => $this->street,
            'zip' => $this->zip,
        ];
    }
}
