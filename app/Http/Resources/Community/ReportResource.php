<?php

namespace App\Http\Resources\Community;

use App\Models\Community\Comment;
use App\Models\Community\Post;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $post = '';
        if($this->category === 'post') {
            $post = Post::find($this->reported_id);
        }

        $comment = '';
        if($this->category === 'comment') {
            $comment = Comment::find($this->reported_id);
        }

        return [
            'id' => $this->id,
            'note' => Str::substr($this->note, 0, 10),
            'customer' => [
                'id' => $this->customer?->id?:'',
                'name' => $this->customer?->name?:'',
                'avatar' => $this->customer?->avatar?:'',
            ],
            'reason' => [
                'id' => $this->reason?->id?:'',
                'title' => $this->reason?->title?:'',
            ],
            'post' => $post?[
                'id' => $post?->id?:'',
                'content' => $post?->content? Str::substr($post->content, 0, 10):null,
            ]:null,
            'comment' => $comment?[
                'id' => $comment?->id?:'',
                'content' => $comment?->content? Str::substr($comment->content, 0, 10):null,
            ]:null,
            'status' => $this->status,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'created_from' => $this->created_at->diffForHumans(),
        ];
    }
}
