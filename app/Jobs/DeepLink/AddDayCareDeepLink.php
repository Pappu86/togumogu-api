<?php

namespace App\Jobs\DeepLink;

use App\Models\Daycare\Daycare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddDayCareDeepLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Daycare
     */
    protected Daycare $daycare;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Daycare $daycare)
    {
        $this->daycare = $daycare;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = config('helper.url') . '/daycare-preschool-kindergarten/' . $this->daycare->slug;
        $title = strip_tags($this->daycare->meta_title ?? $this->daycare->name);
        $description = mb_substr(strip_tags($this->daycare->meta_description ?? $this->daycare->description), 0, 300);
        $image = strip_tags($this->daycare->meta_image ?? $this->daycare->image);

        $response = Http::asJson()->post('https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . config('helper.firebase_api_key'), [
            'dynamicLinkInfo' => [
                'domainUriPrefix' => config('helper.app_url'),
                'link' => $url,
                'androidInfo' => [
                    'androidPackageName' => 'com.togumogu',
                ],
                'iosInfo'=> [
                    'iosBundleId'=> 'com.togumogu-pvt-limited.togumogu'
                ],
                'socialMetaTagInfo' => [
                    'socialTitle' => $title,
                    'socialDescription' => $description,
                    'socialImageLink' => $image,
                ]
            ]
        ]);
        if ($response->successful()) {
            $body = $response->json();

            $shortLink = $body['shortLink'];
            $previewLink = $body['previewLink'];

            $this->daycare->update([
                'longLink' => config('helper.app_url') . '/?link=' . $url . '&apn=com.togumogu&st=' . urlencode($title) . '&sd=' . urlencode($description) . '&si=' . $image,
                'shortLink' => $shortLink,
                'previewLink' => $previewLink,
            ]);
        } else {
            Log::error('dynamicLinkErrorDayCare: ' . $response->body());
        }
    }
}
