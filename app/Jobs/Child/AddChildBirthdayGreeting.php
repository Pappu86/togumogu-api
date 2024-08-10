<?php

namespace App\Jobs\Child;

use App\Jobs\Child\ReadyBirthdayGreetingNotification;
use App\Jobs\Child\ReadyBirthdayGreetingPushNotification;
use App\Notifications\Schedule\SendChildBirtdayDBNotification;
use App\Models\Child\Child;
use App\Models\User\Customer;
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
use App\Traits\CommonHelpers;
use Illuminate\Support\Facades\Log;

class AddChildBirthdayGreeting implements ShouldQueue
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

        if($notificationPeriod === 'before') {
            $date = Carbon::today()->addDays($notificationDays);
            Child::whereMonth("date_of_birth", '=', $date->month)
                ->whereDay("date_of_birth", '=', $date->day)
                ->chunk(100, function($children) use ($notificationType, $template){
                    foreach($children as $child) {
                        if($child?->parent_id) {
                            $customer = Customer::where('status', 'active')->where('id', '=', $child->parent_id)->first();
                            
                            if($customer?->id) {
                                //Email notification
                                if($notificationType === 'email' && $customer?->email) {  
                                    ReadyBirthdayGreetingNotification::dispatch($customer, $template, $child);
                                }

                                //Push notification notification
                                if($notificationType === 'push_notification') {
                                    $commentHelpers = new CommonHelpers;

                                    //Send push notifications 
                                    if($commentHelpers?->isSettingEnabled($customer, 'push_notification', 'all')){
                                        $fcmTokens = null;
                                        if($customer?->id) {
                                            $fcmTokens = DB::table('customer_devices')->where('customer_id', $customer->id)->pluck('token')->all();
                                            if(count($fcmTokens)){
                                                ReadyBirthdayGreetingPushNotification::dispatch($customer, $template, $child, $fcmTokens);
                                            }
                                        }
                                    }
                                }

                                //Get data for Mobile SMS or Database notification
                                if($notificationType === 'sms' || $notificationType === 'database') {
                                    $options['customer'] = [
                                        'id' => $customer->id,
                                        'name' => $customer?->name?:'',
                                        'email' => $customer?->email?:'',
                                        'mobile' => $customer?->mobile?:'',
                                        'date_of_birth' => $customer?->date_of_birth?:'',
                                        'gender' => $customer?->gender?:'',
                                    ];
                                
                                    $options['child'] = $child;
                                    $varialbes = $this->getVariables($template['category'], $options);
                                    $messageBody = $this->getDynamicContent($varialbes, $template?->content?:'');
                                };

                                //Mobile SMS notification
                                if($notificationType === 'sms' && $customer?->mobile) {  
                                    $messageData = [ 'text' => $messageBody];
                                    SendMobileMessage::dispatch($customer?->mobile, $messageData);
                                }

                                //Send database notifications 
                                if($notificationType === 'database') {
                                    $dynamicSubject = $this->getDynamicContent($varialbes, $template?->subject?:'');
                                    $template['messageBody'] = $messageBody?:'';
                                    $template['dynamic_subject'] = $dynamicSubject?:'';
                                    $customer?->notify((new SendChildBirtdayDBNotification($customer, $template, $child)));                   
                                }
                            }
                        }                  
                    }
            });
        }

    }

}
