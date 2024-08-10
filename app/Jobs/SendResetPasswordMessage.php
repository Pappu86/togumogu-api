<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendResetPasswordMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $customer;
    protected $password;

    /**
     * Create a new job instance.
     *
     * @param $customer
     * @param $password
     */
    public function __construct($customer, $password)
    {
        $this->customer = $customer;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
           
            $text = $this->password.' is your current PIN. Now you can use it for login. We suggest you change your PIN from settings for better security - ToguMogu';
        
            $res = Http::get(config('helper.boom_cast_sms_endpoint'), [
                'userName' => config('helper.boom_cast_sms_username'),
                'password' => config('helper.boom_cast_sms_password'),
                'MsgType' => config('helper.boom_cast_sms_type'),
                'masking' => 'NOMASK',
                'message' => $text,
                'receiver' => $this->customer->mobile,
            ]);
    
            if ($res->successful()) {
                if (config('helper.ssl_sms_is_localhost')) {
                    Log::channel('sms')->info('OTP: ' . $res->body());
                }
            }

            // $text = $this->convertBengaliToUnicode('আপনার পাসওয়ার্ড সফল ভাবে পরিবর্তিত হয়েছে। দয়াকরে আপনার পাসওয়ার্ডটি কারো সাথে শেয়ার করবেন না। হেল্পলাইন: 16273');
            // $res = Http::get(config('helper.ssl_sms_endpoint'), [
            //     'user' => config('helper.ssl_sms_username'),
            //     'pass' => config('helper.ssl_sms_password'),
            //     'sid' => config('helper.ssl_sms_sid'),
            //     'sms' => $text,
            //     'msisdn' => $this->customer['mobile'],
            //     'csmsid' => $this->customer['id'],
            // ]);
            // if (config('helper.ssl_sms_is_localhost')) {
            //     Log::channel('sms')->info('RESET: ' . $res->body());
            // }

        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    /**
     * @param $text
     * @return string
     */
    private function convertBengaliToUnicode($text)
    {
        return strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $text)));
    }
}
