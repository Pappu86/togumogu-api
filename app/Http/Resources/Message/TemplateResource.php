<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'type' => $this->type,
            'main_template_id' => $this->main_template_id,
            'created_at' => $this->created_at->toDayDateTimeString(),
        ];
    }
}