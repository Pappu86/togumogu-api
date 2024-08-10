<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CommentResource extends JsonResource
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
                'avatar' => $this->customer?->avatar?:'',
            ],
            'post' => [
                'id' => $this->post?->id?:'',
                'content' => Str::substr($this->post?->content, 0, 10),
            ],
            'status' => $this->status,
            'created_at' => $this->created_at->toDayDateTimeString(),
        ];
    }
}
