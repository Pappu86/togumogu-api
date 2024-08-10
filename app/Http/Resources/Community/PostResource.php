<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PostResource extends JsonResource
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
            'content' => Str::substr($this->content, 0, 10),
            'customer' => [
                'id' => $this->customer?->id?:'',
                'name' => $this->customer?->name?:'',
            ],
            'age_group' => $this->ageGroup,
            'status' => $this->status,
            'created_at' => $this->created_at->toDayDateTimeString(),
        ];
    }
}
