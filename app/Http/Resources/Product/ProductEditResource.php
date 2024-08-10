<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Blog\CategoryAllResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductEditResource extends JsonResource
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
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keyword' => $this->meta_keyword,
            'excerpt' => $this->excerpt,
            'tabs' => $this->tabs,
            'categories' => $this->categories,
            'image' => $this->image,
            'meta_image' => $this->meta_image,
            'status' => $this->status,
            'slug' => $this->slug,
            'datetime' => $this->datetime,
            'filters' => $this->filters,
            'tags' => $this->tags,
            'price' => $this->price,
            'purchased_price' => $this->purchased_price,
            'special_price' => $this->special_price,
            'special_start_date' => $this->special_start_date,
            'special_end_date' => $this->special_end_date,
            'weight' => $this->weight,
            'width' => $this->width,
            'height' => $this->height,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'min' => $this->min,
            'max' => $this->max,
            'warranty_note' => $this->warranty_note,
            'delivery_note' => $this->delivery_note,
            'payment_note' => $this->payment_note,
            'images' => $this->images,
            'tracker' => $this->tracker,
            'tracker_start_day' => $this->tracker_start_day,
            'tracker_end_day' => $this->tracker_end_day,
            'tracker_range' => $this->tracker_range ? json_decode($this->tracker_range):null,
            'video_url' => $this->video_url
        ];
    }
}
