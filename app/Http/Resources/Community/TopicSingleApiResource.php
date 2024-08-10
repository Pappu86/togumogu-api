<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;

class TopicSingleApiResource extends JsonResource
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
            'posts_count' => $this->posts_count,
            'children' => self::collection($this->children),
        ];
    }
}
