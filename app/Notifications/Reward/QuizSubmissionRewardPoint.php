<?php

namespace App\Notifications\Reward;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class QuizSubmissionRewardPoint extends Notification implements ShouldQueue
{
    use Queueable;
 /**
     * who made the quiz submission.
     *
     * @var
     */
    public $customer;

    /**
     * @var
     */
    public $quizResult;

    /**
     * @var
     */
    public $reward;
    
    /**
     * Create a new notification instance.
     *
     * @param $customer
     * @param $quizResult 
     * @param $reward 
     */

    public function __construct($customer, $quizResult, $reward)
    {
        $this->customer = $customer?:'';
        $this->quizResult = $quizResult?:'';
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
            'quiz_result' => [
                "id" => $this->quizResult?->id,
                "customer_id" => $this->quizResult?->customer_id,
                "name" => $this->quizResult?->name,
                "email" => $this->quizResult?->email,
                "mobile" => $this->quizResult?->mobile,
                "referral_url" => $this->quizResult?->referral_url,
                "quiz_score" => $this->quizResult?->quiz_score,
                "answerer_score" => $this->quizResult?->answerer_score,
                "view_time" => $this->quizResult?->view_time,
                "submit_time" => $this->quizResult?->submit_time,
                "taken_time" => $this->quizResult?->taken_time,
            ],
            'reward' => $this->reward?:'',
            'link' => $this->quizResult?->referral_url,
            'type' => 'reward',
            'action' => 'quiz_submission_reward_added',
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
                'quiz_result' => [
                    "id" => $this->quizResult?->id,
                    "customer_id" => $this->quizResult?->customer_id,
                    "name" => $this->quizResult?->name,
                    "email" => $this->quizResult?->email,
                    "mobile" => $this->quizResult?->mobile,
                    "referral_url" => $this->quizResult?->referral_url,
                    "quiz_score" => $this->quizResult?->quiz_score,
                    "answerer_score" => $this->quizResult?->answerer_score,
                    "view_time" => $this->quizResult?->view_time,
                    "submit_time" => $this->quizResult?->submit_time,
                    "taken_time" => $this->quizResult?->taken_time,
                ],
                'reward' => $this->reward?:'',
                'link' => $this->quizResult?->referral_url,
                'type' => 'reward',
                'action' => 'quiz_submission_reward_added',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }
}
