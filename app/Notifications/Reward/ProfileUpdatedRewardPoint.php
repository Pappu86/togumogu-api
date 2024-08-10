<?php

namespace App\Notifications\Reward;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ProfileUpdatedRewardPoint extends Notification implements ShouldQueue
{
    use Queueable;
 /**
     * who made the Post.
     *
     * @var
     */
    public $customer;

    /**
     * @var
     */
    public $reward;
    
    /**
     * Create a new notification instance.
     *
     * @param $customer
     * @param $reward 
     */

    public function __construct($customer, $reward)
    {
        $this->customer = $customer;
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
            'customer' => $this->customer,
            'reward' => $this->reward,
            'link' => '/customer/' . $this->customer?->id,
            'type' => 'reward',
            'action' => 'profile_update_reward_added',
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
                'customer' => $this->customer,
                'reward' => $this->reward,
                'link' => '/customer/' . $this->customer?->id,
                'type' => 'reward',
                'action' => 'profile_update_reward_added',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }

}
