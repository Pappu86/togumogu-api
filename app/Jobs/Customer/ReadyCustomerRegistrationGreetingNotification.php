<?php

namespace App\Jobs\Customer;

use App\Models\Child\Child;
use App\Jobs\Notification\SendEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\TemplateHelpers;

class ReadyCustomerRegistrationGreetingNotification implements ShouldQueue
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer, $template)
    {
        
        $this->customer = $customer;
        $this->template = $template;
        $this->subject = $template?->subject?:'';
        $this->to = $customer->email;
        $this->from = config('helper.mail_from_address');
        $this->from_name = config('helper.mail_from_name');
        $this->content = $template?->content?:'';
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

        $options['customer'] = [
            'id' => $this->customer->id,
            'name' => $this->customer?->name?:'',
            'email' => $this->customer?->email?:'',
            'mobile' => $this->customer?->mobile?:'',
            'date_of_birth' => $this->customer?->date_of_birth?:'',
            'gender' => $this->customer?->gender?:'',
        ];
    
        $options['child'] = Child::where('parent_id', '=', $this->customer->id)->where('is_default', 1)->first();

        $varialbes = $this->getVariables($this->template['category'], $options);
        $subject = $this->getDynamicContent($varialbes, $this->subject);
        $messageBody = $this->getDynamicContent($varialbes, $this->content);

        if(isset($to)) {
            SendEmailNotification::dispatch($to, $from, $from_name, $subject, $messageBody);
        }
    }

}
