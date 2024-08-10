<?php

namespace App\Http\Resources\Community;

use App\Http\Resources\Common\AgeGroupResource;
use App\Http\Resources\Common\TagResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PostAppResource extends JsonResource
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
        $reported = $this->getReported($this->reports);
        return [
            'id' => $this->id,
            'content' => $this->content,
            'images' => PostImageResource::collection($this->images),
            'customer' => $this->getCustomerInfo($this->customer, $this->is_anonymous),
            'total_comments' => $this->comments->count(),
            'topics' => TopicResource::collection($this->topics),
            'hashtags' => TagResource::collection($this->hashtags),
            'age_group' => new AgeGroupResource($this->ageGroup),
            'share_count' => $this->share_count?:0,
            'view_count' => $this->view_count?:0,
            'is_anonymous' => $this->is_anonymous === '0'?false:true,
            'slug' => $this->slug,
            'is_favorite' => !!$this->favorites->where('customer_id', '=', $customer_id)->count(),
            'is_like' => !!$this->likes->where('customer_id', '=', $customer_id)->count(),
            'is_dislike' => !!$this->dislikes->where('customer_id', '=', $customer_id)->count(),
            'total_likes' => $this->likes->count()?:0,
            'total_dislikes' => $this->dislikes->count()?:0,
            'created_at' => $this->created_at,
            'created_from' => $this->created_at->diffForHumans(),
            'visible' => (($this->customer?->id === $customer_id) && ($this->visible === '0'))?'me':'public',
            'is_blocked' => !!$reported,
            'reason' => !!$reported?[
                'id' => $reported?->reason?->id?:null,
                'description' => $reported?->reason?->description?:null,
                'slug' => $reported?->reason?->slug?:null,
                'title' => $reported?->reason?->title?:null,
            ]:null,
        ];
    }

    private function getCustomerInfo($customer, $is_anonymous){
        return [
            'id' => $customer?->id,
            'name' => $is_anonymous ? 'Anonymous':$customer?->name,
            'avatar' => $is_anonymous ? asset('assets/images/user-default.png'):$customer?->avatar,
        ];
    }

    private function getReported($reports){
      return collect($reports)->whereIn('status', ['pending', 'approved'])->first();
    }
}