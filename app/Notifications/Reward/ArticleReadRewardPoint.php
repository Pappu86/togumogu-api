<?php

namespace App\Notifications\Reward;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ArticleReadRewardPoint extends Notification implements ShouldQueue
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
    public $article;

    /**
     * @var
     */
    public $reward;
    
    /**
     * Create a new notification instance.
     *
     * @param $customer
     * @param $article 
     * @param $reward 
     */

    public function __construct($customer, $article, $reward)
    {
        $this->customer = $customer?:'';
        $this->article = $article?:'';
        $this->reward = $reward?:'';
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
                'id' => $this->customer?->id?:'',
                'name' => $this->customer?->name?:'',
                'avatar' => $this->customer?->avatar?:'',
                'mobile' => $this->customer?->mobile?:'',
                'email' => $this->customer?->email?:'',
            ],
            'article' => [
                'id' => $this->article?->id?:'',
                'title' => $this->article?->title?:'',
                'excerpt' => Str::substr($this->article?->excerpt?:'', 0, 50),
                'slug' => $this->article?->slug?:'',
                'created_at' => $this->article?->created_at?:'',
                'updated_at' => $this->article?->updated_at?:'',
            ],
            'reward' => $this->reward?:null,
            'link' => '/articles/' . $this->article?->slug,
            'type' => 'reward',
            'action' => 'article_read_reward_added',
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
                    'id' => $this->customer?->id?:'',
                    'name' => $this->customer?->name?:'',
                    'avatar' => $this->customer?->avatar?:'',
                    'mobile' => $this->customer?->mobile?:'',
                    'email' => $this->customer?->email?:'',
                ],
                'article' => [
                    'id' => $this->article?->id?:'',
                    'title' => $this->article?->title?:'',
                    'excerpt' => Str::substr($this->article?->excerpt?:'', 0, 50),
                    'slug' => $this->article?->slug?:'',
                    'created_at' => $this->article?->created_at?:'',
                    'updated_at' => $this->article?->updated_at?:'',
                ],
                'reward' => $this->reward?:null,
                'link' => '/articles/' . $this->article?->slug,
                'type' => 'reward',
                'action' => 'article_read_reward_added',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }



}
