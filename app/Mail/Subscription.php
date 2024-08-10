<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Subscription extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $body;

    /**
     * Create a new message instance.
     *
     * @param $body
     */
    public function __construct($body)
    {
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->body['subject'];
        return $this->subject($subject)->markdown('emails.subscription');
    }
    
}
