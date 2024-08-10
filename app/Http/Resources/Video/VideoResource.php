<?php

namespace App\Http\Resources\Video;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class VideoResource extends JsonResource
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
            'datetime' => $this->datetime->toDayDateTimeString(),
            'title' => $this->title,
            'status' => $this->status
        ];
    }
}
