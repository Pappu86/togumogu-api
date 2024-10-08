<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportReasonResource extends JsonResource
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
            'status' => $this->status,
            'created_at' => $this->created_at->toDayDateTimeString(),
        ];
    }
}
