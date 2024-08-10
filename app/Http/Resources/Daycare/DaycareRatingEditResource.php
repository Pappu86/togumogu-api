<?php

namespace App\Http\Resources\Daycare;

use Illuminate\Http\Resources\Json\JsonResource;

class DaycareRatingEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'facility' => $this->facility,
            'fee' => $this->fee,
            'security' => $this->security,
            'hygiene' => $this->hygiene,
            'care_giving' => $this->care_giving,
            'comment' => $this->comment,
        ];
    }
}
