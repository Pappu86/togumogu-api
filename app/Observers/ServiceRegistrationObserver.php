<?php

namespace App\Observers;

use App\Models\Marketing\Service;
use App\Models\Marketing\ServiceRegistration;
use App\Models\User\Customer;
use App\Jobs\Notification\SendEmailNotification;
use App\Jobs\Notification\SendMobileMessage;
use Illuminate\Support\Facades\Log;
use App\Traits\TemplateHelpers;

class ServiceRegistrationObserver
{
    use TemplateHelpers;

    /**
     * Handle the ServiceRegistration "created" event.
     *
     * @param ServiceRegistration $serviceRegistration
     * @return void
     */
    public function created(ServiceRegistration $serviceRegistration)
    {
        
        $service = Service::find($serviceRegistration?->service_id);
        $customer = Customer::find($serviceRegistration?->customer_id);

        $from = config('helper.mail_from_address');
        $from_name = config('helper.mail_from_name');
        $serviceRegistrationCustomerInfo = json_decode($serviceRegistration?->customer_info?:[]);
       
        $varialbes = [
            'customer_id' => $customer?->id?:'',
            'customer_name' => $customer?->name?: $serviceRegistrationCustomerInfo?->name?:'',
            'customer_phone' => $customer?->mobile?: $serviceRegistrationCustomerInfo?->phone?:'',
            'customer_email' => $customer?->email?: $serviceRegistrationCustomerInfo->email?:'',
            'child_name' => $serviceRegistrationCustomerInfo?->child_name?:'',
            'child_age' => $serviceRegistrationCustomerInfo?->child_age?:'',
            'child_gender' => $serviceRegistrationCustomerInfo?->child_gender?:'',
            'service_name' => $service?->title?:'',
            'provider_name' => $service?->brand?->name?:$service?->provider_email?:'',
            'provider_email' => $service?->provider_email?:'',
            'provider_phone' => $service?->provider_phone?:'',
            'service_reg_no' => $serviceRegistration?->service_reg_no?:'',
            'app_name' => $from_name,
        ];

        //Service customer activities
        //Notification to service registration 
        if(isset($customer)) {
            if($customer?->email) {
                $to = $customer->email;
                // First message send when customer registration a service 
                if(isset($to)) {
                    $subject = $this->getDynamicContent($varialbes, $this->getServiceCustomerEmailTemplate(true));
                    $messageBody = $this->getDynamicContent($varialbes,$this->getServiceCustomerEmailTemplate(false));
                    SendEmailNotification::dispatch($to, $from, $from_name, $subject, $messageBody);
                }

            }

            //It's disabled right now. Please don't remove it.
            // if($customer?->mobile) {
            //     //Send message/SMS to customer mobile           
            //     $messageData = [ 'text' => "messageBody"];
            //     SendMobileMessage::dispatch($customer?->mobile, $messageData);
            // }
        }

        //Service provider activities
        //Notification to service provider 
        if(isset($service)) {
            if($service?->provider_email) {
                $providerTo =$service?->provider_email;

                // First message send when customer registration a service 
                if(isset($providerTo)) {
                    $questions = json_decode($serviceRegistration?->questions?:[]);
                    $subject = $this->getDynamicContent($varialbes, $this->getServiceProviderEmailTemplate(true));
                    $messageBody = $this->getDynamicContent($varialbes,$this->getServiceProviderEmailTemplate(false, $questions));

                    SendEmailNotification::dispatch($providerTo, $from, $from_name, $subject, $messageBody);
                }
            }

            //It's disabled right now. Please don't remove it.
            // if($service?->provider_phone) {
            //     //Send message/SMS to customer mobile           
            //     $messageData = [ 'text' => "messageBody"];
            //     SendMobileMessage::dispatch($service?->provider_phone, $messageData);
            // }
        }

    }

