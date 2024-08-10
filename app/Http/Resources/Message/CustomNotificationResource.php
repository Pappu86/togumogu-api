<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomNotificationResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->status,
            'type' => $this->type,
            'created_at' => $this->created_at->toDayDateTimeString()
        ];
    }
}