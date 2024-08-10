<?php

namespace App\Http\Resources\Corporate;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CompanyEditResource extends JsonResource
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
            'address' => $this->address,
            'logo' => $this->logo,
            'badge' => $this->badge,
            'reg_id' => $this->reg_id,
            'website' => $this->website,
            'details' => $this->details,
            'categories' => $this->categories,
            'created_at' => $this->created_at,
        ];
    }
}