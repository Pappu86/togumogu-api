<?php

namespace App\Observers;

use App\Models\Quiz\QuizResult;
use App\Models\User\Customer;
use App\Jobs\Notification\SendEmailNotification;
use App\Jobs\Notification\SendMobileMessage;
use Illuminate\Support\Facades\Log;
use App\Traits\TemplateHelpers;

class QuizResultObserver
{
    use TemplateHelpers;

    /**
     * Handle the QuizResult "updated" event.
     *
     * @param QuizResult $quizResult
     * @return void
     */
    public function updated(QuizResult $quizResult)
    {
        $customer = Customer::find($quizResult?->customer_id);
        
        $from = config('helper.mail_from_address');
        $from_name = config('helper.mail_from_name');

        $varialbes = [
            'customer_id' => $customer?->id?:'',
            'customer_name' => $customer?->name?:'',
            'customer_phone' => $customer?->mobile?:'',
            'customer_email' => $customer?->email?:'',
            'customer_score' => $quizResult?->answerer_score?:0,
            'app_name' => $from_name,
        ];

        //quizResult customer activities
        //Notification to quizResult submittion
        if(isset($customer)) {
            if($customer?->email) {
                $to = $customer->email; 

                // First message send when customer registration a service 
                if($quizResult?->status === 'success') {
                    if(isset($to)) {
                        $subject = $this->getDynamicContent($varialbes, $this->getQuizSubmissionEmailTemplate(true));
                        $messageBody = $this->getDynamicContent($varialbes,$this->getQuizSubmissionEmailTemplate(false));
                        SendEmailNotification::dispatch($to, $from, $from_name, $subject, $messageBody);
                    }
                }

            }

            //It's disabled right now. Please don't remove it.
            // if($customer?->mobile) {
            //     //Send message/SMS to customer mobile           
            //     $messageData = [ 'text' => "messageBody"];
            //     SendMobileMessage::dispatch($customer?->mobile, $messageData);
            // }
        }

    }

    /**
     * Handle the Quiz Submission email template.
     * @return string
     */
    private function getQuizSubmissionEmailTemplate($isEmailSubject){
        if($isEmailSubject) {
            return 'Togumogu Quiz Paticipation on {{quiz_name}}';
        } else {
            return  <<<HTML
                <html>
                    <head>
                        <title>Togumogu Quiz Paticipation on {{quiz_name}}</title>
                        <style type="text/css">
                            body {
                                font-family: Arial;
                            }
                        </style>
                    </head>

                    <body>
                        <div style="max-width: 800px; padding: 20px 0; margin: auto; font-size: 16px;line-height: 25px;">
                            <!-- Your other HTML content here -->
                            <span>Dear {{participant_name}},</span>
                            <p>Thank you very much for participating in the quiz. The winner will be selected by lottery from among those who answered all the questions correctly in the quiz,</p>
                            <div>Participant Information: </div>
                            <div>Name: <strong>{{customer_name}}</strong></div>
                            <div>Phone: <strong>{{customer_phone}}</strong></div>
                            <div>Email: <strong>{{customer_email}}</strong></div>
                            <div>Your Score: <strong>{{customer_score}}</strong></div>
                            <div>Total Correct answer: <strong>{{customer_total_correct_answer}}</strong></div>
                            <div>Total Wrong answer: <strong>{{customer_total_wrong_answer}}</strong></div>

                            <p style="margin-bottom: 5px;">FAQ:</p>
                            <ol style="margin-top: 10px;">
                                <li style='margin-bottom:5px'>
                                    <div>How a winner is selected ?</div>
                                    <div>Answer: Top scorers will be put into a lottery syatem for selecting winners.</div>
                                </li>
                                <li style='margin-bottom:5px'>
                                    <div>How will I know if I win a quiz event ?</div>
                                    <div>Answer: Togumogu team will contact you directly to inform you about your winnings</div>
                                </li>
                                <li style='margin-bottom:5px'>
                                    <div>How am I going to receive my prize ?</div>
                                    <div>Answer: Togumogu team will contact you about your delivery address.</div>
                                </li>
                            </ol>
                            <p>Thank you for participating and starting your thoughts. Thank you for using ToguMogu to connect with parents and provide your valuable services.
                            </p>

                            <div>Best regards,</div>
                            <div>{{app_name}}</div>

                        </div>
                    </body>

                    </html>
                HTML;
            }
       }

}
