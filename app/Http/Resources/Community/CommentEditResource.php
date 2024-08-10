<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentEditResource extends JsonResource
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
            'content' => $this->content,
            'status' => $this->status,
            'customer' => $this->getCustomerInfo($this->customer, $this->is_anonymous),
            'total_replies' => $this->replies->count(),
            'replies' => CommentAppResource::collection($this->replies),
            'is_like' => !!$this->likes->where('customer_id', '=', $customer_id)->count(),
            'is_dislike' => !!$this->dislikes->where('customer_id', '=', $customer_id)->count(),
            'total_likes' => $this->likes->count()?:0,
            'total_dislikes' => $this->dislikes->count()?:0,
            'created_at' => $this->created_at,
            'created_from' => $this->created_at->diffForHumans(),
        ];
    }

    private function getCustomerInfo($customer, $is_anonymous){
        return [
            'id' => $customer?->id,
            'name' => $is_anonymous ? 'Mogu parent':$customer?->name,
            'avatar' => $is_anonymous ? asset('assets/images/user-default.png'):$customer?->avatar,
        ];
    }
}
