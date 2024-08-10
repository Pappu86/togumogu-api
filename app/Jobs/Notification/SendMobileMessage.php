<?php

namespace App\Jobs\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendMobileMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $data;
    protected $mobile;

    /**
     * Create a new job instance.
     *
     * @param $customer
     * @param $data
     */
    public function __construct($mobile, $data)
    {
        $this->mobile = $mobile;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $res = Http::get(config('helper.boom_cast_sms_endpoint'), [
            'userName' => config('helper.boom_cast_sms_username'),
            'password' => config('helper.boom_cast_sms_password'),
            'MsgType' => config('helper.boom_cast_sms_type'),
            'masking' => 'NOMASK',
            'message' => $this->data['text'],
            'receiver' => $this->mobile,
        ]);

        if ($res->successful()) {
            if (config('helper.ssl_sms_is_localhost')) {
                Log::channel('sms')->info('OTP: ' . $res->body());
            }
        }
    }

    /**
     * @param $text
     * @return string
     */
    private function convertBengaliToUnicode($text): string
    {
        return mb_strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $text)));
    }
}
