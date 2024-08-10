<?php

namespace App\Http\Resources\Child;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class HospitalResource extends JsonResource
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
            'status' => $this->status,
            'name' => $this->name,
            'address' => $this->address,
            'area_id' => $this->area_id,
            'contact_number' => $this->contact_number,
            'website' => $this->website,
        ];
    }
}