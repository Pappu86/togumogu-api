<?php

namespace App\Http\Resources\Daycare;

use Illuminate\Http\Resources\Json\JsonResource;

class DaycareImageResource extends JsonResource
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
