<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ReportSingleResource extends JsonResource
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
            'note' => $this->note,
            'customer' => [
                'id' => $this->customer?->id?:'',
                'name' => $this->customer?->name?:'',
                'avatar' => $this->customer?->avatar?:'',
            ],
            'reason' => [
                'id' => $this->reason?->id?:'',
                'title' => $this->reason?->title?:'',
            ],
            'post' => $this->post?[
                'id' => $this->post?->id?:'',
                'content' => $this->post?->content? Str::substr($this->post->content, 0, 10):null,
            ]:null,
            'comment' => $this->comment?[
                'id' => $this->comment?->id?:'',
                'content' => $this->comment?->content? Str::substr($this->comment->content, 0, 10):null,
            ]:null,
            'status' => $this->status,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'created_from' => $this->created_at->diffForHumans(),
        ];
    }
}
