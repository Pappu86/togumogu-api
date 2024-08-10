<?php

namespace App\Http\Resources\Quiz;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class QuizAppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $totalParticipants = DB::table('quiz_results')->where('quiz_id', '=', $this->id)->count();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'slug' => $this->slug,
            'image' => $this->image,
            'reward_points' => $this->reward_points,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'button_text' => $this->button_text,
            'dynamic_link' => $this->dynamic_link,
            'color' => $this->color,
            'reward_points' => $this->reward_points,
            'max_time' => $this->max_time,
            'total_questions' => count($this?->questions),
            'total_participants' => $totalParticipants,
            'ending_msg' => $this->ending_msg,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->updated_at->toDayDateTimeString()
        ];
    }
}
