<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TemplateEditResource extends JsonResource
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
            'category' => $this->category,
            'subject' => $this->subject,
            'content' => $this->content,
            'image' => $this->image,
            'is_dynamic_value' => $this->is_dynamic_value,
            'main_template_id' => $this->main_template_id,
            'ad_channel_name' => $this->ad_channel_name,
            'ad_custom_data' => $this->ad_custom_data,
            'ad_sound' => $this->ad_sound?:'enabled',
            'ad_apple_badge' => $this->ad_apple_badge?:'disabled',
            'ad_expire_value' => $this->ad_expire_value,
            'ad_expire_unit' => $this->ad_expire_unit,
            'ad_apple_badge_count' => $this->ad_apple_badge_count?:0,
            'created_at' => $this->created_at,
        ];
    }
}