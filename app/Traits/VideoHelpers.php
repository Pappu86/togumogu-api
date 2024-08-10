<?php

namespace App\Traits;

use App\Models\Video\Video;
use App\Models\Reward\RewardSetting;
use App\Models\Reward\RewardTransaction;
use App\Notifications\Reward\VideoWatchRewardPoint;
use Illuminate\Support\Facades\Log;

trait VideoHelpers {

    /**
     * @param $customer
     * @param Video $video
     * @return int
     */
    public function addVideoWatchRewardPoints( $customer, Video $video): int
    {
        $rewardSetting = RewardSetting::where('category', 'video_watch')
                            ->where('status', 'active')
                            ->first();

        $points = $rewardSetting?->award_points?:0;
        $isReturn = false;

        if($points){
           $reward = RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $customer?->id,
                'reference_id' => $video?->id,
                'credit' => $points,
                'reward_setting_id' => $rewardSetting->id,
                'category' => 'video',
                'debit' => 0,
                'description' => 'Video watch',
                'action' => 'video_watch',
            ]);

            //Send database notifications 
            $customer?->notify((new VideoWatchRewardPoint($customer, $video, $reward))); 

            $isReturn = true;
        }

        return $isReturn? $points:0;
    }

}