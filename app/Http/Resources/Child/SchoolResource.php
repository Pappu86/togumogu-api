<?php

namespace App\Http\Resources\Child;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SchoolResource extends JsonResource
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
            'registration_number' => $this->registration_number,
            'name' => $this->name,
            'status' => $this->status,
            'type' => $this->type,
            'daycare_id' => $this->daycare_id,
            'address' => $this->address,
            'area_id' => $this->area_id,
            'contact_number' => $this->contact_number,
            'website' => $this->website,
        ];
    }
}