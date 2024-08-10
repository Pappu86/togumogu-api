<?php

namespace App\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class QuestionOptionResource extends JsonResource
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
            'question_id' => $this->question_id,
            'text' => $this->text,
            'image' => $this->image,
            'audio' => $this->audio,
            'video' => $this->video,
            'link' => $this->link,
            'hint' => $this->hint,
            'description' => $this->description,
            'is_answer' => $this->is_answer,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->updated_at->toDayDateTimeString()
        ];
    }
}
