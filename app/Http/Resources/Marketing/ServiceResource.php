<?php

namespace App\Http\Resources\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status
        ];
    }
}
