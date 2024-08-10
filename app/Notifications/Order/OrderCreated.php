<?php

namespace App\Notifications\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OrderCreated extends Notification implements ShouldQueue
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
                'id' => $this->order?->id,
                'invoice_no' => $this->order?->invoice_no,
                'order_no' => $this->order?->order_no,
                'payment_method' => $this->order?->payment_method,
                'platform' => $this->order?->platform,
                'send_as_gift' => $this->order?->send_as_gift,
                'shipping_cost' => $this->order?->shipping_cost,
                'shipping_method' => $this->order?->shipping_method,
                'special_discount' => $this->order?->special_discount,
                'total_amount' => $this->order?->total_amount,
                'total_quantity' => $this->order?->total_quantity,
                'total_save_amount' => $this->order?->total_save_amount,
                'created_at' => $this->order->created_at,
                'updated_at' => $this->order->updated_at,
                'customer' => [
                    'id' => $this->order?->customer?->id,
                    'name' => $this->order?->customer?->name,
                    'avatar' => $this->order?->customer?->avatar,
                    'mobile' => $this->order?->customer?->mobile,
                    'email' => $this->order?->customer?->email,
                ],
                'order_status' => [
                    'id' => $this->order?->orderStatus?->id,
                    'name' => $this->order?->orderStatus?->name,
                    'translations' => $this->order?->orderStatus?->translations,
                    'code' => $this->order?->orderStatus?->code,
                    'color' => $this->order?->orderStatus?->color,
                ],
                'payment_status' => [
                    'id' => $this->order?->paymentStatus?->id,
                    'name' => $this->order?->paymentStatus?->name,
                    'translations' => $this->order?->paymentStatus?->translations,
                    'code' => $this->order?->paymentStatus?->code,
                    'color' => $this->order?->paymentStatus?->color,
                ]
            ],
            'link' => '/orders/' . $this->order?->id,
            'type' => 'order',
            'action' => 'order_created',
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
                    'id' => $this->order?->id,
                    'invoice_no' => $this->order?->invoice_no,
                    'order_no' => $this->order?->order_no,
                    'payment_method' => $this->order?->payment_method,
                    'platform' => $this->order?->platform,
                    'send_as_gift' => $this->order?->send_as_gift,
                    'shipping_cost' => $this->order?->shipping_cost,
                    'shipping_method' => $this->order?->shipping_method,
                    'special_discount' => $this->order?->special_discount,
                    'total_amount' => $this->order?->total_amount,
                    'total_quantity' => $this->order?->total_quantity,
                    'total_save_amount' => $this->order?->total_save_amount,
                    'created_at' => $this->order->created_at,
                    'updated_at' => $this->order->updated_at,
                    'customer' => [
                        'id' => $this->order?->customer?->id,
                        'name' => $this->order?->customer?->name,
                        'avatar' => $this->order?->customer?->avatar,
                        'mobile' => $this->order?->customer?->mobile,
                        'email' => $this->order?->customer?->email,
                    ],
                    'order_status' => [
                        'id' => $this->order?->orderStatus?->id,
                        'name' => $this->order?->orderStatus?->name,
                        'description' => $this->order?->orderStatus?->description,
                        'translations' => $this->order?->orderStatus?->translations,
                        'code' => $this->order?->orderStatus?->code,
                        'color' => $this->order?->orderStatus?->color,
                    ],
                    'payment_status' => [
                        'id' => $this->order?->paymentStatus?->id,
                        'name' => $this->order?->paymentStatus?->name,
                        'description' => $this->order?->paymentStatus?->description,
                        'translations' => $this->order?->paymentStatus?->translations,
                        'code' => $this->order?->paymentStatus?->code,
                        'color' => $this->order?->paymentStatus?->color,
                    ]
                ],
                'link' => '/orders/' . $this->order?->id,
                'type' => 'order',
                'action' => 'order_created',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }



}
