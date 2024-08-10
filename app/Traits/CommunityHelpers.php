<?php

namespace App\Traits;

use App\Models\Community\Post;
use App\Models\Community\Comment;
use App\Models\Reward\RewardSetting;
use App\Models\Reward\RewardTransaction;
use App\Models\User\Customer;
use App\Notifications\Reward\PostCreatedRewardPoint;
use App\Notifications\Reward\CommentCreatedRewardPoint;
use Illuminate\Support\Facades\Log;

trait CommunityHelpers {

    /**
     * @param Post $post
     * @return int
     */
    public function addPostCreatedRewardPoints(Post $post): int
    {
        $rewardSetting = RewardSetting::where('category', 'post_created')
                            ->where('status', 'active')
                            ->first();

        $points = $rewardSetting?->award_points?:0;
        $isReturn = false;

        if($points){

           $reward = RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $post?->customer_id,
                'reference_id' => $post?->id,
                'credit' => $points,
                'reward_setting_id' => $rewardSetting->id,
                'category' => 'post',
                'debit' => 0,
                'description' => 'Post created',
                'action' => 'post_created',
            ]);

            //Send database notifications 
            if($post?->customer_id) {
                $customer = Customer::find($post?->customer_id);
                $customer?->notify((new PostCreatedRewardPoint($customer, $post, $reward))); 
            }
            
            $isReturn = true;
        }

        return $isReturn? $points:0;
    }

    /**
     * @param Post $post
     * @param Comment $comment
     * @return int
     */
    public function addCommentOnPostRewardPoints(Post $post, Comment $comment): int
    {
        $rewardSetting = RewardSetting::where('category', 'post_comment')
                            ->where('status', 'active')
                            ->first();

        $points = $rewardSetting?->award_points?:0;
        $isReturn = false;

        if($points){

           $reward = RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $comment?->customer_id,
                'reference_id' => $comment?->id,
                'credit' => $points,
                'reward_setting_id' => $rewardSetting->id,
                'category' => 'comment',
                'debit' => 0,
                'description' => 'Comment created Post',
                'action' => 'post_comment',
            ]);

            //Send database notifications 
            if($comment?->customer_id) {
                $customer = Customer::find($comment?->customer_id);
                $customer?->notify((new CommentCreatedRewardPoint($customer, $post, $comment, $reward))); 
            }
            
            $isReturn = true;
        }

        return $isReturn? $points:0;
    }

}