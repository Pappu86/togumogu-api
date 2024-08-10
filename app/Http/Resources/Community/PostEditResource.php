<?php

namespace App\Http\Resources\Community;

use App\Http\Resources\Common\AgeGroupResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PostEditResource extends JsonResource
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
            'customer' => $this->getCustomerInfo($this->customer, $this->is_anonymous),
            'comments' => CommentResource::collection($this->comments),
            'topics' => TopicResource::collection($this->topics),
            'age_group' => new AgeGroupResource($this->ageGroup),
            'share_count' => $this->share_count?:0,
            'view_count' => $this->view_count?:0,
            'is_anonymous' => $this->is_anonymous === '0'?false:true,
            'slug' => $this->slug,
            'is_like' => !!$this->likes->where('customer_id', '=', $customer_id)->count(),
            'is_dislike' => !!$this->dislikes->where('customer_id', '=', $customer_id)->count(),
            'total_likes' => $this->likes->count()?:0,
            'total_dislikes' => $this->dislikes->count()?:0,
            'created_at' => $this->created_at,
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