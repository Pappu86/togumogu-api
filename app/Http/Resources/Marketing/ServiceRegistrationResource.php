<?php

namespace App\Http\Resources\Marketing;

use App\Http\Resources\Order\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRegistrationResource extends JsonResource
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
            'created_at' => $this->created_at->toDayDateTimeString(),
            'service' => [
                'id' => $this->service?->id?:'',
                'title' => $this->service?->title?:''
            ],
            'customer' => [
                'id' => $this->customer?->id?:'',
                'name' => $this->customer?->name?:'',
                'mobile' => $this->customer?->mobile?:'',
                'email' => $this->customer?->email?:''
            ],
            'brand' => [
                'id' => $this->brand?->id?:'',
                'name' => $this->brand?->name?:''
            ],
            'start_date' => $this->start_date,
            'service_reg_no' => $this->service_reg_no,
            'service_reg_status' => $this->serviceRegistrationStatus ? new StatusResource($this->serviceRegistrationStatus) : null,
            'status' => $this->status,
            'customer_info' => $this->customer_info?json_decode($this->customer_info):'',
            'booking_info' => $this->booking_info?json_decode($this->booking_info):'',
            'questions' => $this->questions?json_decode($this->questions):'',
            'current_latitude' => $this->current_latitude,
            'current_longitude' => $this->current_longitude,
            'current_location_name' => $this->current_location_name
        ];
    }
}
