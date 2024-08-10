<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $body;

    /**
     * @var
     */
    public $templateType;

    /**
     * Create a new message instance.
     *
     * @param $body
     */
    public function __construct($body, $templateType)
    {
        $this->body = $body;
        $this->templateType = $templateType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->body['subject'];   

        if($this->templateType === 'status_changed') {
            return $this->subject($subject)->markdown('emails.orderStatusChanged');
        } else {
            return $this->subject($subject)->markdown('emails.order');
        } 
    }
    
}
