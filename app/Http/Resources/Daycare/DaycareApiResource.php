<?php

namespace App\Http\Resources\Daycare;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Daycare\DaycareCategoryResource;

class DaycareApiResource extends JsonResource
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
            'tgmg_rating' => $this->tgmg_rating,
            'customer_rating' => $this->customer_rating,
            'name' => $this->name,
            'slug' => $this->slug,
            'location' => $this->location,
            'image' => $this->getDefaultImage($this->image),
            'created_at' => $this->created_at,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'category' => $this->getCategoriesName($this->categories),
            'categories' => DaycareCategoryResource::collection($this->categories),
            'distance' => $this->distance === null?"":$this->getDistance($this->distance, 'K'),
        ];
    }

     /**
     * @param $categories
     */
    private function getCategoriesName($categories)
    {
      $name_string = "";
      if(count($categories)) {
        $last_category_id = $categories[count($categories)-1]->id;
        foreach ($categories as $category) {
          $comma = ($category->id !== $last_category_id)?',':'';
          $name_string = "$name_string $category->name$comma";
        }
      }
      return $name_string;
    }

     /**
     * @param $distance
     */
    private function getDistance($distance, $type)
    {
          $type = strtoupper($type);
          $unit = $type;

          if ($type === "K") {
            $distance = ($distance * 1.609344);
            $unit = "km";

          } else if ($type === "N") {
            $distance = ($distance * 0.8684);
            $unit = "NM";

          } else {
            $unit = "miles";
          }

          $distance = number_format((float)$distance, 2, '.', '');
          return "$distance $unit";
    }
}
