<?php

namespace App\Jobs\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $to;
    protected $from_email;
    protected $from_name;
    protected $subject;
    protected $messageBody;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $from, $from_name, $subject, $messageBody)
    {
        
        $this->to = $to;
        $this->from_email = $from;
        $this->from_name = $from_name;
        $this->subject = $subject?:'';
        $this->messageBody = $messageBody?:'';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Mail::send('emails.dynamic', ['message_body' => $this->messageBody], function ($message) {
        //     $message->subject($this->subject);
        //     $message->from($this->from, $this->from_name);
        //     $message->to($this->to);
        // });

        $endpoint = config('services.mailgun.endpoint') . '/' . config('services.mailgun.domain') . '/messages';
        $secret = config('services.mailgun.secret');

        $senderName = $this->from_name;
        $senderEmail = $this->from_email;
        $from = "$senderName <$senderEmail>";

        $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode('api:' . $secret),
            ])
            ->asForm()
            ->post($endpoint, [
                'from' => $from,
                'to' => $this->to,
                'subject' => $this->subject,
                'html' => $this->messageBody,
            ]);

        // Check the response
        if ($response->successful()) {
            // Successful request
            $responseData = $response->json();
        } else {
            // Failed request
            $errorMessage = $response->body();
            Log::info('Email send failed:', [ "response" => $errorMessage ]);
        }


    }

}
