<?php

namespace App\Http\Resources\Daycare;

use Illuminate\Http\Resources\Json\JsonResource;

class DaycareSingleApiResource extends JsonResource
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
            'code' => $this->code,
            'tgmg_rating' => $this->tgmg_rating,
            'customer_rating' => $this->customer_rating,
            'is_featured' => $this->is_featured,
            'contact' => $this->contact,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'social_links' => $this->social_links,
            'year' => $this->year,
            'rooms' => $this->rooms,
            'care_givers' => $this->care_givers,
            'capacity' => $this->capacity,
            'booked' => $this->booked,
            'area' => $this->area,
            'age_range' => $this->age_range ? "{$this->age_range['from_age']} {$this->age_range['from_period']} - {$this->age_range['to_age']} {$this->age_range['to_period']}" : null,
            'time_range' => $this->time_range ? "{$this->time_range['from_time']} - {$this->time_range['to_time']}" : null,
            'opening_days' => $this->opening_days,
            'fees' => $this->fees,
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
            'created_at' => $this->created_at,
            'features' => getFeatures($this->features),
            'images' => DaycareImageResource::collection($this->images),
            'ratings' => DaycareRatingResource::collection($this->ratings),
            'shortLink' => $this->shortLink,
        ];
    }
}

// Create a array of feature for api
function getFeatures($features) {
    $data = [];
    foreach ($features as $feature) {
        array_push($data, (object)[
            'text' => $feature['title'],
            'value' => $feature->pivot['active']
        ]);
    };

    return $data;
  };
