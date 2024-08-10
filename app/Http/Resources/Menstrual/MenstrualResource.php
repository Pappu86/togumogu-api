<?php

namespace App\Http\Resources\Menstrual;

use Illuminate\Http\Resources\Json\JsonResource;

class MenstrualResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
