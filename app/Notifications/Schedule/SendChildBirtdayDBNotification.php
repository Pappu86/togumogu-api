<?php

namespace App\Notifications\Schedule;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SendChildBirtdayDBNotification extends Notification implements ShouldQueue
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
     * @var
     */
    public $child;

    /**
     * Create a new notification instance.
     *
     * @param $customer
     * @param $template
     * @param $child
     */
    public function __construct($customer, $template, $child)
    {
        $this->customer = $customer;
        $this->template = $template;
        $this->child = $child;
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
            'template' => [
                'id' => $this->template->id,
                'subject' => $this->template?->dynamic_subject?:$this->template->subject,
                'messageBody' => $this->template->messageBody?:$this->template->content,
                'data' => $this->template?->ad_custom_data?:[],
            ],
            'child' => [
                'id' => $this->child?->id,
                'name' => $this->child?->name,
                'date_of_birth' => $this->child?->date_of_birth,
                'expecting_date' => $this->child?->expecting_date,
                'gender' => $this->child?->gender,
                'religion' => $this->child?->religion,
                'avatar' => $this->child?->avatar,
                'parent_status' => $this->child?->parent_status,
                'parent_id' => $this->child?->parent_id,
                'created_at' => $this->child?->created_at,
                'updated_at' => $this->child?->updated_at,
            ],
            'link' => '/customer/'. $this->child->parent_id. '/child/' . $this->child->id,
            'type' => 'child_birthday_greeting',
            'action' => 'child_birthday_greeting',
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
                'template' => [
                    'id' => $this->template->id,
                    'subject' => $this->template?->dynamic_subject?:$this->template->subject,
                    'messageBody' => $this->template->messageBody?:$this->template->content,
                    'data' => $this->template?->ad_custom_data?:[],
                ],
                'child' => [
                    'id' => $this->child?->id,
                    'name' => $this->child?->name,
                    'date_of_birth' => $this->child?->date_of_birth,
                    'expecting_date' => $this->child?->expecting_date,
                    'gender' => $this->child?->gender,
                    'religion' => $this->child?->religion,
                    'avatar' => $this->child?->avatar,
                    'parent_status' => $this->child?->parent_status,
                    'parent_id' => $this->child?->parent_id,
                    'created_at' => $this->child?->created_at,
                    'updated_at' => $this->child?->updated_at,
                ],
                'link' => '/customer/'. $this->child->parent_id. '/child/' . $this->child->id,
                'type' => 'child_birthday',
                'action' => 'child_birthday',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }
}
