<?php

namespace App\Notifications\Reward;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class VideoWatchRewardPoint extends Notification implements ShouldQueue
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
    public $video;

    /**
     * @var
     */
    public $reward;
    
    /**
     * Create a new notification instance.
     *
     * @param $customer
     * @param $video 
     * @param $reward 
     */

    public function __construct($customer, $video, $reward)
    {
        $this->customer = $customer;
        $this->video = $video;
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
            'video' => [
                'id' => $this->video->id,
                'title' => $this->video->title,
                'slug' => $this->video->slug,
                'excerpt' => Str::substr($this->video->excerpt, 0, 50),
                'created_at' => $this->video->created_at,
                'updated_at' => $this->video->updated_at,
            ],
            'reward' => $this->reward,
            'link' => '/videos/' . $this->video?->slug,
            'type' => 'reward',
            'action' => 'video_watch_reward_added',
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
                'article' => [
                    'id' => $this->video->id,
                    'title' => $this->video->title,
                    'slug' => $this->video->slug,
                    'excerpt' => Str::substr($this->video->excerpt, 0, 50),
                    'created_at' => $this->video->created_at,
                    'updated_at' => $this->video->updated_at,
                ],
                'reward' => $this->reward,
                'link' => '/videos/' . $this->video?->slug,
                'type' => 'reward',
                'action' => 'video_watch_reward_added',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }



}
