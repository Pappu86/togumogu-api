<?php

namespace App\Http\Resources\Corporate;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CompanyAppResource extends JsonResource
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
            'logo' => $this->logo,
            'badge' => $this->badge,
            'name' => $this->name,
            'status' => $this->status,
            'type' => $this->type,
            'address' => $this->address,
            'website' => $this->website
        ];
    }
}