<?php

namespace App\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class QuizAnswerReviewResource extends JsonResource
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
            'answerer_id' => $this->answerer_id,
            'customer_id' => $this->customer_id,
            'quiz_id' => $this->quiz_id,
            'question_id' => $this->question_id,            
            'question_option_id' => $this->question_option_id,            
            'question' => $this->question?json_decode($this->question):null,
            'question_options' => $this->question_options?json_decode($this->question_options):null,
            'is_right_answer' => $this->is_right_answer,
            'answerer_question_score' => $this->answerer_score,
            'answer_option' => $this->answer_option?json_decode($this->answer_option):null,
            //'created_at' => $this?->created_at?->toDayDateTimeString(),
            //'updated_at' => $this?->updated_at?->toDayDateTimeString()
        ];
    }
}
