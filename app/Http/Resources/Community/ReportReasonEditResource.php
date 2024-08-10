<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ReportReasonEditResource extends JsonResource
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
            'description' => $this->description,
            'slug' => $this->slug,
            'status' => $this->status,
        ];
    }
}
