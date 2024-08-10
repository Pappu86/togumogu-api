<?php

namespace App\Notifications\Reward;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ServiceRegistrationRewardPoint extends Notification implements ShouldQueue
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
    public $service;

    /**
     * @var
     */
    public $reward;
    
    /**
     * Create a new notification instance.
     *
     * @param $customer
     * @param $service 
     * @param $reward 
     */

    public function __construct($customer, $service, $reward)
    {
        $this->customer = $customer;
        $this->service = $service;
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
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'avatar' => $this->customer->avatar,
                'mobile' => $this->customer->mobile,
                'email' => $this->customer->email,
            ],
            'service' => [
                'id' => $this->service->id,
                'title' => $this->service->title,
                'slug' => $this->service->slug,
                'created_at' => $this->service->created_at,
                'updated_at' => $this->service->updated_at,
            ],
            'reward' => $this->reward,
            'link' => '/services/' . $this->service?->slug,
            'type' => 'reward',
            'action' => 'service_registration',
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
                'customer' => [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'avatar' => $this->customer->avatar,
                    'mobile' => $this->customer->mobile,
                    'email' => $this->customer->email,
                ],
                'service' => [
                    'id' => $this->service->id,
                    'title' => $this->service->title,
                    'slug' => $this->service->slug,
                    'created_at' => $this->service->created_at,
                    'updated_at' => $this->service->updated_at,
                ],
                'reward' => $this->reward,
                'link' => '/services/' . $this->service?->slug,
                'type' => 'reward',
                'action' => 'service_registration',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }



}
