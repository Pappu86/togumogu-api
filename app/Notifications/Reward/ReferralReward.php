<?php

namespace App\Notifications\Reward;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ReferralReward extends Notification implements ShouldQueue
{
    use Queueable;
    
    /**
     * @var
     */
    public $sender;

    /**
     * @var
     */
    public $receiver;

    /**
     * @var
     */
    public $reward;

    /**
     * Create a new notification instance.
     *
     * @param $sender 
     * @param $receiver 
     * @param $reward 
     */

    public function __construct($sender, $receiver, $reward)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->reward = $reward;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        return [
            'sender' => [
                'id' => $this->sender?->id,
                'name' => $this->sender?->name,
                'avatar' => $this->sender?->avatar,
                'mobile' => $this->sender?->mobile,
                'email' => $this->sender?->email,
                'referral' => $this->sender?->referral,
                'reward' => $this->sender?->reward,
            ],
            'receiver' => [
                'id' => $this->receiver?->id,
                'name' => $this->receiver?->name,
                'avatar' => $this->receiver?->avatar,
                'mobile' => $this->receiver?->mobile,
                'email' => $this->receiver?->email,
            ],
            'reward_transaction' => $this->reward,
            'link' => '/customer/' . $this->sender?->id,
            'type' => 'reward',
            'action' => 'sender_reward_point_for_signup',
        ];
    }

    /**
     * @param $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'data' => [
                'sender' => [
                    'id' => $this->sender?->id,
                    'name' => $this->sender?->name,
                    'avatar' => $this->sender?->avatar,
                    'mobile' => $this->sender?->mobile,
                    'email' => $this->sender?->email,
                    'referral' => $this->sender?->referral,
                    'reward' => $this->sender?->reward,
                ],
                'receiver' => [
                    'id' => $this->receiver?->id,
                    'name' => $this->receiver?->name,
                    'avatar' => $this->receiver?->avatar,
                    'mobile' => $this->receiver?->mobile,
                    'email' => $this->receiver?->email,
                    'referral' => $this->sender?->referral,
                    'reward' => $this->sender?->reward,
                ],
                'reward_transaction' => $this->reward,
                'link' => '/customer/' . $this->sender?->id,
                'type' => 'reward',
                'action' => 'sender_reward_point_for_signup',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }



}
