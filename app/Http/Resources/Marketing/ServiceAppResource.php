<?php

namespace App\Http\Resources\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceAppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'title' => $this->title,
            'is_free' => $this->is_free,
            'is_promoted' => $this->is_promoted,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'slug' => $this->slug,
            'image' => $this->image,
            'video_url' => $this->video_url,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'view_count' => $this->view_count,
            'registration_count' => $this->registration_count,
            'brand' => [
                'id' => $this->brand?->id,
                'name' => $this->brand?->name,
                'slug' => $this->brand?->slug,
                'logo' => $this->brand?->logo,
                'banner' => $this->brand?->banner,
            ],
        ];
    }
}
