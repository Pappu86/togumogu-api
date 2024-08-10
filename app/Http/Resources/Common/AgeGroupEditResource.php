<?php

namespace App\Http\Resources\Common;

use App\Http\Resources\CategoryTreeChildResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AgeGroupEditResource extends JsonResource
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
            'start' => $this->start,
            'end' => $this->end,
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
//      
    }
}
