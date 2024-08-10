<?php

namespace App\Http\Resources\Daycare;

use Illuminate\Http\Resources\Json\JsonResource;

class DaycareFeatureResource extends JsonResource
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
            'created_at' => $this->created_at->toDayDateTimeString(),
            'title' => $this->title,
            'status' => $this->status
        ];
    }
};