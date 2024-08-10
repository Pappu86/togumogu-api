<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CustomNotificationEditResource extends JsonResource
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
            'template_id' => $this->template_id,
            'platform' => $this->platform,
            'process_status' => $this->process_status,
            'scheduling_type' => $this->scheduling_type,
            'scheduling_date' => $this->scheduling_date,
            'target' => $this->target,
            'is_android' => $this->is_android?:0,
            'is_ios' => $this->is_ios?:0,
            'created_at' => $this->created_at,
            'notification_type' => $this->notification_type,
            'period' => $this->period,
            'activity' => $this->activity,
            'days' => $this->days,
        ];
    }
}