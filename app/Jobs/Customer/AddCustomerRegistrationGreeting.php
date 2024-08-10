<?php

namespace App\Jobs\Customer;

use App\Jobs\Customer\ReadyCustomerRegistrationGreetingNotification;
use App\Notifications\Schedule\SendCustomerRegistrationGreetingDBNotification;
use App\Models\Message\Template;
use App\Traits\NotificationHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\TemplateHelpers;
use App\Jobs\Notification\SendMobileMessage;
use App\Models\User\Customer;
use App\Traits\CommonHelpers;

class AddCustomerRegistrationGreeting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue,
     Queueable, SerializesModels, NotificationHelpers, TemplateHelpers;

    /**
     * @var
     */
    protected $notification;
    protected $templateId;
    protected $template;

    public function __construct($notification)
    {
        $this->notification = $notification?:'';
        $this->notification = $notification?:'';
        $this->templateId = $notification->template_id?:null;
        
        if(isset($this->templateId)) {
            $this->template = Template::with('translations')->where('id', '=', $this->templateId)->first();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notificationPeriod = $this->notification?->period?:null;
        $notificationDays = $this->notification?->days?:0;
        $notificationType = $this->notification?->type?:null;
        $template = $this->template?:'';

        if($notificationPeriod === 'after') {
            $date = Carbon::today()->subDays($notificationDays);

            Customer::where('status', 'active')
                ->whereDate('created_at', $date)
                ->chunk(100, function($customers) use ($notificationType, $template){
                    foreach($customers as $customer) {
                        if(isset($customer) && $customer?->id) {
                            
                            //Email notification
                            if($notificationType === 'email' && $customer?->email) {  
                                ReadyCustomerRegistrationGreetingNotification::dispatch($customer, $template);
                            }
    
                            //Push notification
                            if($notificationType === 'push_notification') {
                                $commentHelpers = new CommonHelpers;
    
                                //Send push notifications 
                                if($commentHelpers?->isSettingEnabled($customer, 'push_notification', 'all')){ 
                                    $fcmTokens = DB::table('customer_devices')->where('customer_id', $customer->id)->pluck('token')->all();
                                    if(count($fcmTokens)){
                                        ReadyCustomerRegistrationGreetingPushNotification::dispatch($customer, $template, $fcmTokens);
                                    }
                                }
                            }
                        
                            //Mobile SMS or Database notification
                            if($notificationType === 'sms' || $notificationType === 'database') {
                                $options['customer'] = [
                                    'id' => $customer->id,
                                    'name' => $customer?->name?:'',
                                    'email' => $customer?->email?:'',
                                    'mobile' => $customer?->mobile?:'',
                                    'date_of_birth' => $customer?->date_of_birth?:'',
                                    'gender' => $customer?->gender?:'',
                                ];
                                
                                $options['child'] = null;
    
                                $varialbes = $this->getVariables($template['category'], $options);
                                $messageBody = $this->getDynamicContent($varialbes, $template?->content?:'');
                            };
    
                            //Send message/SMS to customer mobile           
                            if($notificationType === 'sms' && $customer?->mobile) {
                                $messageData = [ 'text' => $messageBody];
                                SendMobileMessage::dispatch($customer?->mobile, $messageData);
                            }
    
                            //Send database notifications 
                            if($notificationType === 'database') {
                                $dynamicSubject = $this->getDynamicContent($varialbes, $template?->subject?:'');
                                $template['messageBody'] = $messageBody?:'';
                                $template['dynamic_subject'] = $dynamicSubject?:'';
                                $customer?->notify((new SendCustomerRegistrationGreetingDBNotification($customer, $template)));                   
                            }  
                        }               
                    }
                });
            }

    }

}
