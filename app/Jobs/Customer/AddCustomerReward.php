<?php

namespace App\Jobs\Customer;

use App\Jobs\DeepLink\AddReferralDeepLink;
use App\Models\Reward\RewardSetting;
use App\Models\Reward\RewardTransaction;
use App\Models\Reward\Referral;
use App\Models\User\Customer;
use App\Traits\NotificationHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AddCustomerReward implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue,
     Queueable, SerializesModels, NotificationHelpers;

    /**
     * @var
     */
    protected $customer;
    protected $customer_id;
    protected $partnership_id;
    protected $referral_uid;
    protected $referral_type;
    protected $reference_id;

    public function __construct($customer, $link_data)
    {
        $this->customer = $customer?:'';
        $this->customer_id = $customer?->id?:'';
        $this->partnership_id = $link_data['partnership_id']?:'';
        $this->referral_uid = $link_data['uid']?:'';
        $this->referral_type = $link_data['type']?:'';   
        $this->reference_id = $link_data['reference_id']?:'';     
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $receiver_reward_settings = RewardSetting::where('status', 'active')
            ->where('category', '=', 'receiver_reward_point_for_signup')->first();

        if(isset($this->customer_id) && isset($receiver_reward_settings)) {
            // Here will be change referene id when work customer referrall
            RewardTransaction::create([
                'status' => 'active',
                'customer_id' => $this->customer_id,
                'reference_id' => $receiver_reward_settings->id,
                'credit' => $receiver_reward_settings->award_points,
                'reward_setting_id' => $receiver_reward_settings->id,
                'category' => $this->referral_type,
                'debit' => 0,
            ]);
        };

        // Adding reference reward points         
        if($this->referral_type === 'customer') {   
            $sender_reward_settings = RewardSetting::where('status', 'active')
                ->where('category', '=', 'sender_reward_point_for_signup')->first();

            if(isset($this->reference_id) && isset($sender_reward_settings)) {
                // here will be change referene id when work customer referrall
               $createdSenderReward = RewardTransaction::create([
                                        'status' => 'active',
                                        'customer_id' => $this->reference_id,
                                        'reference_id' => $sender_reward_settings->id,
                                        'credit' => $sender_reward_settings->award_points,
                                        'reward_setting_id' => $sender_reward_settings->id,
                                        'category' => $this->referral_type,
                                        'debit' => 0,
                                    ]);

                // Sender customer give reward point for his/her referral link
                // Then he/she will be give a notification with reward point                    
                if(isset($createdSenderReward)){
                    $sender = Customer::find($this->reference_id);
                    $receiver = Customer::find($this->customer_id);
                    $this->sendNotifyOfReferralRewardPoint($sender, $receiver, $createdSenderReward);
                }
            }
        };

        //Create referral doc with reference id
        $referral = Referral::where('customer_id', $customer->id)
                        ->where('type', 'customer');

        //Check already generated referral link of this customer
        if(!$referral->count()){
            $this->customer['referral_type'] = 'customer';
            $this->customer['reference_id'] = $this->reference_id;
            AddReferralDeepLink::dispatch($this->customer);  
        }

    }

}