    /**
     * Handle the ServiceRegistration email template.
     * @return string
     */
   private function getServiceProviderEmailTemplate($isEmailSubject, $questions = []){
    if($isEmailSubject) {
        return 'New Service Registration Request for {{service_name}}';
    } else {
        $htmlContent = <<<HTML
            <html>
            <head>
                <title>Service Registration Confirmation for {{service_name}} on ToguMogu App</title>
                <style type="text/css">
                    body {
                        font-family: Arial;
                    }
                </style>
            </head>

            <body>
                <div style="max-width: 800px; padding: 20px 0; margin: auto; font-size: 16px;line-height: 25px;">
                    <!-- Your other HTML content here -->
                    <span>Dear {{provider_name}},</span>
                    <p>We are excited to inform you that a new service registration for {{service_name}} has been received on ToguMogu. Here are the details,</p>
                    <div>User Information: </div>
                    <div>Service Registration Number: <strong>{{service_reg_no}}</strong></div>
                    <div>Customer ID: <strong>{{customer_id}}</strong></div>
                    <div>Name: <strong>{{customer_name}}</strong></div>
                    <div>Phone: <strong>{{customer_phone}}</strong></div>
                    <div>Email: <strong>{{customer_email}}</strong></div>
                    <div>Child Name: <strong>{{child_name}}</strong></div>
                    <div>Child Age: <strong>{{child_age}}</strong></div>
                    <div>Child Gender: <strong>{{child_gender}}</strong></div>
            HTML;

            if(count($questions)) {
                $htmlContent .= '<p style="margin-bottom: 5px;">Additional Questions:</p>
                <ol style="margin-top: 10px;">';

                $htmlInnerContent = '';
                foreach ($questions as $questionInfo) {
                    $htmlInnerContent .= "<li style='margin-bottom:5px'>
                                <div>$questionInfo?->question</div>
                                <div>Answer:$questionInfo?->answer</div>
                            </li>";
                }
            }

            $htmlContent .= $htmlInnerContent;
            $htmlContent .= <<<HTML
                        </ol>
                <p>Please take the necessary steps to follow up with the user and provide the requested service.Thank you for using ToguMogu to connect with parents and provide your valuable services.</p>

                <div>Best regards,</div>
                <div>{{app_name}}</div>

                </div>
                </body>
               </html>
            HTML;

            return $htmlContent;
        }
    }

    /**
     * Handle the ServiceRegistration email template.
     * @return string
     */
    private function getServiceCustomerEmailTemplate($isEmailSubject){
    if($isEmailSubject) {
        return 'Service Registration Confirmation for {{service_name}} on ToguMogu App';
    } else {
        return  <<<HTML
            <html>
                <head>
                    <title>Service Registration Confirmation for {{service_name}} on ToguMogu App</title>
                    <style type="text/css">
                        body {
                            font-family: Arial;
                        }
                    </style>
                </head>

                <body>
                    <div style="max-width: 800px; padding: 20px 0; margin: auto; font-size: 16px;line-height: 25px;">
                        <!-- Your other HTML content here -->
                        <span>Dear {{customer_name}},</span>
                        <p>We are delighted to confirm your recent service registration for {{service_name}} form {{provider_name}} on ToguMogu. Your details have been successfully submitted. The service provider maycontact you for the rest of the process.</p>

                        <div>Service Registration Number: <strong>{{service_reg_no}}</strong></div>
                        <div>Customer ID: <strong>{{customer_id}}</strong></div>

                        <p style="margin-bottom: 5px;">Service Provider's Contact Information:</p>
                        <div>- Name: <strong>{{provider_name}}</strong></div>
                        <div>- Contact Info: <strong>{{provider_phone}}, {{provider_email}}</strong></div>

                        <p>If you have any questions or need further assistance, please don't hesitate to reach out to theservice provider directly using the provided contact information.</p>

                        <p>Thank you for choosing ToguMogu to access the services you need. We wish you a greatexperience!</p>

                        <div>Best regards,</div>
                        <div>{{app_name}}</div>

                    </div>
                </body>
            </html>
        HTML;
        }
   }

}