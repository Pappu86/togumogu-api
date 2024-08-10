<?php

namespace App\Notifications\Community;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ReactionAddedIntoComment extends Notification implements ShouldQueue
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
     * here on the comment
     */
    public $comment;

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
     * @param $comment
     * @param $type
     */
    public function __construct($customer, $post, $comment, $type)
    {
        $this->customer = $customer;
        $this->post = $post;
        $this->comment = $comment;
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
            'comment' => [
                'id' => $this->comment->id,
                'title' => $this->comment->title,
                'content' => $this->comment->content,
                'commentable_id' => $this->comment->commentable_id,
                'commentable_type' => $this->comment->commentable_type,
                'created_at' => $this->comment->created_at,
                'updated_at' => $this->comment->updated_at,
                'customer' => [
                    'id' => $this->comment?->customer?->id,
                    'name' => $this->comment?->customer?->name,
                    'avatar' => $this->comment?->customer?->avatar,
                    'mobile' => $this->comment?->customer?->mobile,
                    'email' => $this->comment?->customer?->email,
                ],
            ],
            'link' => '/post/' . $this->post->slug,
            'type' => 'community_post',
            'action' => 'post_comment_reaction_like',
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
                'comment' => [
                    'id' => $this->comment->id,
                    'title' => $this->comment->title,
                    'content' => $this->comment->content,
                    'commentable_id' => $this->comment->commentable_id,
                    'commentable_type' => $this->comment->commentable_type,
                    'created_at' => $this->comment->created_at,
                    'updated_at' => $this->comment->updated_at,
                    'customer' => [
                        'id' => $this->comment?->customer?->id,
                        'name' => $this->comment?->customer?->name,
                        'avatar' => $this->comment?->customer?->avatar,
                        'mobile' => $this->comment?->customer?->mobile,
                        'email' => $this->comment?->customer?->email,
                    ],
                ],
                'link' => '/post/' . $this->post->slug,
                'type' => 'community_post',
                'action' => 'post_comment_reaction_like',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }
}
