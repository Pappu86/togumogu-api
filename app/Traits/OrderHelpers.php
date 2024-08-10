<?php

namespace App\Traits;

use App\Models\Order\Order;
use App\Models\Reward\Reward;
use App\Models\Reward\RewardSetting;
use App\Models\Reward\RewardTransaction;
use App\Models\User\Customer;
use App\Notifications\Reward\OrderRewardPoint;
use Illuminate\Support\Facades\Log;

trait OrderHelpers {

    /**
     * @return string
     */
    public function generateOrderAndInvoiceNumber(): string
    {
        $today = date("ym");
        $last_order = Order::latest()->first();
        $last_order_id = $last_order?->id?:0;
        $order_count = $last_order_id+1;
        $prefix = '';
        $string_length = strlen("$order_count");
        if($string_length=='1') $prefix = '00000';
        else if($string_length=='2') $prefix = '0000';
        else if($string_length=='3') $prefix = '000';
        else if($string_length=='4') $prefix = '00';
        else if($string_length=='5') $prefix = '0';

        $order_serial_id = "{$prefix}{$order_count}";
        return "{$today}{$order_serial_id}";
    }

    /**
     * @param Order $order
     * @return int
     */
    public function addOrderRewardPoints(Order $order): int
    {
        
        $rewardSetting = RewardSetting::where('category', 'order_created')
                            ->where('status', 'active')
                            ->first();

        $minAmount = $rewardSetting?->min_amount;
        $maxAwardPoints = $rewardSetting?->max_award_points;
        $totalOrderAmount = $order?->total_amount;
        $isReturn = false;

        if((isset($minAmount) && $minAmount < $totalOrderAmount) || !isset($minAmount)){
            $settingType = $rewardSetting->type;
            $points = $rewardSetting?->award_points?:0;

            if($settingType === 'percentage'){
                $totalPertage = round($totalOrderAmount/100);
                $points = $totalPertage * $points;
            };

            //If has maximum award points at a times for order then set max award points
            if(isset($maxAwardPoints) && $points > $maxAwardPoints){
                $points = $maxAwardPoints;
            }

           $reward = RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $order?->customer_id,
                'reference_id' => $order?->id,
                'credit' => $points,
                'reward_setting_id' => $rewardSetting->id,
                'category' => 'order',
                'debit' => 0,
                'description' => 'Order created',
                'action' => 'order_created',
            ]);

            //Send database notifications 
            $customer = Customer::find($order?->customer_id);
            $options = [
                'type' => 'add'
            ];
            $customer?->notify((new OrderRewardPoint($customer, $order, $reward, $options))); 

            $isReturn = true;
        }

        return $isReturn? $points:0;
    }

    /**
     * @param $order
     * @return string
     */
    public function deductionOrderRewardPoints($order): string
    {

        $rewardTransaction = RewardTransaction::where('status', 'active')
            ->where('reference_id', $order?->id)
            ->where('action', 'order_created')
            ->where('category', 'order')
            ->where('customer_id', $order?->customer_id)
            ->where('credit','>', 0)
            ->where('debit','=', 0)
            ->first();

        $totalDeductAmount = $rewardTransaction?->credit;
        $customerReward = Reward::where('status', 'active')
            ->where('customer_id', $order?->customer_id)
            ->first();

        if((isset($customerReward) && $totalDeductAmount < $customerReward?->balance)){
            $reward = RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $order?->customer_id,
                'reference_id' => $order?->id,
                'credit' => 0,
                'reward_setting_id' => $rewardTransaction->reward_setting_id,
                'category' => 'order',
                'debit' => $totalDeductAmount,
                'description' => 'Points deduction after order cancelled',
            ]);

            //Send database notifications 
            $customer = Customer::find($order?->customer_id);
            $options = [
                'type' => 'deduction'
            ];
            $customer?->notify((new OrderRewardPoint($customer, $order, $reward, $options))); 
        }

        return 'success';
    }

}