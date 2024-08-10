<?php

namespace App\Notifications\Schedule;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SendCustomerRegistrationGreetingDBNotification extends Notification implements ShouldQueue
{
    use Queueable;
 /**
     * who made the Birthday notification.
     *
     * @var
     */
    public $customer;

    /**
     * @var
     */
    public $template;

    /**
     * Create a new notification instance.
     *
     * @param $customer
     * @param $template
     */
    public function __construct($customer, $template)
    {
        $this->customer = $customer;
        $this->template = $template;
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
                'parent_type' => $this->customer->parent_type,
                'gender' => $this->customer->gender,
                'date_of_birth' => $this->customer->date_of_birth,
            ],
            'template' => [
                'id' => $this->template->id,
                'subject' => $this->template?->dynamic_subject?:$this->template->subject,
                'messageBody' => $this->template->messageBody?:$this->template->content,
                'data' => $this->template?->ad_custom_data?:[],
            ],
            'link' => '/customer/'. $this->customer->id,
            'type' => 'customer_birthday',
            'action' => 'customer_birthday',
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
                    'parent_type' => $this->customer->parent_type,
                    'gender' => $this->customer->gender,
                    'date_of_birth' => $this->customer->date_of_birth,
                ],
                'template' => [
                    'id' => $this->template->id,
                    'subject' => $this->template?->dynamic_subject?:$this->template->subject,
                    'messageBody' => $this->template->messageBody?:$this->template->content,
                    'data' => $this->template?->ad_custom_data?:[],
                ],
                'link' => '/customer/'. $this->customer->id,
                'type' => 'customer_birthday_greeting',
                'action' => 'customer_birthday',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }
}
