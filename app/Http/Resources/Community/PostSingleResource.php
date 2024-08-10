<?php

namespace App\Http\Resources\Community;

use App\Http\Resources\Common\AgeGroupResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostSingleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $customer_id = Auth::id();
        return [
            'id' => $this->id,
            'status' => $this->status,
            'content' => $this->content,
            'images' => PostImageResource::collection($this->images),
            'customer' => [
                'id' => $this->customer?->id?:'',
                'name' => $this->customer?->name?:'',
                'avatar' => $this->customer?->avatar?:'',
            ],
            'comments' => CommentResource::collection($this->comments),
            'topics' => TopicResource::collection($this->topics),
            'reports' => ReportSingleResource::collection($this->reports),
            'age_group' => new AgeGroupResource($this->ageGroup),
            'share_count' => $this->share_count?:0,
            'view_count' => $this->view_count?:0,
            'is_anonymous' => $this->is_anonymous === '0'?false:true,
            'slug' => $this->slug,
            'is_like' => !!$this->likes->where('customer_id', '=', $customer_id)->count(),
            'is_dislike' => !!$this->dislikes->where('customer_id', '=', $customer_id)->count(),
            'total_likes' => $this->likes->count()?:0,
            'total_dislikes' => $this->dislikes->count()?:0,
            'created_from' => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at->toDayDateTimeString(),
        ];
    }
}
