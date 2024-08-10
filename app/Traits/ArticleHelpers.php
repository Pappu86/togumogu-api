<?php

namespace App\Traits;

use App\Models\Blog\Article;
use App\Models\Reward\RewardSetting;
use App\Models\Reward\RewardTransaction;
use App\Notifications\Reward\ArticleReadRewardPoint;
use App\Notifications\Reward\PostCreatedRewardPoint;
use Illuminate\Support\Facades\Log;

trait ArticleHelpers {

    /**
     * @param $customer
     * @param Article $article
     * @return int
     */
    public function addArticleReadRewardPoints( $customer, Article $article): int
    {
        $rewardSetting = RewardSetting::where('category', 'article_read')
                            ->where('status', 'active')
                            ->first();

        $points = $rewardSetting?->award_points?:0;
        $isReturn = false;

        if($points){
           $reward = RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $customer?->id,
                'reference_id' => $article?->id,
                'credit' => $points,
                'reward_setting_id' => $rewardSetting->id,
                'category' => 'article',
                'debit' => 0,
                'description' => 'Article/Blog read',
                'action' => 'article_read',
            ]);

            //Send database notifications 
            if(isset($customer)) {
                $customer?->notify((new ArticleReadRewardPoint($customer, $article, $reward))); 
            }
            $isReturn = true;
        }

        return $isReturn? $points:0;
    }

}