<?php

namespace App\Http\Resources\Blog;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class ArticleResource extends JsonResource
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
            'title' => $this->title,
            'status' => $this->status,
            'shortLink' => $this->shortLink,
            'previewLink' => $this->previewLink,
            'tracker' => $this->tracker,
            'tracker_range' => json_decode($this->tracker_range),
            'categories' => $this->getCategoriesName($this->categories),
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
}
