<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;

class PostImageResource extends JsonResource
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
            'src' => $this->src,
            'is_featured' => $this->is_featured,
        ];
    }
}
