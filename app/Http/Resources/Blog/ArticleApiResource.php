<?php

namespace App\Http\Resources\Blog;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleApiResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => $this->image,
            'excerpt' => $this->excerpt,
            'datetime' => $this->datetime,
            'view_count' => $this->view_count,
            'author' => $this->user ? $this->user->name : '-',
        ];
    }
}
