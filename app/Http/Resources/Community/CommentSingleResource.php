<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentSingleResource extends JsonResource
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
            'customer' => [
                'id' => $this->customer?->id?:'',
                'name' => $this->customer?->name?:'',
                'avatar' => $this->customer?->avatar?:'',
            ],
            'total_replies' => $this->replies->count(),
            'replies' => CommentAppResource::collection($this->replies),
            'reports' => ReportSingleResource::collection($this->reports),
            'share_count' => $this->share_count?:0,
            'is_like' => !!$this->likes->where('customer_id', '=', $customer_id)->count(),
            'is_dislike' => !!$this->dislikes->where('customer_id', '=', $customer_id)->count(),
            'total_likes' => $this->likes->count()?:0,
            'total_dislikes' => $this->dislikes->count()?:0,
            'created_from' => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at->toDayDateTimeString(),
        ];
    }
}
