<?php

namespace App\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Quiz\QuestionOptionResource;
use Illuminate\Support\Facades\Gate;

class QuestionEditResource extends JsonResource
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
            'user_id' => $this->user_id,
            'serial_no' => $this->serial_no,
            'quiz_id' => $this->quiz_id,
            'status' => $this->status,
            'type' => $this->type,
            'title' => $this->title,
            'image' => $this->image,
            'audio' => $this->audio,
            'link' => $this->link,
            'time' => $this->time,
            'score' => $this->score,
            'link_text' => $this->link_text,
            'hint' => $this->hint,
            'description' => $this->description,
            'is_multiple' => $this->is_multiple,
            'options' => QuestionOptionResource::collection($this?->options),
            'tags' => $this?->tags?:[],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
