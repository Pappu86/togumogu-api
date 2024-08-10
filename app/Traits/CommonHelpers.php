<?php

namespace App\Traits;

use App\Models\User\Customer;
use Illuminate\Support\Facades\Log;
use App\Models\Order\Order;
use App\Models\Reward\RewardSetting;
use App\Models\Reward\RewardTransaction;
use App\Notifications\Reward\ProfileUpdatedRewardPoint;
use Illuminate\Support\Facades\DB;
use App\Notifications\Reward\QuizSubmissionRewardPoint;

Class CommonHelpers {

    /**
     * @param number
     * @return string
     */
    public static function quickRandom($length = 8)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    /**
     * 
     * @param Customer $customer
     * @param string
     * @param string
     * @return object | string | mixed
     */
    public static function getCustomerSettings($customer, $category, $key)
    {
        return DB::table('customer_settings')->where('status', 'active')
                ->where('customer_id', $customer?->id)
                ->where('category', $category)
                ->where('key', $key)->first();
    }

    /**
     * 
     * @param Customer $customer
     * @param string
     * @param string
     * @return boolean
     */
    public static function isSettingEnabled($customer, $category, $key)
    {

        $setting = DB::table('customer_settings')->where('status', 'active')
                ->where('customer_id', $customer?->id)
                ->where('category', $category)
                ->where('key', $key)->first();

        if(!isset($setting) || ($setting->value === 'true')){
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @return boolean
     */
    public static function isChangedForDynamicLink($exCollection, $newCollection)
    {
        
        //Existing collection data
        $exSlug = $exCollection->slug;
        $exName = $exCollection->name;
        $exMetaTitle = $exCollection->meta_title;
        $exMetaDescription = $exCollection->meta_description;
        $exExcerpt = $exCollection->excerpt;
        $exMetaImage = $exCollection->meta_image;
        $exImage = $exCollection->image;

        //New collection data
        $slug = $newCollection->slug;
        $name = $newCollection->name;
        $metaTitle = $newCollection->meta_title;
        $metaDescription = $newCollection->meta_description;
        $excerpt = $newCollection->excerpt;
        $metaImage = $newCollection->meta_image;
        $image = $newCollection->image;

        return !(($exSlug === $slug) && ($exName === $name) && ($exMetaTitle === $metaTitle)
        && ($exMetaDescription === $metaDescription) && ($exExcerpt === $excerpt) && ($exMetaImage === $metaImage)
        && ($exImage === $image))?true:false;
    }

    /**
     * 
     * @return int
     */
    public static function getPendingPoints($customerId)
    {
        
        $pendingOrderIds = Order::where('customer_id', '=', $customerId)
            ->whereNotIn('order_status', ['completed', 'cancelled', 'failed'])
            ->pluck('id');

        $transactions = RewardTransaction::where('status', 'active')
            ->where('customer_id', '=', $customerId)
            ->where('category', 'order')
            ->where('action', 'order_created')
            ->whereIn('reference_id', $pendingOrderIds);

        return $transactions->sum('credit')*1;
    }

    /**
     * 
     * name  --------------- 10
     * email --------------- 10
     * mobile -------------- 10
     * avatar -------------- 10
     * blood_group --------- 10
     * gender -------------- 10
     * date_of_birth ------- 10
     * parent_type ---------  5
     * primary_language ----  5
     * religion ------------  5
     * education -----------  5
     * position  -----------  5
     * profession ----------  5
     * @param $customer
     * @return int
     */
    public static function getProfileProgress($customer)
    {
        $progress = 0;

        if($customer?->email) $progress += 10;
        if($customer?->mobile) $progress += 10;
        if($customer?->gender) $progress += 10;
        if($customer?->blood_group) $progress += 10;
        if($customer?->date_of_birth) $progress += 10;
        if($customer?->parent_type) $progress += 5;
        if($customer?->primary_language) $progress += 5;
        if($customer?->religion) $progress += 5;
        if($customer?->education) $progress += 5;
        if($customer?->position) $progress += 5;
        if($customer?->profession) $progress += 5;

        // Customer default name is not calculate 
        if($customer?->name && !($customer?->name === $customer?->email || $customer?->name === $customer?->mobile)) {
            $progress += 10;
        }

        // Customer default avatar is not count for progress
        if($customer?->avatar !== asset('assets/images/user-default.png')) {
            $progress += 10;
        }

       return $progress;
    }

    /**
     * @param Customer $customer
     * @return int
     */
    public function addProfileUpdateRewardPoints(Customer $customer): int
    {
        
        $rewardSetting = RewardSetting::where('category', 'profile_updated')
                            ->where('status', 'active')
                            ->first();

        $points = $rewardSetting?->award_points?:0;
        $isReturn = false;

        if($points){

           $reward = RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $customer->id,
                'reference_id' => $customer->id,
                'credit' => $points,
                'reward_setting_id' => $rewardSetting->id,
                'category' => 'customer',
                'debit' => 0,
                'description' => 'Profile updated',
                'action' => 'profile_updated',
            ]);

            //Send database notifications 
            $customer?->notify((new ProfileUpdatedRewardPoint($customer, $reward))); 

            $isReturn = true;
        }

        return $isReturn? $points:0;
    }

    /**
     * @param $quizResult
     * @return int
     */
    public function addQuizSubmissionRewardPoints($quizResult): int
    {
        $rewardSetting = RewardSetting::where('category', 'quiz_submission')
                            ->where('status', 'active')
                            ->first();

        $points = $rewardSetting?->award_points?:0;
        $isReturn = false;

        if($points){

           $reward = RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $quizResult?->customer_id,
                'reference_id' => $quizResult?->id,
                'credit' => $points,
                'reward_setting_id' => $rewardSetting->id,
                'category' => 'quiz',
                'debit' => 0,
                'description' => 'Quiz submission',
                'action' => 'quiz_submission',
            ]);

            //Send database notifications 
            if($quizResult?->customer_id) {
                $customer = Customer::find($quizResult?->customer_id);
                $customer?->notify((new QuizSubmissionRewardPoint($customer, $quizResult, $reward))); 
            }
            
            $isReturn = true;
        }

        return $isReturn? $points:0;
    }
}