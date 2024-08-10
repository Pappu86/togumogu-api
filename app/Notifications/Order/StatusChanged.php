<?php

namespace App\Notifications\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class statusChanged extends Notification implements ShouldQueue
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
    public $order;
    /**
     * Create a new notification instance.
     *
     * @param $customer
     * @param $order 
     */

    public function __construct($customer, $order)
    {
        $this->customer = $customer;
        $this->order = $order;
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
            'order' => [
                'id' => $this->order->id,
            ],
            'link' => '/orders/' . $this->order?->id,
            'type' => 'order',
            'action' => 'status_changed',
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
                'order' => [
                    'id' => $this->order->id,
                ],
                'link' => '/orders/' . $this->order?->id,
                'type' => 'order',
                'action' => 'status_changed',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }
}
