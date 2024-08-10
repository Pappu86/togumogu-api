<?php

namespace App\Http\Resources\Child;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Child\HospitalResource;
use Carbon\Carbon;

class DoctorResource extends JsonResource
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
            'avatar' => $this->avatar,
            'department' => $this->department,
            'degree' => $this->degree,
            'contact_number' => $this->contact_number,
            'visiting_fee' => $this->visiting_fee,
            'website' => $this->website,
            'hospital' => new HospitalResource($this->hospital),
        ];
    }
}