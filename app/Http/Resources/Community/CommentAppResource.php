<?php

namespace App\Http\Resources\Community;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentAppResource extends JsonResource
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
            'customer' => $this->getCustomerInfo($this->customer, $this?->post),
            'total_replies' => $this->replies->count(),
            'replies' => $this->replies?CommentAppResource::collection($this->replies)->sortDesc()->take(2)->values()->all():[],
            'show_reply_box' => false,
            'is_like' => !!$this->likes->where('customer_id', '=', $customer_id)->count(),
            'is_dislike' => !!$this->dislikes->where('customer_id', '=', $customer_id)->count(),
            'total_likes' => $this->likes->count()?:0,
            'total_dislikes' => $this->dislikes->count()?:0,
            'created_at' => $this->created_at,
            'created_from' => $this->created_at->diffForHumans(),
        ];
    }

    private function getCustomerInfo($customer, $post){

        //Here if post creator was enable anonymous and he add comment/reply
        //Then He/She will be anonymous commenter or replier
        $isAnonymous = $post?->is_anonymous;
        $isPostOwner = !!($post?->customer_id === $customer?->id);

        return [
            'id' => $customer?->id,
            'name' => !!($isAnonymous && $isPostOwner)? 'Anonymous':$customer?->name,
            'avatar' => !!($isAnonymous && $isPostOwner)? asset('assets/images/user-default.png'):$customer?->avatar,
        ];
    }
}
