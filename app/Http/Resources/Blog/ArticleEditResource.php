<?php

namespace App\Http\Resources\Blog;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleEditResource extends JsonResource
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
            'video_url' => $this->video_url,
            'audio_url' => $this->audio_url,
        ];
    }
}
