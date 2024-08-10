<?php

namespace App\Jobs\Child;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\TemplateHelpers;
use App\Jobs\Notification\SendPushNotification;

class ReadyBirthdayGreetingPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TemplateHelpers;

    /**
     * @var
     */
    protected $customer;
    protected $template;
    protected $subject;
    protected $content;
    protected $child;
    protected $fcmTokens;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer, $template, $child, $fcmTokens)
    {
        
        $this->customer = $customer;
        $this->template = $template;
        $this->subject = $template?->subject?:'';
        $this->content = $template?->content?:'';
        $this->child = $child?:'';
        $this->fcmTokens = $fcmTokens?:null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if(isset($this->fcmTokens)){
            
            $options['customer'] = [
                'id' => $this->customer->id,
                'name' => $this->customer?->name?:'',
                'email' => $this->customer?->email?:'',
                'mobile' => $this->customer?->mobile?:'',
                'date_of_birth' => $this->customer?->date_of_birth?:'',
                'gender' => $this->customer?->gender?:'',
            ];
        
            $options['child'] = $this->child?:null;
        
            $varialbes = $this->getVariables($this->template['category'], $options);
            $subject = $this->getDynamicContent($varialbes, $this->subject);
            $messageBody = $this->getDynamicContent($varialbes, $this->content);

            $image = $this->template?->image?:'';
            $sound = $this->template?->sound === 'disabled'?false:true;
            $ad_custom_data = json_decode($this->template['ad_custom_data'], true);
            $data = [];
    
            // Data maping
            if(count($ad_custom_data)) {
                foreach($ad_custom_data as $item) {
                    $data[$item['key']] = $item['value'];
                 }
            }

            $notification_data = collect([
                "data"=> $data,
                "title"=> $subject,
                "body"=> $messageBody,
                "image"=> $image,
                "sound"=> $sound,
                "fcm_tokens"=> $this->fcmTokens,
            ]);

            SendPushNotification::dispatch($notification_data);
        }
    }

}
