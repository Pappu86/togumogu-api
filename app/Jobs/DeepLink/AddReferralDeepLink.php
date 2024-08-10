<?php

namespace App\Jobs\DeepLink;

use App\Models\Reward\Referral;
use App\Traits\CommonHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddReferralDeepLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue,
     Queueable, SerializesModels;

    /**
     * @var
     */
    protected $referral;
    protected $uid;
    protected $type;
    protected $customer_id;
    protected $partnership_id;
    protected $reference_id;

    public function __construct($data)
    {
        $commentHelpers = new CommonHelpers;

        $this->referral = $data;
        $this->customer_id = $data?->referral_type ==='customer'?$data?->id:'';
        $this->partnership_id = $data?->referral_type ==='partnership'?$data?->id:'';
        $this->reference_id = $data?->reference_id?:null;
        $this->uid = $commentHelpers->quickRandom(6);
        $this->type = $data?->referral_type;

        $referral = Referral::where('uid', $this->uid);
        if($referral->count()){
            $this->uid = $commentHelpers->quickRandom(6);
        };
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $url = '/referral?uid='.$this->uid;

        if(isset($this->type)) {
            $url = $url."&type=".$this->type;
        }

        $response = Http::asJson()->post('https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . config('helper.firebase_api_key'), [
            'dynamicLinkInfo' => [
                'domainUriPrefix' => config('helper.app_url'),
                'link' => config('helper.url') . "$url",
                'androidInfo' => [
                    'androidPackageName' => 'com.togumogu',
                ],
                'iosInfo'=> [
                    'iosBundleId'=> 'com.togumogu-pvt-limited.togumogu'
                ],
            ]
        ]);

        if ($response->successful()) {
            $body = $response->json();

            $shortLink = $body['shortLink'];
            $previewLink = $body['previewLink'];

            $data = [
                'uid' => $this->uid,
                'type' => $this->type,
                'url' => $url,
                'dynamic_url' => $shortLink,
                'preview_url' => $previewLink,
            ];
            
            if($this->type === 'customer'){
                $data['customer_id'] = $this->customer_id;
            } else {
                $data['partnership_id'] = $this->partnership_id;
            }

            //Added reference id
            if(isset($this->reference_id)){
                $data['reference_id'] = $this->reference_id;
            }

            Referral::create($data);

        } else {
            Log::error('dynamicLinkErrorArticle: ' . $response->body());
        }
    }

}
