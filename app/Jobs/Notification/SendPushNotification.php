<?php

namespace App\Jobs\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $notification;
    protected $fcm_tokens;
    protected $title;
    protected $body;
    protected $image;
    protected $sound;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        $notification = json_decode($notification);
        $this->notification = $notification;
        $this->fcm_tokens = $notification?->fcm_tokens?:[];
        $this->title = $notification?->title?:'';
        $this->body = $notification?->body?:'';
        // $this->image = $notification?->image?:'';
        // $this->sound = $notification?->sound;
        $this->data = $notification?->data?:[];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = config('helper.firebase_server_key');
        if(count($this->fcm_tokens) > 0) {
            
            $data = [
                "registration_ids" => $this->fcm_tokens,
                "notification" => [
                    "title" => $this->title,
                    "body" => $this->body,
                    // "image" => $this->image,
                    // "default_sound" => $this->sound,
                ],
                "data"=> $this->data
            ];
            
            $encodedData = json_encode($data);

            $headers = [
                'Authorization:key=' . $serverKey,
                'Content-Type: application/json',
            ];
        
            $ch = curl_init();
        
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

            // Execute post
            $result = curl_exec($ch);

            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
                Log::error('Curl failed: ' . curl_error($ch));
            }        

            // Close connection
            curl_close($ch);
        }

    }

}
