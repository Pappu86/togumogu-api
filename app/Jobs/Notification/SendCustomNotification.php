<?php

namespace App\Jobs\Notification;

use Illuminate\Bus\Queueable;
use App\Jobs\Notification\SendPushNotification;
use App\Jobs\Notification\SendEmailNotification;
use App\Jobs\Notification\SendMobileMessage;
use App\Models\Message\CustomNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Child\Child;
use App\Models\User\Customer;
use App\Models\Message\Template;
use App\Traits\TemplateHelpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendCustomNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TemplateHelpers;

    /**
     * @var
     */
    protected $notification;
    protected $notification_type;
    protected $template;
    protected $template_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification)
    {

        $this->notification = $notification;
        $this->notification_type = $notification['type']?:null;
        $this->template_id = $notification['template_id']?:null;
        
        if(isset($this->template_id)) {
            $this->template = Template::with('translations')->where('id', '=', $this->template_id)->first();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $query_list = $this?->notification['target']?json_decode($this?->notification['target'], true):[];
        $child_age_range = null;
        $child_age_condition = null;
        $child_expecting_range = null;
        $child_expecting_Condition = null;
        $user_gender = null;
        $user_gender_condition = null;

        // Start split target query
        foreach ($query_list as $query) {
            if(isset($query) && $query['key'] === 'child_age') {
                $child_age_range =  $query['firstValue'].'-'.$query['lastValue'].'-m-parent';
                $child_age_condition =  $query['condition'];
            }

            if(isset($query) && $query['key'] === 'child_expecting') {
                $child_expecting_range = $query['firstValue'].'-'.$query['lastValue'].'-m-expecting';
                $child_expecting_Condition =  $query['condition'];
            }

            if(isset($query) && $query['key'] === 'user_gender') {
                $user_gender = $query['value'];
                $user_gender_condition =  $query['condition'];
            }
        }

        $child_age_date = $this->getDateRange($child_age_range);
        $child_expecting_date = $this->getDateRange($child_expecting_range);
        $child_ids = [];
        $child_parent_ids = [];

        if($child_expecting_date && $child_expecting_date['parent_status'] === 'expecting') {
            $children = Child::where('parent_status', '=', 'expecting');
  
            if($child_expecting_Condition === 'not_between') {
                $children = $children->whereNotBetween('expecting_date', $child_expecting_date['range']);
            } else {
                $children = $children->whereBetween('expecting_date', $child_expecting_date['range']);
            }

            $child_ids = $children?->pluck('id');
            $child_parent_ids = $children?->pluck('parent_id');  
        }
        
        if($child_age_date && $child_age_date['parent_status'] === 'parent') {
            $children = Child::where('parent_status', '=', 'parent');
            
            if($child_age_condition === 'not_between') {
                $children = $children->whereNotBetween('date_of_birth', $child_age_date['range']);
            } else {
                $children = $children->whereBetween('date_of_birth', $child_age_date['range']);
            }

            $child_ids = array_unique([...$children?->pluck('id'), ...$child_ids]);;
            $child_parent_ids = array_unique([...$children?->pluck('parent_id'), ...$child_parent_ids]);
        }

        $customers = Customer::with('device')
                        ->whereIn('id', $child_parent_ids);

        if(isset($user_gender) && $user_gender_condition === 'nin') {
            $customers =  $customers->whereNotIn('gender', $user_gender);
        } else if(isset($user_gender)) {
            $customers =  $customers->whereIn('gender', $user_gender);
        }

        if($this->notification_type === 'push_notification') {
            $customers = $customers->whereHas('device', function ($device_info) {
                $device_info->where('token', '!=', null);
            });
        }

        if($this->notification_type === 'email') {
            $customers = $customers->where('email', '!=', null);
        }

        if($this->notification_type === 'sms') {
            $customers = $customers->where('mobile', '!=', null);
        }

        // Will be update notification statistic
        $notificationType = $this->notification_type;
        $template = $this->template;
        $chunkSize = !($template?->is_dynamic_value)? 99999:30; // Number of records per chunk
        $delayInSeconds = 3; // Delay in seconds between chunks

        Log::info("customers", [ "total" => $customers->count() ]);

        if($customers->count()) {
            $customers->chunk($chunkSize, function($customersList) use ($child_ids, $notificationType, $template, $delayInSeconds){

                //If template does not send with dynamic value
                if(!($template?->is_dynamic_value)) {
               
                    $emails = $customersList->pluck('email')->filter(function ($email) {
                        return filter_var($email, FILTER_VALIDATE_EMAIL);
                    })->values()->toArray();

                    // $emails = $customersList->pluck('email')->toArray();
                    $senderEmail = config('helper.mail_from_address');
                    $senderName = config('helper.mail_from_name');
                    $from = "$senderName <$senderEmail>";
                    $this->sendBulkEmailNotification($from, $emails, $template);
               
                } else  {
                    
                    //If template does send with dynamic value
                    foreach($customersList as $customer) {
                        if($customer['id']) {

                            if(count($child_ids)) {
                                $options['child'] = Child::where('parent_id', '=', $customer['id'])->whereIn('id', $child_ids)->first();
                            }
                            
                            if($notificationType === 'push_notification') {
                                $fcm_tokens = DB::table('customer_devices')->where('customer_id', $customer['id'])->pluck('token')->all();
                                if(count($fcm_tokens)) {
                                    $this->sendPushNotificationToFirebase($customer, $fcm_tokens, $options);
                                }
                            }  

                            //Email notification
                            if($notificationType === 'email' && $customer?->email) {  
                                $to = $customer?->email;
                                $from = config('helper.mail_from_address');
                                $from_name = config('helper.mail_from_name');

                                if(isset($to)) {
                                    $emailMessageData = $this->getNotificationData($customer, $options['child'], $template);
                                    $subject = $emailMessageData['subject'];
                                    $messageBody = $emailMessageData['messageBody'];
                                    SendEmailNotification::dispatch($to, $from, $from_name, $subject, $messageBody);
                                }
                            }
                        
                            //Send message/SMS to customer mobile           
                            if($notificationType === 'sms' && $customer?->mobile) {
                                $smsMessageData = $this->getNotificationData($customer, $options['child'], $template);
                                $messageData = [ 'text' => $smsMessageData['messageBody']];
                                SendMobileMessage::dispatch($customer?->mobile, $messageData);
                            }

                            //Send database notifications 
                            // if($notificationType === 'database') {
                            //     $dynamicSubject = $this->getDynamicContent($varialbes, $template?->subject?:'');
                            //     $template['messageBody'] = $messageBody?:'';
                            //     $template['dynamic_subject'] = $dynamicSubject?:'';
                            //     $customer?->notify((new SendCustomerRegistrationGreetingDBNotification($customer, $template)));                   
                            // }  

                        }
                        
                        // Introduce a delay after processing each item
                        sleep(1); // Delay for 1 second
                    }

                }

                // Introduce a delay between chunks
                sleep($delayInSeconds);
            });
        }

        // Process status change "Custom notification" table after send the notification
        CustomNotification::where('id','=', $this->notification['id'])
            ->update(['process_status'=> 'completed']);

    }

     /**
     * Execute the result by notification target query.
     *
     * @return void
     */
    private function sendPushNotificationToFirebase($customer, $fcm_tokens, $options)
    {

        $options['customer'] = $customer;
        $varialbes = $this->getVariables($this->template['category'], $options);
        $title = $this->getDynamicContent($varialbes, $this->template['subject']);
        $body = $this->getDynamicContent($varialbes, $this->template['content']);
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
            "title"=> $title,
            "body"=> $body,
            "image"=> $image,
            "sound"=> $sound,
            "fcm_tokens"=> $fcm_tokens,
        ]);
        
        SendPushNotification::dispatch($notification_data);
    
    }

    private function getDateRange($dateStr)
    {
        //Check 
        if(!$dateStr) return false;

        $range = explode("-",$dateStr);
        $range_type = count($range)?$range[2]:null;
        $parent_status = count($range)?$range[3]:null;
        $start_date = '';
        $end_date = '';

        if($range_type === 'y') {
            $start_date = Carbon::now()->subYear($range[0])->toDateTimeString();
            $end_date = Carbon::now()->subYear($range[1])->toDateTimeString();
        } else if($range_type === 'm') {
            if($parent_status === 'expecting' ) {
                $start_date = Carbon::now()->addMonth($range[1])->toDateTimeString();
                $end_date = Carbon::now()->addMonth($range[0])->toDateTimeString();
            } else {
                $start_date = Carbon::now()->subMonth($range[0])->toDateTimeString();
                $end_date = Carbon::now()->subMonth($range[1])->toDateTimeString();    
            }
        }
     
        return [
            "parent_status" => $parent_status?:'parent',
            "range" => [$end_date, $start_date]
        ];
    }

    private function getNotificationData($customer, $child, $template)
    {
     
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
        $subject = $this->getDynamicContent($varialbes, $template?->subject);
        $messageBody = $this->getDynamicContent($varialbes, $template?->content?:'');
       
        return [
            "subject" => $subject,
            "messageBody" => $messageBody,
        ];
    }

    private function sendBulkEmailNotification($from, $to, $template)
    {
        
        $endpoint = config('services.mailgun.endpoint') . '/' . config('services.mailgun.domain') . '/messages';
        $secret = config('services.mailgun.secret');

        $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode('api:' . $secret),
            ])
            ->asForm()
            ->post($endpoint, [
                'from' => $from,
                'to' => $from,
                'bcc' => implode(',', $to),
                'subject' => $template?->subject?:'Welcome',
                'html' => $template?->content?:"Congratulation for join Togumogu!",
            ]);

        // Check the response
        if ($response->successful()) {
            // Successful request
            $responseData = $response->json();
            Log::info('Bulk email send Successful');
        } else {
            // Failed request
            $errorMessage = $response->body();
            Log::info('Bulk email send failed:', [ "response" => $errorMessage ]);
        }
    }
}
