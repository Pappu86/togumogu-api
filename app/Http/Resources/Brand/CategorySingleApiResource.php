<?php

namespace App\Http\Resources\Brand;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySingleApiResource extends JsonResource
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
            'slug' => $this->slug,
            'brands_count' => $this->brands_count,
            'image' => $this->image,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_image' => $this->meta_image,
            'meta_keyword' => $this->meta_keyword,
            'children' => self::collection($this->children),
        ];
    }
}
