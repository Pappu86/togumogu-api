<?php

namespace App\Http\Resources\Daycare;

use Illuminate\Http\Resources\Json\JsonResource;

class DaycareRatingResource extends JsonResource
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
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id,
            'comment' => $this->comment,
            'rating' => [
                [
                    'text' => 'Care Giving',
                    'value' => $this->care_giving,
                ],
                [
                    'text' => 'Hygiene',
                    'value' => $this->hygiene,
                ],
                [
                    'text' => 'Security',
                    'value' => $this->security,
                ],
                [
                    'text' => 'Fee',
                    'value' => $this->fee,
                ],
                [
                    'text' => 'Facility',
                    'value' => $this->facility,
                ],
            ]
        ];
    }
}
