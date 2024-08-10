<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWelcomeMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $customer;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param $customer
     */
    public function __construct($customer, $message)
    {
        $this->customer = $customer;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $text = $this->message;
     
            $res = Http::get(config('helper.boom_cast_sms_endpoint'), [
                'userName' => config('helper.boom_cast_sms_username'),
                'password' => config('helper.boom_cast_sms_password'),
                'MsgType' => config('helper.boom_cast_sms_type'),
                'masking' => 'NOMASK',
                'message' => $text,
                'receiver' => $this->customer?->mobile,
            ]);
    
            if ($res->successful()) {
                if (config('helper.ssl_sms_is_localhost')) {
                    Log::channel('sms')->info('OTP: ' . $res->body());
                }
            }
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
