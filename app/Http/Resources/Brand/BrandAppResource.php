<?php

namespace App\Http\Resources\Brand;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class BrandAppResource extends JsonResource
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
            'name' => $this->name,
            'logo' => $this->logo,
            'banner' => $this->banner,
            'slug' => $this->slug,
            'status' => $this->status,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
        ];
    }
}
