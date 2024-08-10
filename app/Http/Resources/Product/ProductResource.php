<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'datetime' => $this->datetime->toDayDateTimeString(),
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'shortLink' => $this->shortLink,
            'previewLink' => $this->previewLink,
            'facebookLink' => $this->facebookLink,
        ];
    }
}
