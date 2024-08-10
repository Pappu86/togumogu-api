<?php

namespace App\Http\Resources\Child;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Http\Resources\Child\SchoolResource;

class ChildClassResource extends JsonResource
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
            'type' => $this->type,
            // 'school' => new SchoolResource($this->school),
        ];
    }
}