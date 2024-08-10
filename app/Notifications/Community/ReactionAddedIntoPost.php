<?php

namespace App\Notifications\Community;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ReactionAddedIntoPost extends Notification implements ShouldQueue
{
    use Queueable;
 /**
     * who made the comment.
     *
     * @var
     */
    public $customer;

    /**
     * @var
     */
    public $post;

    /**
     * @var
     * here reaction type on the post
     */
    public $type;

    /**
     * Create a new notification instance.
     *
     * @param $customer
     * @param $post
     * @param $type
     */
    public function __construct($customer, $post, $type)
    {
        $this->customer = $customer;
        $this->post = $post;
        $this->type = $type;
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
            'post' => [
                'id' => $this->post->id,
                'title' => $this->post->title,
                'content' => $this->post->content,
                'slug' => $this->post->slug,
                'is_anonymous' => $this->post->is_anonymous,
                'customer' => [
                    'id' => $this->post?->customer?->id,
                    'name' => $this->post?->customer?->name,
                    'avatar' => $this->post?->customer?->avatar,
                    'mobile' => $this->post?->customer?->mobile,
                    'email' => $this->post?->customer?->email,
                ],
            ],
            'link' => '/post/' . $this->post->slug,
            'type' => 'community_post',
            'action' => 'post_reaction_like',
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
                'post' => [
                    'id' => $this->post->id,
                    'title' => $this->post->title,
                    'content' => $this->post->content,
                    'slug' => $this->post->slug,
                    'is_anonymous' => $this->post->is_anonymous,
                    'created_at' => $this->post->created_at,
                    'updated_at' => $this->post->updated_at,
                    'customer' => [
                        'id' => $this->post?->customer?->id,
                        'name' => $this->post?->customer?->name,
                        'avatar' => $this->post?->customer?->avatar,
                        'mobile' => $this->post?->customer?->mobile,
                        'email' => $this->post?->customer?->email,
                    ],
                ],
                'link' => '/post/' . $this->post->slug,
                'type' => 'community_post',
                'action' => 'post_reaction_like',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }
}
