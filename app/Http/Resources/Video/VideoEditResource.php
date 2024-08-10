<?php

namespace App\Http\Resources\Video;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoEditResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keyword' => $this->meta_keyword,
            'excerpt' => $this->excerpt,
            'image' => $this->image,
            'meta_image' => $this->meta_image,
            'status' => $this->status,
            'content' => $this->content,
            'slug' => $this->slug,
            'datetime' => $this->datetime,
            'tags' => $this->tags,
            'categories' => $this->categories,
            'tracker' => $this->tracker,
            'tracker_start_day' => $this->tracker_start_day,
            'tracker_end_day' => $this->tracker_end_day,
            'tracker_range' => $this->tracker_range ? json_decode($this->tracker_range):null,
            'url' => $this->url,
            'video_language' => $this->video_language,
            'video_type' => $this->video_type,
            'live_start' => $this->live_start,
            'sub_title' => $this->sub_title,
            'duration' => $this->duration,
        ];
    }
}
