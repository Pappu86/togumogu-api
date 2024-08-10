<?php

namespace App\Observers;

use App\Models\Reward\Reward;
use App\Models\Reward\RewardTransaction;

class RewardTransactionObserver
{

    /**
     * Handle the RewardTransaction "created" event.
     *
     * @param RewardTransaction $rewardTransaction
     * @return void
     */
    public function created(RewardTransaction $rewardTransaction)
    {
        //Adding reward according to reward transaction by the customer 
        $reward = Reward::where('customer_id', '=', $rewardTransaction->customer_id)->first();
        
        if(isset($reward)) { 
            $total = $reward->total;
            $balance = $reward->balance;
            $redeem = $reward->redeem;

            //When reward transaction is credit 
            if($rewardTransaction->credit){
                $balance = $balance + ($rewardTransaction->credit);
                $total = $total + ($rewardTransaction->credit);
            }

            //When reward transaction is dedit 
            if($rewardTransaction->debit){
                $balance = $balance - ($rewardTransaction->debit);
                $redeem = $redeem + ($rewardTransaction->debit);
            }

            $reward->update([
                'total' => $total,
                'balance' => $balance,
                'redeem' => $redeem,
            ]);   

        } else {
            Reward::create([
                'status' => 'active',
                'customer_id' => $rewardTransaction->customer_id,
                'total' => $rewardTransaction->credit,
                'balance' => $rewardTransaction->credit,
                'redeem' => 0,
            ]);
        }
        
    }

    /**
     * Handle the RewardTransaction "updated" event.
     *
     * @param  RewardTransaction  $rewardTransaction
     * @return void
     */
    public function updated(RewardTransaction $rewardTransaction)
    {
        
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param RewardTransaction $rewardTransaction
     * @return void
     */
    public function deleted(RewardTransaction $rewardTransaction)
    {
        //
    }

    /**
     * Handle the RewardTransaction "restored" event.
     *
     * @param RewardTransaction  $rewardTransaction
     * @return void
     */
    public function restored(RewardTransaction $rewardTransaction)
    {
        //
    }

    /**
     * Handle the RewardTransaction "force deleted" event.
     *
     * @param RewardTransaction $rewardTransaction
     * @return void
     */
    public function forceDeleted(RewardTransaction $rewardTransaction)
    {
        //
    }
}
