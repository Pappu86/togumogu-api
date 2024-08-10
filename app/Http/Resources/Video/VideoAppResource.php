<?php

namespace App\Http\Resources\Video;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Video\CategoryResource;

class VideoAppResource extends JsonResource
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
            // 'categories' => $this->categories,
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => $this->image,
            'excerpt' => $this->excerpt,
            'url' => $this->url,
            'video_language' => $this->video_language === 'en'?"English":"Bangla",
            'video_type' => $this->video_type,
            'live_start' => $this->live_start,
            'duration' => $this->duration?$this->getDuration($this->duration):"0:00",
            'datetime' => $this->datetime,
            'view_count' => $this->view_count,
            'author' => $this->user ? $this->user->name : '-',
            'content' => $this->content,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keyword' => $this->meta_keyword,
        ];
    }

     /**
     * @param $duration
     */
    private function getDuration($duration)
    {
      $duration_list = explode(":",$duration);

      $hour = $duration_list[0];
      $hour = $hour === '00' || $hour === '0'? "":"$hour:";
      $minute = $duration_list[1];
      $second = $duration_list[2];
      $second = $second>9? $second:"0$second";

      return "$hour$minute:$second";
    }
}
