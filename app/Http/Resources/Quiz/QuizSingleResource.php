<?php

namespace App\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Quiz\QuestionAppResource;
use Illuminate\Support\Facades\Gate;

class QuizSingleResource extends JsonResource
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
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'slug' => $this->slug,
            'platforms' => $this->platforms,            
            'area' => $this->area,            
            'max_time' => $this->max_time,
            'total_points' => $this->total_points,
            'image' => $this->image,
            'reward_points' => $this->reward_points,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'description' => $this->description,
            'meta_description' => $this->meta_description,
            'meta_keyword' => $this->meta_keyword,
            'button_text' => $this->button_text,
            'dynamic_link' => $this->dynamic_link,
            'is_featured' => $this->is_featured,
            'color' => $this->color,
            'terms_and_conditions' => $this->terms_and_conditions,
            'retry_allow' => $this->retry_allow,
            'ending_msg' => $this->ending_msg,
            'questions' => QuestionAppResource::collection($this?->questions)->where('status', 'active')->all(),
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->updated_at->toDayDateTimeString()
        ];
    }
}
