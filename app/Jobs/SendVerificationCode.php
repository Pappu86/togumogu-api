<?php

namespace App\Jobs;

use App\Models\User\MessageCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendVerificationCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var MessageCode
     */
    protected MessageCode $code;

    /**
     * Create a new job instance.
     *
     * @param MessageCode $code
     */
    public function __construct(MessageCode $code)
    {
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $text = $this->code->code . ' is your ToguMogu OTP Code. Please use it within 2 minutes.';
        
        $res = Http::get(config('helper.boom_cast_sms_endpoint'), [
            'userName' => config('helper.boom_cast_sms_username'),
            'password' => config('helper.boom_cast_sms_password'),
            'MsgType' => config('helper.boom_cast_sms_type'),
            'masking' => 'NOMASK',
            'message' => $text,
            'receiver' => $this->code->mobile,
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
