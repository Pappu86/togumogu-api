<?php

namespace App\Jobs\Child;

use App\Jobs\Notification\SendEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\TemplateHelpers;

class ReadyBirthdayGreetingNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TemplateHelpers;

    /**
     * @var
     */
    protected $customer;
    protected $template;
    protected $subject;
    protected $to;
    protected $from;
    protected $from_name;
    protected $content;
    protected $child;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer, $template, $child)
    {
        $this->customer = $customer;
        $this->template = $template;
        $this->subject = $template?->subject?:'';
        $this->to = $customer->email;
        $this->from = config('helper.mail_from_address');
        $this->from_name = config('helper.mail_from_name');
        $this->content = $template?->content?:'';
        $this->child = $child?:'';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $to = $this->customer->email;
        $from = $this->from;
        $from_name = $this->from_name;

        $options['customer'] = [
            'id' => $this->customer->id,
            'name' => $this->customer?->name?:'',
            'email' => $this->customer?->email?:'',
            'mobile' => $this->customer?->mobile?:'',
            'date_of_birth' => $this->customer?->date_of_birth?:'',
            'gender' => $this->customer?->gender?:'',
        ];
    
        $options['child'] = $this->child;
    
        $varialbes = $this->getVariables($this->template['category'], $options);
        $subject = $this->getDynamicContent($varialbes, $this->subject);
        $messageBody = $this->getDynamicContent($varialbes, $this->content);

        if(isset($to)) {
            SendEmailNotification::dispatch($to, $from, $from_name, $subject, $messageBody);
        }
    }

}
