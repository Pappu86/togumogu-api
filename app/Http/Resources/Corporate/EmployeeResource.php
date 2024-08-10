<?php

namespace App\Http\Resources\Corporate;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EmployeeResource extends JsonResource
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
            'phone' => $this->phone,
            'email' => $this->email,
            'is_registered' => $this->is_registered,
            'company_employee_id' => $this->company_employee_id,
            'designation' => $this->designation,
            'join_date' => $this->join_date,
            'created_at' => $this->created_at,
        ];
    }
}